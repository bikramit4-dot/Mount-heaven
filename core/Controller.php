<?php

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'public'): string
    {
        return View::render($view, $data, $layout);
    }

    protected function json($data, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        return (string) json_encode($data);
    }

    protected function redirect(string $to): string
    {
        // Route paths ("/admin/login") must carry the app's sub-folder base
        // so redirects survive moving the project between hosts/folders.
        if ($to !== '' && $to[0] === '/') {
            $to = app_base() . $to;
        }
        return 'redirect:' . $to;
    }
}
