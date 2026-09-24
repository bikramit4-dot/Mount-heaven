<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;

class AuthController extends Controller
{
    public function showLogin(): string
    {
        if (Auth::check()) {
            return $this->redirect('/admin');
        }
        return $this->view('admin/login', [], 'admin/auth');
    }

    public function login(): string
    {
        $v = new Validator($_POST);
        $v->required('login', 'Username or email')
          ->required('password', 'Password');

        if ($v->fails()) {
            Session::flash('error', 'Please enter both username and password.');
            return $this->redirect('/admin/login');
        }

        $login = trim((string) $_POST['login']);
        $password = (string) $_POST['password'];

        if (Auth::attempt($login, $password)) {
            $intended = Session::get('_intended_url');
            Session::remove('_intended_url');
            Session::flash('success', 'Welcome back, ' . (Auth::user()['name'] ?? '') . '!');
            return $this->redirect(
                is_string($intended) && str_starts_with($intended, '/admin') ? $intended : '/admin'
            );
        }

        Session::flash('error', 'Invalid credentials or too many failed attempts. Please try again in a few minutes.');
        return $this->redirect('/admin/login');
    }

    public function logout(): string
    {
        Auth::logout();
        Session::start();
        Session::flash('success', 'You have been logged out successfully.');
        return $this->redirect('/admin/login');
    }
}
