<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;

class SliderController extends AdminController
{
    public function index(): string
    {
        return $this->adminView('admin/sliders/index', [
            'sliders'       => Database::fetchAll('SELECT * FROM sliders ORDER BY sort_order, id'),
            'title'         => 'Home Sliders',
            'activeSection' => 'sliders',
        ]);
    }

    public function create(): string
    {
        return $this->adminView('admin/sliders/form', [
            'item'          => null,
            'title'         => 'Add Slider',
            'activeSection' => 'sliders',
        ]);
    }

    public function store(): string
    {
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/sliders/create');
        }

        try {
            $data['image'] = Uploader::image('image', 'sliders');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data);
            return $this->redirect('/admin/sliders/create');
        }

        if (empty($data['image'])) {
            Session::flash('error', 'Please choose a slider image.');
            with_input($data);
            return $this->redirect('/admin/sliders/create');
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        Database::insert('sliders', $data);
        Session::flash('success', 'Slider created.');
        return $this->redirect('/admin/sliders');
    }

    public function edit(string $id): string
    {
        $item = Database::fetch('SELECT * FROM sliders WHERE id = ?', [(int) $id]);
        if (!$item) {
            return $this->redirect('/admin/sliders');
        }
        return $this->adminView('admin/sliders/form', [
            'item'          => $item,
            'title'         => 'Edit Slider',
            'activeSection' => 'sliders',
        ]);
    }

    public function update(string $id): string
    {
        $item = Database::fetch('SELECT * FROM sliders WHERE id = ?', [(int) $id]);
        if (!$item) {
            return $this->redirect('/admin/sliders');
        }

        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/sliders/' . (int) $id . '/edit');
        }

        try {
            $data['image'] = Uploader::image('image', 'sliders', $item['image']);
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data);
            return $this->redirect('/admin/sliders/' . (int) $id . '/edit');
        }

        Database::update('sliders', $data, (int) $id);
        Session::flash('success', 'Slider updated.');
        return $this->redirect('/admin/sliders');
    }

    public function destroy(string $id): string
    {
        $item = Database::fetch('SELECT * FROM sliders WHERE id = ?', [(int) $id]);
        if ($item) {
            Uploader::delete($item['image']);
            Database::delete('sliders', (int) $id);
        }
        Session::flash('success', 'Slider deleted.');
        return $this->redirect('/admin/sliders');
    }

    private function validate(): array
    {
        $v = new Validator($_POST);
        $v->required('title', 'Title')->max('title', 150, 'Title')
          ->max('subtitle', 255, 'Subtitle')
          ->max('button_text', 60, 'Button text')
          ->max('button_url', 255, 'Button URL');

        $data = $v->validated();
        unset($data['image']);
        $data['active'] = isset($_POST['active']) ? 1 : 0;
        $data['sort_order'] = (int) ($_POST['sort_order'] ?? 0);

        if ($v->fails()) {
            with_input($data, $v->errors());
            return [$data, false];
        }
        return [$data, true];
    }
}
