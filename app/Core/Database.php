<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $cfg = config('db');
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $cfg['host'],
                $cfg['port'],
                $cfg['name'],
                $cfg['charset']
            );
            try {
                self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                self::fail($e);
            }
        }
        return self::$pdo;
    }

    private static function fail(PDOException $e): void
    {
        if (config('app.debug')) {
            throw $e;
        }
        http_response_code(500);
        echo '<!doctype html><html><body style="font-family:sans-serif;text-align:center;padding:60px">'
            . '<h1>Database connection failed</h1>'
            . '<p>Please check <code>config/config.php</code> and make sure MySQL is running.</p>'
            . '</body></html>';
        exit;
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_map(static fn ($c) => '`' . str_replace('`', '', $c) . '`', array_keys($data));
        $ph   = implode(', ', array_fill(0, count($data), '?'));
        self::run(
            'INSERT INTO `' . str_replace('`', '', $table) . '` (' . implode(', ', $cols) . ") VALUES ($ph)",
            array_values($data)
        );
        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, int $id): void
    {
        $set = implode(', ', array_map(
            static fn ($c) => '`' . str_replace('`', '', $c) . '` = ?',
            array_keys($data)
        ));
        self::run(
            'UPDATE `' . str_replace('`', '', $table) . "` SET $set WHERE `id` = ?",
            [...array_values($data), $id]
        );
    }

    public static function delete(string $table, int $id): void
    {
        self::run('DELETE FROM `' . str_replace('`', '', $table) . '` WHERE `id` = ?', [$id]);
    }
}
