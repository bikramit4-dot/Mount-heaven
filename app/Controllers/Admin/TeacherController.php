<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;

class TeacherController extends AdminController
{
    public function index(): string
    {
        return $this->adminView('admin/teachers/index', [
            'teachers'      => Database::fetchAll('SELECT * FROM teachers ORDER BY sort_order, id'),
            'title'         => 'Teachers',
            'activeSection' => 'teachers',
        ]);
    }

    public function create(): string
    {
        return $this->adminView('admin/teachers/form', [
            'item'          => null,
            'title'         => 'Add Teacher',
            'activeSection' => 'teachers',
        ]);
    }

    public function store(): string
    {
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/teachers/create');
        }
        try {
            $data['photo'] = Uploader::image('photo', 'teachers');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data);
            return $this->redirect('/admin/teachers/create');
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        Database::insert('teachers', $data);
        Session::flash('success', 'Teacher added.');
        return $this->redirect('/admin/teachers');
    }

    public function edit(string $id): string
    {
        $item = Database::fetch('SELECT * FROM teachers WHERE id = ?', [(int) $id]);
        if (!$item) {
            return $this->redirect('/admin/teachers');
        }
        return $this->adminView('admin/teachers/form', [
            'item'          => $item,
            'title'         => 'Edit Teacher',
            'activeSection' => 'teachers',
        ]);
    }

    public function update(string $id): string
    {
        $item = Database::fetch('SELECT * FROM teachers WHERE id = ?', [(int) $id]);
        if (!$item) {
            return $this->redirect('/admin/teachers');
        }
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/teachers/' . (int) $id . '/edit');
        }
        try {
            $data['photo'] = Uploader::image('photo', 'teachers', $item['photo']);
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data);
            return $this->redirect('/admin/teachers/' . (int) $id . '/edit');
        }
        Database::update('teachers', $data, (int) $id);
        Session::flash('success', 'Teacher updated.');
        return $this->redirect('/admin/teachers');
    }

    public function destroy(string $id): string
    {
        $item = Database::fetch('SELECT * FROM teachers WHERE id = ?', [(int) $id]);
        if ($item) {
            if ($item['photo']) {
                Uploader::delete($item['photo']);
            }
            Database::delete('teachers', (int) $id);
        }
        Session::flash('success', 'Teacher removed.');
        return $this->redirect('/admin/teachers');
    }

    private function validate(): array
    {
        $v = new Validator($_POST);
        $v->required('name', 'Name')->max('name', 120, 'Name')
          ->max('designation', 120, 'Designation')
          ->max('qualification', 150, 'Qualification');

        $data = $v->validated();
        unset($data['photo']);
        $data['active'] = isset($_POST['active']) ? 1 : 0;
        $data['sort_order'] = (int) ($_POST['sort_order'] ?? 0);

        if ($v->fails()) {
            with_input($data, $v->errors());
            return [$data, false];
        }
        return [$data, true];
    }
}
