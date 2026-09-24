<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\Event;
use App\Models\GalleryPhoto;
use App\Models\Message;
use App\Models\Notice;
use App\Models\Slider;
use App\Models\Teacher;

class DashboardController extends AdminController
{
    public function index(): string
    {
        $stats = [
            'notices'   => Notice::count(),
            'events'    => Event::count(),
            'teachers'  => Teacher::count(),
            'photos'    => GalleryPhoto::count(),
            'sliders'   => Slider::count(),
            'messages'  => Message::count(),
        ];

        $latestMessages = Message::latest(5);
        $latestNotices  = Notice::latestPublished(5);

        return $this->adminView('admin/dashboard', [
            'stats'          => $stats,
            'latestMessages' => $latestMessages,
            'latestNotices'  => $latestNotices,
            'title'          => 'Dashboard',
            'activeSection'  => 'dashboard',
        ]);
    }
}
