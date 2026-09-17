<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

class HomeController extends Controller
{
    public function home(): string
    {
        return $this->view('public/home', [
            'sliders'   => Database::fetchAll('SELECT * FROM sliders WHERE active = 1 ORDER BY sort_order, id'),
            'notices'   => Database::fetchAll('SELECT * FROM notices ORDER BY is_pinned DESC, published_at DESC LIMIT 4'),
            'events'    => Database::fetchAll('SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 3'),
            'programs'  => Database::fetchAll('SELECT * FROM programs WHERE active = 1 ORDER BY sort_order, id LIMIT 4'),
            'facilities'=> Database::fetchAll('SELECT * FROM facilities WHERE active = 1 ORDER BY sort_order, id LIMIT 6'),
            'popupBanner' => Database::fetch(
                'SELECT * FROM popup_banners WHERE active = 1 ORDER BY id DESC LIMIT 1'
            ),
        ]);
    }

    public function about(): string
    {
        return $this->view('public/about', [
            'teachers' => Database::fetchAll(
                'SELECT * FROM teachers WHERE active = 1 ORDER BY sort_order, id'
            ),
        ]);
    }

    public function academics(): string
    {
        return $this->view('public/academics', [
            'programs'   => Database::fetchAll('SELECT * FROM programs WHERE active = 1 ORDER BY sort_order, id'),
            'facilities' => Database::fetchAll('SELECT * FROM facilities WHERE active = 1 ORDER BY sort_order, id'),
        ]);
    }

    public function admissions(): string
    {
        return $this->view('public/admissions', [
            'errors' => Session::getFlash('errors', []),
            'old'    => Session::getFlash('old', []),
        ]);
    }

    public function gallery(): string
    {
        $albums = Database::fetchAll('SELECT * FROM gallery_albums ORDER BY sort_order, id');
        $photos = Database::fetchAll('SELECT * FROM gallery_photos ORDER BY sort_order, id');

        $byAlbum = [];
        foreach ($photos as $photo) {
            $byAlbum[(int) $photo['album_id']][] = $photo;
        }

        return $this->view('public/gallery', [
            'albums'  => $albums,
            'byAlbum' => $byAlbum,
        ]);
    }

    public function notices(): string
    {
        return $this->view('public/notices', [
            'notices' => Database::fetchAll('SELECT * FROM notices ORDER BY is_pinned DESC, published_at DESC'),
            'events'  => Database::fetchAll('SELECT * FROM events ORDER BY event_date DESC'),
        ]);
    }

    public function noticeDetail(string $id): string
    {
        $notice = Database::fetch('SELECT * FROM notices WHERE id = ?', [(int) $id]);
        if (!$notice) {
            return abort_page(404);
        }

        return $this->view('public/notice-detail', [
            'notice'  => $notice,
            'notices' => Database::fetchAll(
                'SELECT * FROM notices WHERE id <> ? ORDER BY is_pinned DESC, published_at DESC LIMIT 5',
                [(int) $id]
            ),
        ]);
    }

    public function eventDetail(string $id): string
    {
        $event = Database::fetch('SELECT * FROM events WHERE id = ?', [(int) $id]);
        if (!$event) {
            return abort_page(404);
        }

        return $this->view('public/event-detail', [
            'event'  => $event,
            'events' => Database::fetchAll(
                'SELECT * FROM events WHERE id <> ? AND event_date >= CURDATE() ORDER BY event_date LIMIT 5',
                [(int) $id]
            ),
        ]);
    }

    public function contact(): string
    {
        return $this->view('public/contact', [
            'errors' => Session::getFlash('errors', []),
            'old'    => Session::getFlash('old', []),
        ]);
    }
}
