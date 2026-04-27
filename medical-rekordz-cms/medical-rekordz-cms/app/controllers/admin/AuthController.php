<?php

namespace App\Controllers\Admin;

use Core\Controller;

class AuthController extends Controller
{
    public function login(): void
    {
        if ($this->auth->isAdmin()) {
            $this->redirect('/admin');
        }

        $this->view('admin/auth/login', [
            'title' => 'Admin Login',
        ]);
    }

    public function attempt(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->session->flash('error', 'Please enter your email and password.');
            $this->redirect('/admin/login');
        }

        $user = $this->auth->attempt($email, $password, 'admin');

        if (!$user) {
            $this->session->flash('error', 'Invalid credentials or account locked.');
            $this->redirect('/admin/login');
        }

        $this->auth->login($user, 'admin');
        $this->redirect('/admin');
    }

    public function logout(): void
    {
        $this->auth->logout('admin');
        $this->session->flash('success', 'You have been logged out.');
        $this->redirect('/admin/login');
    }
}
