<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;

class NoticeController extends AdminController
{
    public function index(): string
    {
        return $this->adminView('admin/notices/index', [
            'notices'       => Database::fetchAll('SELECT * FROM notices ORDER BY is_pinned DESC, published_at DESC'),
            'title'         => 'Notices',
            'activeSection' => 'notices',
        ]);
    }

    public function create(): string
    {
        return $this->adminView('admin/notices/form', [
            'item'          => null,
            'title'         => 'Add Notice',
            'activeSection' => 'notices',
        ]);
    }

    public function store(): string
    {
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/notices/create');
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        Database::insert('notices', $data);
        Session::flash('success', 'Notice published.');
        return $this->redirect('/admin/notices');
    }

    public function edit(string $id): string
    {
        $item = Database::fetch('SELECT * FROM notices WHERE id = ?', [(int) $id]);
        if (!$item) {
            return $this->redirect('/admin/notices');
        }
        return $this->adminView('admin/notices/form', [
            'item'          => $item,
            'title'         => 'Edit Notice',
            'activeSection' => 'notices',
        ]);
    }

    public function update(string $id): string
    {
        $item = Database::fetch('SELECT * FROM notices WHERE id = ?', [(int) $id]);
        if (!$item) {
            return $this->redirect('/admin/notices');
        }
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/notices/' . (int) $id . '/edit');
        }
        Database::update('notices', $data, (int) $id);
        Session::flash('success', 'Notice updated.');
        return $this->redirect('/admin/notices');
    }

    public function destroy(string $id): string
    {
        Database::delete('notices', (int) $id);
        Session::flash('success', 'Notice deleted.');
        return $this->redirect('/admin/notices');
    }

    private function validate(): array
    {
        $v = new Validator($_POST);
        $v->required('title', 'Title')->max('title', 200, 'Title')
          ->required('body', 'Notice body');

        $data = $v->validated();
        $data['is_pinned'] = isset($_POST['is_pinned']) ? 1 : 0;
        $data['published_at'] = !empty($_POST['published_at'])
            ? date('Y-m-d H:i:s', strtotime((string) $_POST['published_at']))
            : date('Y-m-d H:i:s');

        if ($v->fails()) {
            with_input($data, $v->errors());
            return [$data, false];
        }
        return [$data, true];
    }
}
