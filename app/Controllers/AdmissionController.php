<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Validator;

class AdmissionController extends Controller
{
    public function store(): string
    {
        // Honeypot: hidden field that only bots fill in.
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            Session::flash('success', 'Thank you! Your admission enquiry has been submitted.');
            return $this->redirect('/admissions');
        }

        $v = new Validator($_POST);
        $v->required('student_name', 'Student name')->max('student_name', 150, 'Student name')
          ->required('grade_applying', 'Grade applying for')->max('grade_applying', 60, 'Grade applying for')
          ->required('parent_name', 'Parent/guardian name')->max('parent_name', 150, 'Parent/guardian name')
          ->required('email', 'Email')->email('email', 'Email')->max('email', 150, 'Email')
          ->required('phone', 'Phone')->max('phone', 40, 'Phone')
          ->max('gender', 20, 'Gender')
          ->max('address', 255, 'Address')
          ->max('previous_school', 200, 'Previous school')
          ->max('message', 5000, 'Message');

        if ($v->fails()) {
            with_input($v->validated(), $v->errors());
            return $this->redirect('/admissions#apply');
        }

        $data = $v->validated();

        $dob = trim((string) ($_POST['dob'] ?? ''));
        $dobDate = $dob !== '' ? date('Y-m-d', strtotime($dob)) : null;

        Database::insert('admissions_enquiries', [
            'student_name'    => $data['student_name'],
            'dob'             => $dobDate,
            'gender'          => $data['gender'] ?? null,
            'grade_applying'  => $data['grade_applying'],
            'parent_name'     => $data['parent_name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'address'         => $data['address'] ?? null,
            'previous_school' => $data['previous_school'] ?? null,
            'message'         => $data['message'] ?? null,
            'status'          => 'new',
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        // Email notification to the school (never blocks the submission).
        try {
            Mailer::notifySchool(
                'New Admission Enquiry — ' . $data['student_name'],
                '📋 New Admission Enquiry',
                [
                    'Student'          => $data['student_name'],
                    'Grade applying'   => $data['grade_applying'],
                    'Date of birth'    => $dobDate ? date('d M Y', strtotime($dobDate)) : null,
                    'Gender'           => $data['gender'] ?? null,
                    'Parent/Guardian'  => $data['parent_name'],
                    'Phone'            => $data['phone'],
                    'Email'            => $data['email'],
                    'Previous school'  => $data['previous_school'] ?? null,
                    'Address'          => $data['address'] ?? null,
                    'Message'          => $data['message'] ?? null,
                ]
            );
        } catch (\Throwable $e) {
            error_log('[Admission notify] ' . $e->getMessage());
        }

        Session::flash('success', 'Thank you! Your admission enquiry has been submitted. Our admissions team will contact you soon.');
        return $this->redirect('/admissions');
    }
}
