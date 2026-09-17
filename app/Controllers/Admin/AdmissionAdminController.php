<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;

class AdmissionAdminController extends AdminController
{
    public function index(): string
    {
        $status = (string) ($_GET['status'] ?? 'all');
        $allowed = ['all', 'new', 'contacted', 'enrolled', 'closed'];

        $sql = 'SELECT * FROM admissions_enquiries';
        $params = [];
        if (in_array($status, $allowed, true) && $status !== 'all') {
            $sql .= ' WHERE status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY created_at DESC';

        return $this->adminView('admin/admissions/index', [
            'enquiries'     => Database::fetchAll($sql, $params),
            'status'        => $status,
            'newCount'      => (int) Database::fetch('SELECT COUNT(*) AS c FROM admissions_enquiries WHERE status = ?', ['new'])['c'],
            'title'         => 'Admission Enquiries',
            'activeSection' => 'admissions',
        ]);
    }

    public function show(string $id): string
    {
        $enquiry = Database::fetch('SELECT * FROM admissions_enquiries WHERE id = ?', [(int) $id]);
        if (!$enquiry) {
            Session::flash('error', 'Enquiry not found.');
            return $this->redirect('/admin/admissions');
        }

        return $this->adminView('admin/admissions/show', [
            'enquiry'       => $enquiry,
            'title'         => 'Enquiry — ' . $enquiry['student_name'],
            'activeSection' => 'admissions',
        ]);
    }

    public function status(string $id): string
    {
        $status = (string) ($_POST['status'] ?? '');
        $allowed = ['new', 'contacted', 'enrolled', 'closed'];

        if (in_array($status, $allowed, true)) {
            Database::run('UPDATE admissions_enquiries SET status = ? WHERE id = ?', [$status, (int) $id]);
            Session::flash('success', 'Enquiry status updated.');
        }
        return $this->redirect('/admin/admissions');
    }

    public function destroy(string $id): string
    {
        Database::delete('admissions_enquiries', (int) $id);
        Session::flash('success', 'Enquiry deleted.');
        return $this->redirect('/admin/admissions');
    }
}
