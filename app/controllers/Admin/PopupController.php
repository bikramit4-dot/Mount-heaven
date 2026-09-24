<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;

/**
 * Website popup banner manager — the image that automatically shows when
 * someone opens the website and disappears after a few seconds.
 * Everything lives on ONE page: an add form on top, existing banners below.
 */
class PopupController extends AdminController
{
    public function index(): string
    {
        // ?edit=ID loads a banner into the form above (edit-in-place).
        $editId = (int) ($_GET['edit'] ?? 0);
        $edit = $editId
            ? Database::fetch('SELECT * FROM popup_banners WHERE id = ?', [$editId])
            : null;

        return $this->adminView('admin/popup/index', [
            'banners'       => Database::fetchAll('SELECT * FROM popup_banners ORDER BY id DESC'),
            'edit'          => $edit,
            'title'         => 'Popup Banner',
            'activeSection' => 'popup',
        ]);
    }

    public function store(): string
    {
        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/popup');
        }

        try {
            $data['image'] = Uploader::image('image', 'popup');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data);
            return $this->redirect('/admin/popup');
        }

        if (empty($data['image'])) {
            Session::flash('error', 'Please choose an image for the popup.');
            with_input($data);
            return $this->redirect('/admin/popup');
        }

        // Only one popup at a time: deactivate the previous ones.
        if ($data['active']) {
            Database::run('UPDATE popup_banners SET active = 0');
        }

        $data['duration'] = max(2, min(30, (int) ($data['duration'] ?? 5)));
        Database::insert('popup_banners', $data);
        Session::flash('success', 'Popup banner added — it now shows on the website.');
        return $this->redirect('/admin/popup');
    }

    public function update(string $id): string
    {
        $item = Database::fetch('SELECT * FROM popup_banners WHERE id = ?', [(int) $id]);
        if (!$item) {
            return $this->redirect('/admin/popup');
        }

        [$data, $ok] = $this->validate();
        if (!$ok) {
            return $this->redirect('/admin/popup');
        }

        try {
            $data['image'] = Uploader::image('image', 'popup', $item['image']);
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            with_input($data);
            return $this->redirect('/admin/popup');
        }

        if (empty($data['image'])) {
            $data['image'] = $item['image'];
        }

        // Only one popup at a time.
        if ($data['active']) {
            Database::run('UPDATE popup_banners SET active = 0 WHERE id <> ?', [(int) $id]);
        }

        $data['duration'] = max(2, min(30, (int) ($data['duration'] ?? 5)));
        Database::update('popup_banners', $data, (int) $id);
        Session::flash('success', 'Popup banner updated.');
        return $this->redirect('/admin/popup');
    }

    public function toggle(string $id): string
    {
        $item = Database::fetch('SELECT * FROM popup_banners WHERE id = ?', [(int) $id]);
        if ($item) {
            if (!$item['active']) {
                Database::run('UPDATE popup_banners SET active = 0'); // only one active
                Database::run('UPDATE popup_banners SET active = 1 WHERE id = ?', [(int) $id]);
                Session::flash('success', 'Popup banner is now live on the website.');
            } else {
                Database::run('UPDATE popup_banners SET active = 0 WHERE id = ?', [(int) $id]);
                Session::flash('success', 'Popup banner hidden from the website.');
            }
        }
        return $this->redirect('/admin/popup');
    }

    public function destroy(string $id): string
    {
        $item = Database::fetch('SELECT * FROM popup_banners WHERE id = ?', [(int) $id]);
        if ($item) {
            Uploader::delete($item['image']);
            Database::delete('popup_banners', (int) $id);
        }
        Session::flash('success', 'Popup banner deleted.');
        return $this->redirect('/admin/popup');
    }

    private function validate(): array
    {
        $v = new Validator($_POST);
        $v->max('title', 150, 'Title')
          ->max('link_url', 255, 'Link URL');

        $data = $v->validated();
        unset($data['image']);
        $data['active'] = isset($_POST['active']) ? 1 : 0;
        $data['duration'] = (int) ($_POST['duration'] ?? 5);

        if ($v->fails()) {
            with_input($data, $v->errors());
            return [$data, false];
        }
        return [$data, true];
    }
}
