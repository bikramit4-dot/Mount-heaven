<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Session;
use App\Models\Payment;

class PaymentAdminController extends AdminController
{
    public function index(): string
    {
        $status = (string) ($_GET['status'] ?? 'all');
        $allowed = ['all', 'pending', 'verified', 'failed'];

        $sql = 'SELECT * FROM payments';
        $params = [];
        if (in_array($status, $allowed, true) && $status !== 'all') {
            $sql .= ' WHERE status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY created_at DESC, id DESC';

        return $this->adminView('admin/payments/index', [
            'payments' => Database::fetchAll($sql, $params),
            'status'   => $status,
            'pendingCount' => Payment::pendingCount(),
            'title'        => 'Fee Payments',
            'activeSection' => 'payments',
        ]);
    }

    public function show(string $id): string
    {
        $payment = Payment::find((int) $id);
        if (!$payment) {
            Session::flash('error', 'Payment not found.');
            return $this->redirect('/admin/payments');
        }

        return $this->adminView('admin/payments/show', [
            'payment'       => $payment,
            'title'         => 'Payment — ' . $payment['student_name'],
            'activeSection' => 'payments',
        ]);
    }

    public function status(string $id): string
    {
        $status = (string) ($_POST['status'] ?? '');
        $allowed = ['pending', 'verified', 'failed'];

        if (in_array($status, $allowed, true)) {
            Database::run('UPDATE payments SET status = ? WHERE id = ?', [$status, (int) $id]);
            Session::flash('success', 'Payment status updated.');
        }
        return $this->redirect('/admin/payments');
    }

    public function destroy(string $id): string
    {
        Database::delete('payments', (int) $id);
        Session::flash('success', 'Payment record deleted.');
        return $this->redirect('/admin/payments');
    }
}
