<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Uploader;

class SettingController extends AdminController
{
    /** Editable keys grouped by tab, with labels and input types. */
    public const GROUPS = [
        'general'   => ['General', [
            'site_name'       => ['School Name', 'text'],
            'site_tagline'    => ['Tagline', 'text'],
            'site_logo'       => ['School Logo (shows in header & topbar)', 'image'],
            'about_short'     => ['About Summary (short)', 'textarea'],
            'about_history'   => ['About / History (full)', 'textarea'],
            'about_mission'   => ['Mission Statement', 'textarea'],
            'about_vision'    => ['Vision Statement', 'textarea'],
            'footer_note'     => ['Footer Text ({year} = current year)', 'text'],
        ]],
        'principal' => ['Principal', [
            'principal_name'        => ['Principal Name', 'text'],
            'principal_designation' => ['Designation', 'text'],
            'principal_message'     => ['Principal Message', 'textarea'],
            'principal_photo'       => ['Principal Photo', 'image'],
        ]],
        'contact'   => ['Contact Info', [
            'address'      => ['Address', 'textarea'],
            'phone'        => ['Phone', 'text'],
            'email'        => ['Email', 'text'],
            'office_hours' => ['Office Hours', 'text'],
        ]],
        'social'    => ['Social & Admissions', [
            'facebook_url'   => ['Facebook URL', 'text'],
            'instagram_url'  => ['Instagram URL', 'text'],
            'youtube_url'    => ['YouTube URL', 'text'],
            'admission_open' => ['Admissions Open? (1 = yes, 0 = no)', 'text'],
            'admission_info' => ['Admission Info Text', 'textarea'],
        ]],
        'email'     => ['Email Notifications', [
            'notify_enabled' => ['Notifications On? (1 = yes, 0 = no)', 'text'],
            'notify_email'   => ['Send Notifications To (school email)', 'text'],
            'smtp_host'      => ['SMTP Host (e.g. smtp.gmail.com)', 'text'],
            'smtp_port'      => ['SMTP Port (587 or 465)', 'text'],
            'smtp_user'      => ['SMTP Username', 'text'],
            'smtp_pass'      => ['SMTP Password / App Password', 'password'],
            'smtp_from'      => ['From Email (usually same as username)', 'text'],
            'smtp_from_name' => ['From Name (e.g. Mount Heaven Website)', 'text'],
        ]],
    ];

    public function edit(): string
    {
        $rows = Database::fetchAll('SELECT `key`, `value` FROM settings');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }

        return $this->adminView('admin/settings', [
            'settings'      => $settings,
            'groups'        => self::GROUPS,
            'title'         => 'Site Settings',
            'activeSection' => 'settings',
        ]);
    }

    public function update(): string
    {
        foreach (self::GROUPS as $group) {
            foreach ($group[1] as $key => $meta) {
                if ($meta[1] === 'image') {
                    try {
                        $value = Uploader::image($key, 'settings', $_POST['_current_' . $key] ?? null);
                    } catch (\RuntimeException $e) {
                        Session::flash('error', $e->getMessage());
                        return $this->redirect('/admin/settings');
                    }
                    Database::run(
                        'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                        [$key, $value]
                    );
                    continue;
                }

                if (!array_key_exists($key, $_POST)) {
                    continue;
                }
                Database::run(
                    'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    [$key, trim((string) $_POST[$key])]
                );
            }
        }

        Session::flash('success', 'Settings saved successfully.');
        return $this->redirect('/admin/settings');
    }
}
