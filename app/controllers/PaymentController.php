<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Payment;

class PaymentController extends Controller
{
    /** Payment page: student info form on one side, school QR code on the other. */
    public function index(): string
    {
        return $this->view('pages/payment', [
            'errors' => Session::getFlash('errors', []),
            'old'    => Session::getFlash('old', []),
        ]);
    }

    public function store(): string
    {
        // Honeypot: hidden field that only bots fill in.
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            Session::flash('success', 'Thank you! Your payment details have been submitted.');
            return $this->redirect('/payment');
        }

        $v = new Validator($_POST);
        $v->required('student_name', 'Student name')
          ->max('student_name', 150, 'Student name')
          ->required('class', 'Class')
          ->max('class', 60, 'Class')
          ->required('roll_no', 'Roll no')
          ->max('roll_no', 30, 'Roll no')
          ->required('phone', 'Phone')
          ->max('phone', 40, 'Phone')
          ->max('payment_for', 120, 'Payment for')
          ->max('amount', 15, 'Amount')
          ->max('transaction_ref', 80, 'Transaction reference');

        $amount = str_replace([',', ' '], '', (string) ($_POST['amount'] ?? ''));
        if ($amount !== '' && !is_numeric($amount)) {
            $v->addError('amount', 'Amount must be a number.');
        }

        if ($v->fails()) {
            with_input($v->validated(), $v->errors());
            return $this->redirect('/payment');
        }

        $data = $v->validated();

        // Optional payment voucher: screenshot of the UPI receipt or a PDF.
        $voucher = null;
        try {
            $voucher = Uploader::voucher('voucher', 'payments');
        } catch (\RuntimeException $e) {
            with_input($v->validated(), ['voucher' => [$e->getMessage()]]);
            return $this->redirect('/payment');
        }

        Payment::create([
            'student_name'    => $data['student_name'],
            'class'           => $data['class'],
            'roll_no'         => $data['roll_no'],
            'phone'           => $data['phone'],
            'payment_for'     => $data['payment_for'] ?? null,
            'amount'          => $amount !== '' ? number_format((float) $amount, 2, '.', '') : null,
            'payment_method'  => 'qr',
            'transaction_ref' => $data['transaction_ref'] ?? null,
            'voucher'         => $voucher,
            'status'          => 'pending',
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Thank you! Your payment details have been submitted. The school office will verify your payment shortly.');
        return $this->redirect('/payment');
    }
}
