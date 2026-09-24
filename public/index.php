<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Mount Heaven English School — front controller
|--------------------------------------------------------------------------
| Everything is bootstrapped here so that ONLY this file (plus public/)
| needs to be web-accessible. app/, core/, config/, routes/, database/
| and storage/ all stay out of the web root.
*/

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/core/helpers.php';
load_env(BASE_PATH . '/.env');

// Autoloader: App\Core\Foo -> core/Foo.php and every other App\X\Y
// (Models\Notice, Controllers\Admin\NoticeController, ...) -> app/X/Y.php
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $parts = explode('\\', substr($class, strlen($prefix)));
    $className = array_pop($parts);
    $directory = implode('/', array_map('strtolower', $parts));

    // Core framework classes live at the project root; everything else in app/.
    $file = $directory === 'core'
        ? BASE_PATH . '/core/' . $className . '.php'
        : BASE_PATH . '/app/' . $directory . '/' . $className . '.php';

    if (is_file($file)) {
        require $file;
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
    if (config('app.debug')) {
        // friendlyError() stays silent in debug mode on purpose — print the
        // exception details here so a debug-mode 500 is never a blank page.
        if (!headers_sent()) {
            http_response_code(500);
        }
        echo '<pre style="padding:20px;font:13px/1.5 monospace;background:#fff6f6;border:1px solid #e33;color:#900;white-space:pre-wrap;word-break:break-word">'
            . htmlspecialchars(get_class($e) . ': ' . $e->getMessage(), ENT_QUOTES, 'UTF-8') . "\n\n"
            . htmlspecialchars('in ' . $e->getFile() . ':' . $e->getLine(), ENT_QUOTES, 'UTF-8') . "\n\n"
            . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8')
            . '</pre>';
    }
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

// ---------- Output buffering (white-screen guard) ----------
// Buffers any accidental stray output (a stray space in an included file,
// a notice printed mid-request) so redirects never fail with
// "headers already sent" — which renders as a blank white page when
// APP_DEBUG is off. The buffer is flushed automatically at script end.
ob_start();

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
App\Core\Migrator::run();

$router = new App\Core\Router();
require BASE_PATH . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
