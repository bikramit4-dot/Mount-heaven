<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;

class GalleryController extends AdminController
{
    public function index(): string
    {
        $albums = Database::fetchAll('SELECT * FROM gallery_albums ORDER BY sort_order, id');
        $photos = Database::fetchAll('SELECT * FROM gallery_photos ORDER BY sort_order, id');

        $byAlbum = [];
        foreach ($photos as $photo) {
            $byAlbum[(int) $photo['album_id']][] = $photo;
        }

        return $this->adminView('admin/gallery/index', [
            'albums'        => $albums,
            'byAlbum'       => $byAlbum,
            'title'         => 'Gallery',
            'activeSection' => 'gallery',
        ]);
    }

    public function storeAlbum(): string
    {
        $v = new Validator($_POST);
        $v->required('title', 'Album title')->max('title', 150, 'Album title');
        if ($v->fails()) {
            Session::flash('error', $v->errors()['title'][0]);
            return $this->redirect('/admin/gallery');
        }

        $cover = null;
        try {
            $cover = Uploader::image('cover', 'gallery');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/admin/gallery');
        }

        Database::insert('gallery_albums', [
            'title'      => trim((string) $_POST['title']),
            'cover'      => $cover,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Album created. You can now add photos to it.');
        return $this->redirect('/admin/gallery');
    }

    public function addPhotos(string $id): string
    {
        $album = Database::fetch('SELECT * FROM gallery_albums WHERE id = ?', [(int) $id]);
        if (!$album) {
            return $this->redirect('/admin/gallery');
        }

        if (empty($_FILES['photos']) || !is_array($_FILES['photos']['name'])) {
            Session::flash('error', 'Please choose at least one photo.');
            return $this->redirect('/admin/gallery');
        }

        $names = $_FILES['photos']['name'];
        $saved = 0;
        $failed = 0;

        // Re-key the multi-upload array into individual file structures.
        foreach ($names as $i => $name) {
            if (($_FILES['photos']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $_FILES['photos_single'] = [
                'name'     => $names[$i],
                'type'     => $_FILES['photos']['type'][$i],
                'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                'error'    => $_FILES['photos']['error'][$i],
                'size'     => $_FILES['photos']['size'][$i],
            ];
            try {
                $stored = Uploader::image('photos_single', 'gallery');
                if ($stored) {
                    Database::insert('gallery_photos', [
                        'album_id'   => (int) $id,
                        'image'      => $stored,
                        'caption'    => trim((string) ($_POST['caption'] ?? '')) ?: null,
                        'sort_order' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    if (!$album['cover']) {
                        Database::update('gallery_albums', ['cover' => $stored], (int) $id);
                        $album['cover'] = $stored;
                    }
                    $saved++;
                }
            } catch (\RuntimeException $e) {
                $failed++;
            }
        }
        unset($_FILES['photos_single']);

        if ($saved > 0) {
            Session::flash('success', "{$saved} photo(s) uploaded." . ($failed ? " {$failed} file(s) were skipped." : ''));
        } else {
            Session::flash('error', 'No photos could be uploaded. Check file types (JPG, PNG, GIF, WEBP) and size (max 5 MB).');
        }
        return $this->redirect('/admin/gallery');
    }

    public function destroyAlbum(string $id): string
    {
        $album = Database::fetch('SELECT * FROM gallery_albums WHERE id = ?', [(int) $id]);
        if ($album) {
            $photos = Database::fetchAll('SELECT image FROM gallery_photos WHERE album_id = ?', [(int) $id]);
            foreach ($photos as $p) {
                Uploader::delete($p['image']);
            }
            if ($album['cover']) {
                Uploader::delete($album['cover']);
            }
            Database::delete('gallery_albums', (int) $id); // photos cascade
        }
        Session::flash('success', 'Album and its photos deleted.');
        return $this->redirect('/admin/gallery');
    }

    public function destroyPhoto(string $id): string
    {
        $photo = Database::fetch('SELECT * FROM gallery_photos WHERE id = ?', [(int) $id]);
        if ($photo) {
            Uploader::delete($photo['image']);
            Database::delete('gallery_photos', (int) $id);
        }
        Session::flash('success', 'Photo deleted.');
        return $this->redirect('/admin/gallery');
    }
}
