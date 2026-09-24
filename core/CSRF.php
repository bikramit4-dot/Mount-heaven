<?php

namespace App\Core;

class CSRF
{
    public static function token(): string
    {
        if (!Session::has('_csrf')) {
            Session::set('_csrf', bin2hex(random_bytes(32)));
        }
        return (string) Session::get('_csrf');
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function validate(): void
    {
        $sent = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!is_string($sent) || $sent === '' || !hash_equals(self::token(), $sent)) {
            http_response_code(419);
            echo '<!doctype html><html><body style="font-family:sans-serif;text-align:center;padding:60px">'
                . '<h1>419 — Session expired</h1>'
                . '<p>Your form session expired. Please <a href="javascript:history.back()">go back</a>, '
                . 'refresh the page and try again.</p>'
                . '</body></html>';
            exit;
        }
    }
}
