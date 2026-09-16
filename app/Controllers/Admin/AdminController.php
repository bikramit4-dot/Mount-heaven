<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;

abstract class AdminController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            // remember where the user wanted to go
            Session::set('_intended_url', $_SERVER['REQUEST_URI'] ?? '/admin');
            header('Location: ' . url('/admin/login'));
            exit;
        }
    }

    /** Render the 403 page and stop. */
    protected function deny(): never
    {
        http_response_code(403);
        echo View::render('public/403', [], null);
        exit;
    }

    /** Render a view inside the admin layout with shared data. */
    protected function adminView(string $view, array $data = []): string
    {
        $unread = Database::fetch('SELECT COUNT(*) c FROM messages WHERE is_read = 0');
        $data['unreadCount'] = (int) ($unread['c'] ?? 0);
        $data['user'] = Auth::user();
        $data['tab'] = $data['tab'] ?? 'backup';
        return $this->view($view, $data, 'admin');
    }
}
