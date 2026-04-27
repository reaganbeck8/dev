<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\ContactModel;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('public/contact/index', [
            'title' => 'Contact',
        ]);
    }

    public function submit(): void
    {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Basic validation
        if ($name === '' || $email === '' || $message === '') {
            $this->session->flash('error', 'Please fill in all required fields.');
            $this->redirect('/contact');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->flash('error', 'Please enter a valid email address.');
            $this->redirect('/contact');
        }

        (new ContactModel())->save([
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone ?: null,
            'message'    => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $this->session->flash('success', 'Thank you for your message. We\'ll be in touch soon.');
        $this->redirect('/contact');
    }
}
