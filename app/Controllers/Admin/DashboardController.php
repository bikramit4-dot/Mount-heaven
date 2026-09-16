<?php

namespace App\Controllers\Admin;

use App\Core\Database;

class DashboardController extends AdminController
{
    public function index(): string
    {
        $stats = [
            'notices'   => (int) Database::fetch('SELECT COUNT(*) c FROM notices')['c'],
            'events'    => (int) Database::fetch('SELECT COUNT(*) c FROM events')['c'],
            'teachers'  => (int) Database::fetch('SELECT COUNT(*) c FROM teachers')['c'],
            'photos'    => (int) Database::fetch('SELECT COUNT(*) c FROM gallery_photos')['c'],
            'sliders'   => (int) Database::fetch('SELECT COUNT(*) c FROM sliders')['c'],
            'messages'  => (int) Database::fetch('SELECT COUNT(*) c FROM messages')['c'],
        ];

        $latestMessages = Database::fetchAll('SELECT * FROM messages ORDER BY created_at DESC LIMIT 5');
        $latestNotices  = Database::fetchAll('SELECT * FROM notices ORDER BY published_at DESC LIMIT 5');

        return $this->adminView('admin/dashboard', [
            'stats'          => $stats,
            'latestMessages' => $latestMessages,
            'latestNotices'  => $latestNotices,
            'title'          => 'Dashboard',
            'activeSection'  => 'dashboard',
        ]);
    }
}
