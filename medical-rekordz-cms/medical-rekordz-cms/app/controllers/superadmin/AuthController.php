<?php

namespace App\Controllers\Superadmin;

use Core\Controller;

class AuthController extends Controller
{
    public function login(): void
    {
        if ($this->auth->isSuperAdmin()) {
            $this->redirect('/' . SUPERADMIN_SLUG);
        }

        $this->view('superadmin/auth/login', [
            'title' => 'Access',
        ]);
    }

    public function attempt(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->session->flash('error', 'Credentials required.');
            $this->redirect('/' . SUPERADMIN_SLUG . '/login');
        }

        $user = $this->auth->attempt($email, $password, 'superadmin');

        if (!$user) {
            $this->session->flash('error', 'Access denied.');
            $this->redirect('/' . SUPERADMIN_SLUG . '/login');
        }

        $this->auth->login($user, 'superadmin');
        $this->redirect('/' . SUPERADMIN_SLUG);
    }

    public function logout(): void
    {
        $this->auth->logout('superadmin');
        $this->redirect('/');
    }
}
