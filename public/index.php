<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Core/helpers.php';

// Autoloader: App\Core\Foo -> app/Core/Foo.php
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

date_default_timezone_set((string) config('app.timezone', 'UTC'));
error_reporting(E_ALL);
ini_set('display_errors', config('app.debug') ? '1' : '0');
ini_set('log_errors', '1');

// ---------- Global error handler (prevents blank white pages) ----------
// Any uncaught error/exception renders a friendly page instead of a white
// screen. Details are always written to the PHP error log; on-screen details
// appear only when APP_DEBUG is on.
$friendlyError = static function (string $logMessage): void {
    error_log($logMessage);
    if (!headers_sent()) {
        http_response_code(500);
    }
    if (config('app.debug')) {
        return; // let the normal debug output show
    }
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<title>Something went wrong</title></head>'
        . '<body style="font-family:sans-serif;text-align:center;padding:70px 20px;line-height:1.6">'
        . '<h1 style="font-size:1.6rem">Something went wrong</h1>'
        . '<p style="color:#555">Sorry, an unexpected error occurred. It has been logged and will be looked at.</p>'
        . '<p><a href="javascript:history.back()" style="color:#143d8f">&larr; Go back</a>'
        . ' &middot; <a href="/" style="color:#143d8f">Go to homepage</a></p>'
        . '</body></html>';
    exit;
};

set_exception_handler(static function (Throwable $e) use ($friendlyError): void {
    $friendlyError('Uncaught exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
});

set_error_handler(static function (int $severity, string $message, string $file, int $line) use ($friendlyError): bool {
    if (!(error_reporting() & $severity)) {
        return false; // respect the @ operator / configured level
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(static function () use ($friendlyError): void {
    $e = error_get_last();
    if ($e !== null && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        // Output has already started, so print a plain (non-blank) fallback.
        error_log('Fatal: ' . $e['message'] . ' in ' . $e['file'] . ':' . $e['line']);
        if (ob_get_length() !== false) {
            while (ob_get_level() > 0) { ob_end_clean(); }
        }
        if (!headers_sent()) {
            http_response_code(500);
        }
        if (!config('app.debug')) {
            echo '<!doctype html><html lang="en"><head><meta charset="utf-8">'
                . '<meta name="viewport" content="width=device-width, initial-scale=1">'
                . '<title>Something went wrong</title></head>'
                . '<body style="font-family:sans-serif;text-align:center;padding:70px 20px;line-height:1.6">'
                . '<h1 style="font-size:1.6rem">Something went wrong</h1>'
                . '<p style="color:#555">Sorry, an unexpected error occurred. It has been logged and will be looked at.</p>'
                . '<p><a href="javascript:history.back()" style="color:#143d8f">&larr; Go back</a>'
                . ' &middot; <a href="/" style="color:#143d8f">Go to homepage</a></p>'
                . '</body></html>';
        }
    }
});

// ---------- Security headers ----------
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

App\Core\Session::start();
App\Core\Installer::ensure();

$router = new App\Core\Router();
require BASE_PATH . '/app/routes.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
