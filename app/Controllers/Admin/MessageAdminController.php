<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;

class MessageAdminController extends AdminController
{
    public function index(): string
    {
        return $this->adminView('admin/messages/index', [
            'messages'      => Database::fetchAll('SELECT * FROM messages ORDER BY is_read ASC, created_at DESC'),
            'title'         => 'Contact Messages',
            'activeSection' => 'messages',
        ]);
    }

    public function markRead(string $id): string
    {
        Database::update('messages', ['is_read' => 1], (int) $id);
        Session::flash('success', 'Message marked as read.');
        return $this->redirect('/admin/messages');
    }

    public function markUnread(string $id): string
    {
        Database::update('messages', ['is_read' => 0], (int) $id);
        Session::flash('success', 'Message marked as unread.');
        return $this->redirect('/admin/messages');
    }

    public function destroy(string $id): string
    {
        Database::delete('messages', (int) $id);
        Session::flash('success', 'Message deleted.');
        return $this->redirect('/admin/messages');
    }
}
