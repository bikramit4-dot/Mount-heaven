<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;

class BackupController extends AdminController
{
    public function index(): string
    {
        $tab = ($_GET['tab'] ?? 'backup') === 'restore' ? 'restore' : 'backup';

        return $this->adminView('admin/backup/index', [
            'tab'           => $tab,
            'files'         => $this->files(),
            'dbSize'        => $this->dbSize(),
            'title'         => 'Backup & Restore',
            'activeSection' => 'backup',
        ]);
    }

    /** Create a new full database backup and store it in storage/backups. */
    public function create(): string
    {
        $sql = $this->dumpSql();
        $dir = (string) config('backup.dir');
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }

        $name = 'backup-' . date('Y-m-d_His') . '.sql';
        if (@file_put_contents($dir . '/' . $name, $sql) === false) {
            Session::flash('error', 'Could not write the backup file. Check folder permissions on storage/backups.');
            return $this->redirect('/admin/backup');
        }
        @chmod($dir . '/' . $name, 0640);

        Session::flash('success', 'Backup created successfully: ' . $name . ' (' . $this->humanSize(strlen($sql)) . ')');
        return $this->redirect('/admin/backup');
    }

    /** Download an existing backup file (admin only, path-traversal protected). */
    public function download(string $file): never
    {
        $path = $this->resolve($file);
        if ($path === null) {
            Session::flash('error', 'Backup file not found.');
            header('Location: ' . url('/admin/backup'));
            exit;
        }

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Length: ' . (string) filesize($path));
        header('Cache-Control: no-store');
        readfile($path);
        exit;
    }

    /** Restore the database from an uploaded .sql file or a stored backup. */
    public function restore(): string
    {
        $confirm = trim((string) ($_POST['confirm'] ?? ''));
        if ($confirm !== 'RESTORE') {
            Session::flash('error', 'Type RESTORE in the confirmation box to proceed.');
            return $this->redirect('/admin/backup?tab=restore');
        }

        $source = (string) ($_POST['source'] ?? 'upload');

        if ($source === 'existing') {
            $path = $this->resolve((string) ($_POST['file'] ?? ''));
            if ($path === null) {
                Session::flash('error', 'Selected backup file no longer exists.');
                return $this->redirect('/admin/backup?tab=restore');
            }
            $sql = (string) @file_get_contents($path);
        } else {
            if (empty($_FILES['sql_file']) || ($_FILES['sql_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                Session::flash('error', 'Please choose a .sql file to restore from.');
                return $this->redirect('/admin/backup?tab=restore');
            }
            $f = $_FILES['sql_file'];
            if (($f['size'] ?? 0) > (int) config('backup.max_upload')) {
                Session::flash('error', 'File is too large. Maximum ' . round((int) config('backup.max_upload') / 1048576) . ' MB.');
                return $this->redirect('/admin/backup?tab=restore');
            }
            if (strtolower(pathinfo((string) $f['name'], PATHINFO_EXTENSION)) !== 'sql') {
                Session::flash('error', 'Only .sql backup files can be restored.');
                return $this->redirect('/admin/backup?tab=restore');
            }
            $sql = (string) @file_get_contents($f['tmp_name']);
        }

        if (trim($sql) === '') {
            Session::flash('error', 'The backup file appears to be empty.');
            return $this->redirect('/admin/backup?tab=restore');
        }

        try {
            $statements = $this->splitSql($sql);
            $pdo = Database::pdo();
            $pdo->beginTransaction();
            foreach ($statements as $statement) {
                $pdo->exec($statement);
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            Session::flash('error', 'Restore failed: ' . $e->getMessage());
            return $this->redirect('/admin/backup?tab=restore');
        }

        Session::flash('success', 'Database restored successfully from ' . ($source === 'existing' ? 'stored backup' : 'uploaded file') . '.');
        return $this->redirect('/admin/backup?tab=restore');
    }

    /** Delete a stored backup. */
    public function destroy(string $file): string
    {
        $path = $this->resolve($file);
        if ($path !== null) {
            @unlink($path);
            Session::flash('success', 'Backup deleted.');
        } else {
            Session::flash('error', 'Backup file not found.');
        }
        return $this->redirect('/admin/backup');
    }

    // ================= internals =================

    /** List backup files, newest first. */
    private function files(): array
    {
        $dir = (string) config('backup.dir');
        if (!is_dir($dir)) {
            return [];
        }
        $files = glob($dir . '/*.sql') ?: [];
        rsort($files, SORT_STRING);

        return array_map(static function (string $path): array {
            return [
                'name' => basename($path),
                'size' => filesize($path),
                'date' => date('Y-m-d H:i', (int) filemtime($path)),
            ];
        }, $files);
    }

    /** Safely resolve a requested backup filename inside the backup dir. */
    private function resolve(string $file): ?string
    {
        if (!preg_match('/^[A-Za-z0-9._-]+\.sql$/', $file)) {
            return null;
        }
        $base = realpath((string) config('backup.dir'));
        $path = realpath((string) config('backup.dir') . '/' . $file);
        if (!$base || !$path || !str_starts_with($path, $base) || !is_file($path)) {
            return null;
        }
        return $path;
    }

    /** Generate a complete SQL dump of the current database. */
    private function dumpSql(): string
    {
        $cfg = config('db');
        $pdo = Database::pdo();
        $out = [];

        $out[] = '-- Mount Heaven English School — database backup';
        $out[] = '-- Generated: ' . date('Y-m-d H:i:s');
        $out[] = '-- Database: ' . $cfg['name'];
        $out[] = '';
        $out[] = 'SET NAMES utf8mb4;';
        $out[] = 'SET FOREIGN_KEY_CHECKS = 0;';
        $out[] = '';

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $create = $pdo->query('SHOW CREATE TABLE `' . $table . '`')->fetch(\PDO::FETCH_NUM);
            $out[] = '-- ----------------------------';
            $out[] = '-- Table: ' . $table;
            $out[] = '-- ----------------------------';
            $out[] = 'DROP TABLE IF EXISTS `' . $table . '`;';
            $out[] = $create[1] . ';';
            $out[] = '';

            $rows = $pdo->query('SELECT * FROM `' . $table . '`')->fetchAll(\PDO::FETCH_ASSOC);
            if (!$rows) {
                continue;
            }
            foreach (array_chunk($rows, 50) as $chunk) {
                $values = [];
                foreach ($chunk as $row) {
                    $parts = array_map(static function ($v) use ($pdo) {
                        return $v === null ? 'NULL' : $pdo->quote((string) $v);
                    }, array_values($row));
                    $values[] = '(' . implode(', ', $parts) . ')';
                }
                $cols = '`' . implode('`, `', array_keys($chunk[0])) . '`';
                $out[] = 'INSERT INTO `' . $table . '` (' . $cols . ') VALUES';
                $out[] = implode(",\n", $values) . ';';
                $out[] = '';
            }
        }

        $out[] = 'SET FOREIGN_KEY_CHECKS = 1;';
        $out[] = '';
        return implode("\n", $out);
    }

    /** Split an SQL dump into individual executable statements. */
    private function splitSql(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $inString = false;
        $stringChar = '';

        $length = strlen($sql);
        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $sql[$i + 1] ?? '';

            if (($char === '-' && $next === '-') || $char === '#') {
                while ($i < $length && $sql[$i] !== "\n") { $i++; }
                continue;
            }
            if ($char === '/' && $next === '*') {
                $i = strpos($sql, '*/', $i);
                if ($i === false) { break; }
                $i++;
                continue;
            }
            if ($inString) {
                $buffer .= $char;
                if ($char === '\\' && $next !== '') {
                    $buffer .= $next;
                    $i++;
                } elseif ($char === $stringChar) {
                    $inString = false;
                }
                continue;
            }
            if ($char === '"' || $char === "'") {
                $inString = true;
                $stringChar = $char;
                $buffer .= $char;
                continue;
            }
            if ($char === ';') {
                $stmt = trim($buffer);
                if ($stmt !== '') {
                    $statements[] = $stmt;
                }
                $buffer = '';
                continue;
            }
            $buffer .= $char;
        }

        $stmt = trim($buffer);
        if ($stmt !== '') {
            $statements[] = $stmt;
        }
        return $statements;
    }

    private function dbSize(): string
    {
        $row = Database::fetch(
            'SELECT SUM(data_length + index_length) AS s
             FROM information_schema.TABLES WHERE table_schema = ?',
            [config('db.name')]
        );
        return $this->humanSize((int) ($row['s'] ?? 0));
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
