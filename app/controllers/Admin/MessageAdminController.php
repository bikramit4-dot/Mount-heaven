<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Models\Message;

class MessageAdminController extends AdminController
{
    public function index(): string
    {
        return $this->adminView('admin/messages/index', [
            'messages'      => Message::inbox(),
            'title'         => 'Contact Messages',
            'activeSection' => 'messages',
        ]);
    }

    public function show(string $id): string
    {
        $message = Message::find((int) $id);
        if (!$message) {
            Session::flash('error', 'Message not found.');
            return $this->redirect('/admin/messages');
        }

        // Opening a message marks it as read.
        if (!$message['is_read']) {
            Database::update('messages', ['is_read' => 1], (int) $id);
            $message['is_read'] = 1;
        }

        return $this->adminView('admin/messages/show', [
            'message'       => $message,
            'title'         => 'Message — ' . $message['name'],
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
