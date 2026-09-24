<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Event;
use App\Models\Notice;

class HomeController extends Controller
{
    public function home(): string
    {
        return $this->view('pages/home', [
            'sliders'   => Database::fetchAll('SELECT * FROM sliders WHERE active = 1 ORDER BY sort_order, id'),
            'notices'   => Notice::latest(4),
            'events'    => Event::upcoming(3),
            'programs'  => Database::fetchAll('SELECT * FROM programs WHERE active = 1 ORDER BY sort_order, id LIMIT 4'),
            'facilities'=> Database::fetchAll('SELECT * FROM facilities WHERE active = 1 ORDER BY sort_order, id LIMIT 6'),
            'popupBanner' => Database::fetch(
                'SELECT * FROM popup_banners WHERE active = 1 ORDER BY id DESC LIMIT 1'
            ),
        ]);
    }

    public function about(): string
    {
        return $this->view('pages/about', [
            'teachers' => Database::fetchAll(
                'SELECT * FROM teachers WHERE active = 1 ORDER BY sort_order, id'
            ),
        ]);
    }

    public function academics(): string
    {
        return $this->view('pages/academics', [
            'programs'   => Database::fetchAll('SELECT * FROM programs WHERE active = 1 ORDER BY sort_order, id'),
            'facilities' => Database::fetchAll('SELECT * FROM facilities WHERE active = 1 ORDER BY sort_order, id'),
        ]);
    }

    public function admissions(): string
    {
        return $this->view('pages/admissions', [
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

        return $this->view('pages/gallery', [
            'albums'  => $albums,
            'byAlbum' => $byAlbum,
        ]);
    }

    public function notices(): string
    {
        // Calendar selection: ?cal=YYYY-M (BS year/month), or cal_y + cal_m from
        // the dropdown form. Defaults to today.
        $calY = 0;
        $calM = 0;
        if (preg_match('/^(\d{4})-(\d{1,2})$/', trim((string) ($_GET['cal'] ?? '')), $m)) {
            $calY = (int) $m[1];
            $calM = (int) $m[2];
        } else {
            $calY = (int) ($_GET['cal_y'] ?? 0);
            $calM = (int) ($_GET['cal_m'] ?? 0);
        }
        if ($calY < 2000 || $calY > 2090 || $calM < 1 || $calM > 12) {
            $todayBs = \App\Core\NepaliCalendar::adToBs(date('Y-m-d')) ?? ['year' => 2083, 'month' => 6];
            $calY = (int) $todayBs['year'];
            $calM = (int) $todayBs['month'];
        }

        // English calendar selection: ?adcal=YYYY-M, or adcal_y + adcal_m.
        $adY = 0;
        $adM = 0;
        if (preg_match('/^(\d{4})-(\d{1,2})$/', trim((string) ($_GET['adcal'] ?? '')), $m2)) {
            $adY = (int) $m2[1];
            $adM = (int) $m2[2];
        } else {
            $adY = (int) ($_GET['adcal_y'] ?? 0);
            $adM = (int) ($_GET['adcal_m'] ?? 0);
        }
        if ($adY < 1944 || $adY > 2033 || $adM < 1 || $adM > 12) {
            $adY = (int) date('Y');
            $adM = (int) date('n');
        }

        return $this->view('pages/notices', [
            'notices' => Notice::allOrdered(),
            'events'  => Event::allOrdered(),
            'cal'     => \App\Core\NepaliCalendar::monthGrid($calY, $calM),
            'calY'    => $calY,
            'calM'    => $calM,
            'adCal'   => \App\Core\NepaliCalendar::adMonthGrid($adY, $adM),
            'adY'     => $adY,
            'adM'     => $adM,
        ]);
    }

    public function noticeDetail(string $id): string
    {
        $notice = Notice::find((int) $id);
        if (!$notice) {
            return abort_page(404);
        }

        return $this->view('pages/notice-detail', [
            'notice'  => $notice,
            'notices' => Notice::related((int) $id, 5),
        ]);
    }

    public function eventDetail(string $id): string
    {
        $event = Event::find((int) $id);
        if (!$event) {
            return abort_page(404);
        }

        return $this->view('pages/event-detail', [
            'event'  => $event,
            'events' => Event::related((int) $id, 5),
        ]);
    }

    public function programDetail(string $id): string
    {
        $program = Database::fetch('SELECT * FROM programs WHERE id = ? AND active = 1', [(int) $id]);
        if (!$program) {
            return abort_page(404);
        }

        return $this->view('pages/program-detail', [
            'program'  => $program,
            'programs' => Database::fetchAll(
                'SELECT * FROM programs WHERE id <> ? AND active = 1 ORDER BY sort_order, id LIMIT 5',
                [(int) $id]
            ),
        ]);
    }

    public function facilityDetail(string $id): string
    {
        $facility = Database::fetch('SELECT * FROM facilities WHERE id = ? AND active = 1', [(int) $id]);
        if (!$facility) {
            return abort_page(404);
        }

        return $this->view('pages/facility-detail', [
            'facility'   => $facility,
            'facilities' => Database::fetchAll(
                'SELECT * FROM facilities WHERE id <> ? AND active = 1 ORDER BY sort_order, id LIMIT 5',
                [(int) $id]
            ),
        ]);
    }

    public function teacherDetail(string $id): string
    {
        $teacher = Database::fetch('SELECT * FROM teachers WHERE id = ? AND active = 1', [(int) $id]);
        if (!$teacher) {
            return abort_page(404);
        }

        return $this->view('pages/teacher-detail', [
            'teacher'  => $teacher,
            'teachers' => Database::fetchAll(
                'SELECT * FROM teachers WHERE id <> ? AND active = 1 ORDER BY sort_order, id LIMIT 5',
                [(int) $id]
            ),
        ]);
    }

    public function contact(): string
    {
        return $this->view('pages/contact', [
            'errors' => Session::getFlash('errors', []),
            'old'    => Session::getFlash('old', []),
        ]);
    }
}
