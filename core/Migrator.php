<?php

namespace App\Core;

use PDO;
use PDOException;
use Throwable;

/**
 * Runs pending database migrations exactly once.
 *
 * Migrations live in database/migrations/*.sql as plain SQL files. Applied
 * files are recorded in the `migrations` table, so dropping that table (or
 * adding new files) re-runs only what is needed. Failures are logged but do
 * not break the site — a migration that fails will simply be retried on the
 * next request.
 */
class Migrator
{
    public static function run(): void
    {
        try {
            // Ensure the tracking table exists (plain CREATE, guarded by IF NOT EXISTS).
            Database::run(
                'CREATE TABLE IF NOT EXISTS migrations (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(190) NOT NULL UNIQUE,
                    applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );

            $applied = array_column(
                Database::fetchAll('SELECT migration FROM migrations'),
                'migration'
            );

            $files = glob(BASE_PATH . '/database/migrations/*.sql') ?: [];
            sort($files);

            foreach ($files as $file) {
                $name = basename($file);
                if (in_array($name, $applied, true)) {
                    continue;
                }

                $sql = file_get_contents($file);
                if ($sql === false || trim($sql) === '') {
                    continue;
                }

                try {
                    foreach (preg_split('/;\s*[\r\n]+/', $sql) as $stmt) {
                        $stmt = trim($stmt);
                        if ($stmt !== '') {
                            Database::run($stmt);
                        }
                    }
                    Database::run('INSERT INTO migrations (migration) VALUES (?)', [$name]);
                } catch (PDOException $e) {
                    // MySQL 1060 = duplicate column: a fresh install already has
                    // the column via database/database.sql. Treat the migration as applied.
                    $errno = $e->errorInfo[1] ?? null;
                    if ($errno === 1060 || str_contains($e->getMessage(), 'Duplicate column')) {
                        try {
                            Database::run('INSERT IGNORE INTO migrations (migration) VALUES (?)', [$name]);
                        } catch (Throwable $ignored) {
                        }
                    } else {
                        error_log('[Migrator] ' . $name . ': ' . $e->getMessage());
                    }
                } catch (Throwable $e) {
                    error_log('[Migrator] ' . $name . ': ' . $e->getMessage());
                }
            }
        } catch (Throwable $e) {
            error_log('[Migrator] ' . $e->getMessage());
        }
    }
}
