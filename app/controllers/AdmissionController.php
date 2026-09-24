<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Validator;
use App\Models\AdmissionEnquiry;

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
          ->max('previous_class', 60, 'Previous class')
          ->max('emis_no', 30, 'EMIS number')
          ->max('message', 5000, 'Message');

        if ($v->fails()) {
            with_input($v->validated(), $v->errors());
            return $this->redirect('/admissions#apply');
        }

        $data = $v->validated();

        // Date of birth: accept either the A.D. picker value or a B.S. date,
        // converting B.S. → A.D. server-side (never trust JS-only conversion).
        $dob = trim((string) ($_POST['dob'] ?? ''));
        $dobBsRaw = trim((string) ($_POST['dob_bs'] ?? ''));
        $dobBsRaw = strtr($dobBsRaw, [
            '०' => '0', '१' => '1', '२' => '2', '३' => '3', '४' => '4',
            '५' => '5', '६' => '6', '७' => '7', '८' => '8', '९' => '9',
        ]);

        $dobDate = $dob !== '' ? date('Y-m-d', strtotime($dob)) : null;
        $dobNpLabel = null;

        if ($dobDate === null && preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $dobBsRaw, $bm)) {
            $bs = \App\Core\NepaliCalendar::bsToAd((int) $bm[1], (int) $bm[2], (int) $bm[3]);
            if ($bs !== null) {
                $dobDate = sprintf('%04d-%02d-%02d', $bs['year'], $bs['month'], $bs['day']);
            }
        }

        // Keep the Devanagari label when a valid B.S. date was provided.
        if (preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $dobBsRaw, $bm2)) {
            $check = \App\Core\NepaliCalendar::bsToAd((int) $bm2[1], (int) $bm2[2], (int) $bm2[3]);
            if ($check !== null) {
                $dobNpLabel = \App\Core\NepaliCalendar::formatBsNp([
                    'year' => (int) $bm2[1], 'month' => (int) $bm2[2], 'day' => (int) $bm2[3],
                ]);
            }
        }

        AdmissionEnquiry::create([
            'student_name'    => $data['student_name'],
            'dob'             => $dobDate,
            'dob_bs_label'    => $dobNpLabel,
            'gender'          => $data['gender'] ?? null,
            'grade_applying'  => $data['grade_applying'],
            'parent_name'     => $data['parent_name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'address'         => $data['address'] ?? null,
            'previous_school' => $data['previous_school'] ?? null,
            'previous_class'  => $data['previous_class'] ?? null,
            'emis_no'         => $data['emis_no'] ?? null,
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
                    'Date of birth'    => trim(($dobDate ? date('d M Y', strtotime($dobDate)) : '')
                                        . ($dobNpLabel ? ' (' . $dobNpLabel . ')' : '')) ?: null,
                    'Gender'           => $data['gender'] ?? null,
                    'Parent/Guardian'  => $data['parent_name'],
                    'Phone'            => $data['phone'],
                    'Email'            => $data['email'],
                    'Previous school'  => $data['previous_school'] ?? null,
                    'Previous class'   => $data['previous_class'] ?? null,
                    'EMIS number'      => $data['emis_no'] ?? null,
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
