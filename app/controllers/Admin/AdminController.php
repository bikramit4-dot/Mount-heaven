<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Models\Message;

abstract class AdminController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            // Remember where the user wanted to go — as a ROUTE path (sub-folder
            // base stripped, query kept). AuthController redirects it back via
            // Controller::redirect(), which re-applies the base automatically.
            $uri  = $_SERVER['REQUEST_URI'] ?? '/admin';
            $path = parse_url($uri, PHP_URL_PATH) ?: '/';
            $base = app_base();
            if ($base !== '' && str_starts_with($path, $base)) {
                $path = substr($path, strlen($base));
            }
            $path = '/' . ltrim($path, '/');
            $query = parse_url($uri, PHP_URL_QUERY);
            Session::set('_intended_url', $path . ($query !== null && $query !== '' ? '?' . $query : ''));
            header('Location: ' . url('/admin/login'));
            exit;
        }
    }

    /** Render the 403 page and stop. */
    protected function deny(): never
    {
        http_response_code(403);
        echo View::render('pages/403', [], null);
        exit;
    }

    /** Render a view inside the admin layout with shared data. */
    protected function adminView(string $view, array $data = []): string
    {
        $data['unreadCount'] = Message::unreadCount();
        $data['user'] = Auth::user();
        $data['tab'] = $data['tab'] ?? 'backup';
        return $this->view($view, $data, 'admin');
    }
}
