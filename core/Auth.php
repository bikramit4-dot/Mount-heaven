<?php

namespace App\Core;

class Auth
{
    private static ?array $user = null;

    public static function user(): ?array
    {
        if (self::$user === null && Session::has('user_id')) {
            try {
                self::$user = Database::fetch(
                    'SELECT * FROM users WHERE id = ? AND active = 1',
                    [(int) Session::get('user_id')]
                );
            } catch (\Throwable $e) {
                self::$user = null;
            }
            if (!self::$user) {
                self::clear();
            }
        }
        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'admin';
    }

    /**
     * Attempt a login. Rate limited: max 5 tries per 10 minutes
     * for the same IP + username combination.
     */
    public static function attempt(string $login, string $password): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // housekeeping: drop attempts older than one day
        try {
            Database::run('DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)');
        } catch (\Throwable $e) {
            // ignore
        }

        $recent = Database::fetch(
            'SELECT COUNT(*) AS c FROM login_attempts
             WHERE ip = ? AND username = ? AND attempted_at > (NOW() - INTERVAL 10 MINUTE)',
            [$ip, $login]
        );
        if ((int) ($recent['c'] ?? 0) >= 5) {
            return false;
        }

        Database::insert('login_attempts', [
            'ip'           => $ip,
            'username'     => mb_substr($login, 0, 150),
            'attempted_at' => date('Y-m-d H:i:s'),
        ]);

        $user = Database::fetch(
            'SELECT * FROM users WHERE (username = ? OR email = ?) AND active = 1',
            [$login, $login]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            Database::update('users', ['password' => password_hash($password, PASSWORD_DEFAULT)], (int) $user['id']);
        }

        session_regenerate_id(true);
        Session::set('user_id', (int) $user['id']);
        Session::remove('_csrf'); // fresh token for the new session
        Database::update('users', ['last_login' => date('Y-m-d H:i:s')], (int) $user['id']);
        self::$user = $user;

        return true;
    }

    public static function logout(): void
    {
        self::clear();
        Session::destroy();
    }

    private static function clear(): void
    {
        self::$user = null;
        Session::remove('user_id');
    }
}
