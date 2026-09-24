<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Core\Uploader;

class SettingController extends AdminController
{
    /**
     * Editable settings, grouped into tabs.
     * key => [Label, type, help text]
     * Types: text, textarea, image, password, bool (checkbox)
     */
    public const GROUPS = [
        'general' => [
            'title' => '🏷️ General',
            'intro' => 'School name, logo and the texts visitors see on the About section.',
            'fields' => [
                'site_name'       => ['School Name', 'text', 'Appears in the browser tab, header and footer of the website.'],
                'site_tagline'    => ['Tagline', 'text', 'Short slogan shown under the school name in the header.'],
                'site_logo'       => ['School Logo', 'image', 'Square image works best. Shows in the header, footer and admin panel.'],
                'about_short'     => ['About Summary (short)', 'textarea', 'A 2–3 line preview shown on the home page.'],
                'about_history'   => ['About / History (full)', 'textarea', 'Full history text on the About page.'],
                'about_mission'   => ['Mission Statement', 'textarea', 'Shown in the Mission & Vision box on the About page.'],
                'about_vision'    => ['Vision Statement', 'textarea', 'Shown in the Mission & Vision box on the About page.'],
                'footer_note'     => ['Footer Text', 'text', 'Closing line in the footer. Use {year} to auto-insert the current year.'],
            ],
        ],
        'principal' => [
            'title' => '👤 Principal',
            'intro' => 'The principal’s message shown on the About page.',
            'fields' => [
                'principal_name'        => ['Principal Name', 'text', 'Shown above the message on the About page.'],
                'principal_designation' => ['Designation', 'text', 'e.g. “Principal, Mount Heaven English School”.'],
                'principal_message'     => ['Principal Message', 'textarea', 'Full welcome message from the principal.'],
                'principal_photo'       => ['Principal Photo', 'image', 'A portrait photo works best (roughly square).'],
            ],
        ],
        'stats' => [
            'title' => '📊 School Stats',
            'intro' => 'The big numbers strip on the home page. Keep values short, e.g. “800+”.',
            'fields' => [
                'stat_established'     => ['Year Established', 'text', 'Big number, e.g. “Since 1959”.'],
                'stat_established_sub' => ['Established Sub-label', 'text', 'Small text under the number, e.g. “Years of excellence”.'],
                'stat_students'        => ['Total Students', 'text', 'Big number, e.g. “800+”.'],
                'stat_students_sub'    => ['Students Sub-label', 'text', 'Small text under the number, e.g. “Happy students”.'],
                'stat_teachers'        => ['Total Teachers', 'text', 'Big number, e.g. “35+”.'],
                'stat_teachers_sub'    => ['Teachers Sub-label', 'text', 'Small text under the number, e.g. “Expert teachers”.'],
                'stat_results'         => ['Board Results', 'text', 'Big number, e.g. “100%”.'],
                'stat_results_sub'     => ['Results Sub-label', 'text', 'Small text under the number, e.g. “Success rate”.'],
            ],
        ],
        'contact' => [
            'title' => '📞 Contact Info',
            'intro' => 'Shown in the top bar, Contact page and footer.',
            'fields' => [
                'address'      => ['Address', 'textarea', 'Full postal address of the school.'],
                'phone'        => ['Phone Number', 'text', 'Shown as a tappable link on mobile.'],
                'email'        => ['Email Address', 'text', 'Public email visitors can write to.'],
                'office_hours' => ['Office Hours', 'text', 'e.g. “Mon – Sat, 8:00 AM – 3:00 PM”.'],
                'map_location' => ['Map Search Location (optional)', 'text', 'What to show on the Contact-page map: the school name with area, a landmark, or coordinates like 28.5672, 77.2100. Leave empty to use the Address above.'],
                'map_embed'    => ['Google Maps Embed URL (optional)', 'url', 'Advanced: open Google Maps, search the school, click Share → Embed a map, copy the link inside src="…" and paste it here. Overrides the search location.'],
            ],
        ],
        'social' => [
            'title' => '🌐 Social & Admissions',
            'intro' => 'Social page links and the admissions status shown on the Admissions page.',
            'fields' => [
                'facebook_url'   => ['Facebook URL', 'url', 'Full link, e.g. https://facebook.com/yourschool'],
                'instagram_url'  => ['Instagram URL', 'url', 'Full link, e.g. https://instagram.com/yourschool'],
                'youtube_url'    => ['YouTube URL', 'url', 'Full link, e.g. https://youtube.com/@yourschool'],
                'admission_open' => ['Admissions Open', 'bool', 'Tick to show the green “Admissions Open” badge; untick to show “Closed”.'],
                'admission_info' => ['Admission Info Text', 'textarea', 'Extra text shown under the admissions badge.'],
            ],
        ],
        'email' => [
            'title' => '✉️ Email Notifications',
            'intro' => 'Get an email when someone submits the contact form or an admission enquiry.',
            'fields' => [
                'notify_enabled' => ['Enable Notifications', 'bool', 'Master switch for all email notifications.'],
                'notify_email'   => ['Send Notifications To', 'email', 'School inbox that receives the notifications.'],
                'smtp_host'      => ['SMTP Host', 'text', 'e.g. smtp.gmail.com'],
                'smtp_port'      => ['SMTP Port', 'number', 'Use 587 (STARTTLS) or 465 (SSL).'],
                'smtp_user'      => ['SMTP Username', 'text', 'Usually your full email address.'],
                'smtp_pass'      => ['SMTP Password / App Password', 'password', 'For Gmail, create an App Password in your Google account.'],
                'smtp_from'      => ['From Email', 'text', 'Address shown as sender — usually same as SMTP username.'],
                'smtp_from_name' => ['From Name', 'text', 'e.g. “Mount Heaven Website”.'],
            ],
        ],
    ];

    public function edit(): string
    {
        $tab = $this->currentTab();

        return $this->adminView('admin/settings', [
            'tab'           => $tab,
            'groups'        => self::GROUPS,
            'settings'      => $this->settings(),
            'title'         => 'Site Settings',
            'activeSection' => 'settings',
        ]);
    }

    /** Save only the fields of the tab the form was submitted from. */
    public function update(): string
    {
        $tab  = $this->currentTab();
        $meta = self::GROUPS[$tab]['fields'] ?? [];

        foreach ($meta as $key => [$label, $type]) {
            if ($type === 'image') {
                try {
                    $value = Uploader::image($key, 'settings', $_POST['_current_' . $key] ?? null);
                } catch (\RuntimeException $e) {
                    Session::flash('error', $e->getMessage());
                    return $this->redirect('/admin/settings?tab=' . $tab);
                }
                $this->save($key, $value);
                continue;
            }

            if ($type === 'bool') {
                // Unchecked checkboxes send nothing — normalise explicitly.
                $this->save($key, isset($_POST[$key]) ? '1' : '0');
                continue;
            }

            if (!array_key_exists($key, $_POST)) {
                continue;
            }
            $this->save($key, trim((string) $_POST[$key]));
        }

        Session::flash('success', ucfirst($tab) . ' settings saved successfully.');
        return $this->redirect('/admin/settings?tab=' . $tab);
    }

    private function currentTab(): string
    {
        $tab = (string) ($_GET['tab'] ?? $_POST['tab'] ?? 'general');

        return isset(self::GROUPS[$tab]) ? $tab : 'general';
    }

    private function settings(): array
    {
        $settings = [];
        foreach (Database::fetchAll('SELECT `key`, `value` FROM settings') as $row) {
            $settings[$row['key']] = $row['value'];
        }

        return $settings;
    }

    private function save(string $key, string $value): void
    {
        Database::run(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            [$key, $value]
        );
    }
}
