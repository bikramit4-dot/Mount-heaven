<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Message;

class MessageController extends Controller
{
    public function store(): string
    {
        // Honeypot: hidden field that only bots fill in.
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            Session::flash('success', 'Thank you! Your message has been sent.');
            return $this->redirect('/contact');
        }

        $v = new Validator($_POST);
        $v->required('name', 'Name')
          ->max('name', 120, 'Name')
          ->required('email', 'Email')
          ->email('email', 'Email')
          ->max('email', 150, 'Email')
          ->max('phone', 40, 'Phone')
          ->max('subject', 200, 'Subject')
          ->required('message', 'Message')
          ->max('message', 5000, 'Message');

        if ($v->fails()) {
            with_input($v->validated(), $v->errors());
            return $this->redirect('/contact');
        }

        $data = $v->validated();

        Message::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'subject'    => $data['subject'] ?? null,
            'message'    => $data['message'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Email notification to the school (never blocks the submission).
        try {
            Mailer::notifySchool(
                'New Contact Message — ' . ($data['subject'] ?: $data['name']),
                '💬 New Contact Message',
                [
                    'Name'    => $data['name'],
                    'Email'   => $data['email'],
                    'Phone'   => $data['phone'] ?? null,
                    'Subject' => $data['subject'] ?? null,
                    'Message' => $data['message'],
                ]
            );
        } catch (\Throwable $e) {
            error_log('[Contact notify] ' . $e->getMessage());
        }

        Session::flash('success', 'Thank you! Your message has been sent. We will get back to you soon.');
        return $this->redirect('/contact');
    }
}
