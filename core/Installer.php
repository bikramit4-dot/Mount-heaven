<?php

namespace App\Core;

use PDO;

class Installer
{
    public static function ensure(): void
    {
        try {
            Database::fetch('SELECT 1 FROM settings LIMIT 1');
            return; // already installed
        } catch (\Throwable $e) {
            // fall through to installation
        }

        self::install();
    }

    private static function install(): void
    {
        try {
            // 1. Connect without database and create it if needed.
            $cfg = config('db');
            $pdo = new PDO(
                "mysql:host={$cfg['host']};port={$cfg['port']};charset=utf8mb4",
                $cfg['user'],
                $cfg['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $name = str_replace('`', '', $cfg['name']);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}`
                        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$name}`");

            // 2. Run the schema + seeds.
            $schema = file_get_contents(BASE_PATH . '/database/database.sql');
            if ($schema === false) {
                throw new \RuntimeException('database/database.sql not found.');
            }
            $statements = preg_split('/;\s*[\r\n]+/', $schema);
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if ($stmt !== '') {
                    $pdo->exec($stmt);
                }
            }

            // 3. Create the default administrator if there are no users yet.
            $hasUsers = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
            if ($hasUsers === 0) {
                $st = $pdo->prepare(
                    'INSERT INTO users (name, username, email, password, role, active, created_at)
                     VALUES (?, ?, ?, ?, "admin", 1, NOW())'
                );
                $st->execute([
                    config('admin.name'),
                    config('admin.username'),
                    config('admin.email'),
                    password_hash(config('admin.password'), PASSWORD_DEFAULT),
                ]);
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                throw $e;
            }
            http_response_code(500);
            echo '<!doctype html><html><body style="font-family:sans-serif;max-width:640px;margin:80px auto;line-height:1.6">'
                . '<h1>Installation problem</h1>'
                . '<p>Could not create the database or import the schema.</p>'
                . '<ul><li>Check the MySQL credentials in <code>config/config.php</code></li>'
                . '<li>Make sure the MySQL server is running</li></ul>'
                . '<p><em>' . e($e->getMessage()) . '</em></p>'
                . '</body></html>';
            exit;
        }
    }
}
