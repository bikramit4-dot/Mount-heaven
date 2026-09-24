<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Notice;

class NoticeController extends AdminController
{
    public function index(): string
    {
        return $this->adminView('admin/notices/index', [
            'notices'       => Notice::allOrdered(),
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
        Notice::create($data);
        Session::flash('success', 'Notice published.');
        return $this->redirect('/admin/notices');
    }

    public function edit(string $id): string
    {
        $item = Notice::find((int) $id);
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
        $item = Notice::find((int) $id);
        if (!$item) {
            return $this->redirect('/admin/notices');
        }
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/notices/' . (int) $id . '/edit');
        }
        Notice::update((int) $id, $data);
        Session::flash('success', 'Notice updated.');
        return $this->redirect('/admin/notices');
    }

    public function destroy(string $id): string
    {
        Notice::remove((int) $id);
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
