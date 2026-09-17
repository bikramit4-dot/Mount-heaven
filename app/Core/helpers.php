<?php

use App\Core\Database;
use App\Core\Session;

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
     * Derived from the executed script's real location vs the document root —
     * NOT from the request path, which is unreliable under some servers.
     */
    function app_base(): string
    {
        static $base = null;
        if ($base !== null) {
            return $base;
        }
        $docRoot   = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? '')) ?: '';
        $scriptDir = realpath(dirname((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''))) ?: '';

        if ($docRoot !== '' && $docRoot !== '/' && $scriptDir !== '' && str_starts_with($scriptDir, $docRoot)) {
            $base = rtrim(substr($scriptDir, strlen($docRoot)), '/');
            return $base;
        }
        $base = '';
        return $base;
    }
}

if (!function_exists('public_base')) {
    /**
     * URL prefix for files inside public/ (assets, uploads).
     * Empty when the docroot IS the public folder; otherwise "/base/public".
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
     * (e.g. `/assets/css/style.css?v=1726500000`).
     */
    function asset(string $path): string
    {
        $file = BASE_PATH . '/public/assets/' . ltrim($path, '/');
        $version = is_file($file) ? filemtime($file) : '0';
        return public_base() . '/assets/' . ltrim($path, '/') . '?v=' . $version;
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
        return App\Core\View::render('public/' . ($code === 403 ? '403' : '404'), [
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
