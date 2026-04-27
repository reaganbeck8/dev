<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ContactModel;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('admin/contact/index', [
            'title'       => 'Contact Submissions',
            'submissions' => (new ContactModel())->getAll(),
        ]);
    }

    public function show(int $id): void
    {
        $model      = new ContactModel();
        $submission = $model->findById($id);

        if (!$submission) {
            $this->abort(404);
        }

        // Mark as read on view
        if (!$submission['is_read']) {
            $model->markRead($id);
        }

        $this->view('admin/contact/show', [
            'title'      => 'Message from ' . $submission['name'],
            'submission' => $submission,
        ]);
    }

    public function delete(int $id): void
    {
        (new ContactModel())->delete($id);
        $this->session->flash('success', 'Message deleted.');
        $this->redirect('/admin/contact');
    }
}
