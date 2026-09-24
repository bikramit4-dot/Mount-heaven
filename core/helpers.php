<?php

use App\Core\Database;
use App\Core\Session;

if (!function_exists('load_env')) {
    /** Load simple KEY=VALUE entries from the project root .env file. */
    function load_env(string $file): void
    {
        if (!is_file($file) || !is_readable($file)) {
            return;
        }

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if ($name === '' || getenv($name) !== false) {
                continue;
            }

            if (strlen($value) >= 2) {
                $first = $value[0];
                $last = $value[strlen($value) - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        static $config = null;
        if ($config === null) {
            $config = require BASE_PATH . '/config/config.php';
        }
        $value = $config;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}

if (!function_exists('e')) {
    /** HTML-escape output (XSS protection). */
    function e($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('app_base')) {
    /**
     * Base URL of the app (works at domain root, /public, or a sub-folder).
     *
     * Order of precedence:
     *  1. APP_BASE_URL in .env (manual override — set it when auto-detection
     *     fails on a shared host, exactly like BASE_URL in the wellness app).
     *  2. Auto-detect from DOCUMENT_ROOT vs the executed script folder.
    *     The front controller lives at the project root, while public/ holds
    *     browser-facing files.
     */
    function app_base(): string
    {
        static $base = null;
        if ($base !== null) {
            return $base;
        }

        $override = getenv('APP_BASE_URL') ?: ($_ENV['APP_BASE_URL'] ?? '');
        if (is_string($override) && trim($override) !== '') {
            $base = '/' . trim(trim($override), '/');
            return $base; // '/' means domain root
        }

        $base = '';
        $docRoot   = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? '')) ?: '';
        $scriptDir = realpath(dirname((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''))) ?: '';

        if ($docRoot !== '' && $docRoot !== '/' && $scriptDir !== '' && str_starts_with($scriptDir, $docRoot)) {
            $base = rtrim(substr($scriptDir, strlen($docRoot)), '/');
        }

        // A trailing "/public" is usually an artifact of a web server using
        // the project root as its document root.
        // Exception: the project folder itself is named "public" and visitors
        // genuinely browse /public/... — then the prefix is real, keep it.
        if (str_ends_with($base, '/public')) {
            $uriPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '';
            if (!str_starts_with($uriPath, $base)) {
                $base = substr($base, 0, -strlen('/public'));
            }
        }

        return $base;
    }
}

if (!function_exists('public_base')) {
    /**
     * URL prefix for files inside public/ (assets, uploads).
     *
     * Empty when the docroot IS the public folder (Apache vhost pointing at
     * public/, or the built-in dev router); otherwise "/base/public".
     * APP_BASE_URL can override just the app part — e.g. "/school" + docroot
    * at the repo root → assets served from "/school/public/css/...".
     */
    function public_base(): string
    {
        static $base = null;
        if ($base !== null) {
            return $base;
        }
        $docRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? '')) ?: '';
        $base = ($docRoot !== '' && $docRoot === realpath(BASE_PATH . '/public'))
            ? ''
            : app_base() . '/public';
        return $base;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return app_base() . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * URL for a public asset, with a filemtime-based version string so
     * browsers always pick up updated CSS/JS instead of a stale cache
    * (e.g. `/css/style.css?v=1726500000`).
     */
    function asset(string $path): string
    {
        $file = BASE_PATH . '/public/' . ltrim($path, '/');
        $version = is_file($file) ? filemtime($file) : '0';
        return public_base() . '/' . ltrim($path, '/') . '?v=' . $version;
    }
}

if (!function_exists('upload_url')) {
    function upload_url(?string $path): string
    {
        if (!$path) {
            return '';
        }
        return public_base() . '/uploads/' . ltrim($path, '/');
    }
}

if (!function_exists('setting')) {
    /** Read a site setting (cached per request). */
    function setting(string $key, $default = '')
    {
        static $cache = null;
        if ($cache === null) {
            $cache = [];
            try {
                foreach (Database::fetchAll('SELECT `key`, `value` FROM settings') as $row) {
                    $cache[$row['key']] = $row['value'];
                }
            } catch (\Throwable $e) {
                // database not ready yet
            }
        }
        return $cache[$key] ?? $default;
    }
}

if (!function_exists('redirect')) {
    /** Return a redirect instruction for the router (base URL included). */
    function redirect(string $to): string
    {
        if ($to !== '' && $to[0] === '/') {
            $to = app_base() . $to;
        }
        return 'redirect:' . $to;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return App\Core\CSRF::field();
    }
}

if (!function_exists('flash')) {
    function flash(string $key, $default = null)
    {
        return Session::getFlash($key, $default);
    }
}

if (!function_exists('with_input')) {
    /** Keep submitted input + validation errors across a redirect. */
    function with_input(array $old, array $errors = []): void
    {
        Session::flash('old', $old);
        Session::flash('errors', $errors);
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = ''): string
    {
        $v = Session::get('_old_backup', null);
        unset($v);
        return '';
    }
}

if (!function_exists('error_for')) {
    function error_for(array $errors, string $field): string
    {
        return e($errors[$field][0] ?? '');
    }
}

if (!function_exists('abort_page')) {
    function abort_page(int $code, string $message = ''): string
    {
        http_response_code($code);
        return App\Core\View::render('pages/' . ($code === 403 ? '403' : '404'), [
            'message' => $message,
        ], 'public');
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date, string $format = 'M j, Y'): string
    {
        if (!$date) {
            return '';
        }
        $ts = strtotime($date);
        return $ts ? date($format, $ts) : '';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text !== '' ? substr($text, 0, 180) : 'item-' . time();
    }
}

if (!function_exists('str_limit')) {
    function str_limit(?string $value, int $limit = 120): string
    {
        $value = trim((string) $value);
        if (mb_strlen($value) <= $limit) {
            return $value;
        }
        return rtrim(mb_substr($value, 0, $limit)) . '…';
    }
}

if (!function_exists('current_path')) {
    /** Request path with the app base (and optional /public) removed. */
    function current_path(): string
    {
        $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = app_base();
        if ($base && str_starts_with($current, $base)) {
            $current = substr($current, strlen($base));
        }
        if (str_starts_with($current, '/public')) {
            $current = substr($current, 7);
        }
        return '/' . ltrim($current, '/');
    }
}

if (!function_exists('is_active_path')) {
    function is_active_path(string $path): bool
    {
        $current = current_path();
        if ($path === '/') {
            return $current === '/' || $current === '';
        }
        return str_starts_with($current, '/' . ltrim($path, '/'));
    }
}
