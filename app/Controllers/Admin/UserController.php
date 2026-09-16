<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;

class UserController extends AdminController
{
    public function index(): string
    {
        if (!Auth::isAdmin()) {
            $this->deny();
        }
        return $this->adminView('admin/users/index', [
            'users'         => Database::fetchAll('SELECT id, name, username, email, role, active, last_login, created_at FROM users ORDER BY id'),
            'title'         => 'Admin Users',
            'activeSection' => 'users',
        ]);
    }

    public function create(): string
    {
        if (!Auth::isAdmin()) {
            $this->deny();
        }
        return $this->adminView('admin/users/form', [
            'title'         => 'Add User',
            'activeSection' => 'users',
        ]);
    }

    public function store(): string
    {
        if (!Auth::isAdmin()) {
            $this->deny();
        }

        $v = new Validator($_POST);
        $v->required('name', 'Name')->max('name', 120, 'Name')
          ->required('username', 'Username')->max('username', 60, 'Username')
          ->required('email', 'Email')->email('email', 'Email')
          ->required('password', 'Password')->min('password', 8, 'Password')
          ->same('password_confirmation', 'password', 'Password');

        // unique checks
        if (Database::fetch('SELECT id FROM users WHERE username = ?', [trim((string) ($_POST['username'] ?? ''))])) {
            $_POST['username_dup'] = 'x';
            $v->required('username_dup', 'Username is already taken');
        }
        if (Database::fetch('SELECT id FROM users WHERE email = ?', [trim((string) ($_POST['email'] ?? ''))])) {
            $_POST['email_dup'] = 'x';
            $v->required('email_dup', 'Email is already registered');
        }

        if ($v->fails()) {
            with_input($v->validated(), $v->errors());
            return $this->redirect('/admin/users/create');
        }

        $role = ($_POST['role'] ?? 'editor') === 'admin' ? 'admin' : 'editor';

        Database::insert('users', [
            'name'       => trim((string) $_POST['name']),
            'username'   => trim((string) $_POST['username']),
            'email'      => trim((string) $_POST['email']),
            'password'   => password_hash((string) $_POST['password'], PASSWORD_DEFAULT),
            'role'       => $role,
            'active'     => isset($_POST['active']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'User created.');
        return $this->redirect('/admin/users');
    }

    public function destroy(string $id): string
    {
        if (!Auth::isAdmin()) {
            $this->deny();
        }

        $id = (int) $id;
        if ($id === (int) Auth::user()['id']) {
            Session::flash('error', 'You cannot delete your own account.');
            return $this->redirect('/admin/users');
        }

        $admins = Database::fetch("SELECT COUNT(*) c FROM users WHERE role = 'admin' AND active = 1");
        $target = Database::fetch("SELECT * FROM users WHERE id = ?", [$id]);
        if ($target && $target['role'] === 'admin' && (int) $admins['c'] <= 1) {
            Session::flash('error', 'At least one active admin must remain.');
            return $this->redirect('/admin/users');
        }

        Database::delete('users', $id);
        Session::flash('success', 'User deleted.');
        return $this->redirect('/admin/users');
    }

    // ---------------- Profile ----------------

    public function profile(): string
    {
        return $this->adminView('admin/users/profile', [
            'title'         => 'My Profile',
            'activeSection' => 'profile',
        ]);
    }

    public function updateProfile(): string
    {
        $userId = (int) Auth::user()['id'];

        $v = new Validator($_POST);
        $v->required('name', 'Name')->max('name', 120, 'Name')
          ->required('email', 'Email')->email('email', 'Email');

        // changing password?
        $newPassword = (string) ($_POST['new_password'] ?? '');
        if ($newPassword !== '') {
            $v->min('new_password', 8, 'New password')
              ->same('new_password_confirmation', 'new_password', 'New password');

            $current = (string) ($_POST['current_password'] ?? '');
            if (!password_verify($current, Auth::user()['password'])) {
                $_POST['current_wrong'] = 'x';
                $v->required('current_wrong', 'Current password is incorrect');
            }
        }

        if ($v->fails()) {
            with_input($v->validated(), $v->errors());
            return $this->redirect('/admin/profile');
        }

        $update = [
            'name'  => trim((string) $_POST['name']),
            'email' => trim((string) $_POST['email']),
        ];
        if ($newPassword !== '') {
            $update['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        Database::update('users', $update, $userId);
        Auth::logout();
        Session::start();
        Session::flash('success', 'Profile updated. Please sign in again with your new credentials.');
        return $this->redirect('/admin/login');
    }
}
