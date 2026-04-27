<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\MediaModel;
use App\Models\ActivityLogModel;

class MediaController extends Controller
{
    public function index(): void
    {
        $type  = $_GET['type'] ?? null;
        $model = new MediaModel();

        $this->view('admin/media/index', [
            'title' => 'Media Library',
            'media' => $model->getAll($type),
            'type'  => $type,
        ]);
    }

    public function upload(): void
    {
        $this->view('admin/media/upload', [
            'title' => 'Upload Media',
        ]);
    }

    public function store(): void
    {
        $fileType = $_POST['file_type'] ?? 'image';

        if (empty($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
            $this->session->flash('error', 'Please select a file to upload.');
            $this->redirect('/admin/media/upload');
        }

        try {
            $id = (new MediaModel())->upload($_FILES['file'], $fileType, $this->currentUser()['id']);
            (new ActivityLogModel())->record('uploaded_media', $this->currentUser()['id'], 'media', $id);
            $this->session->flash('success', 'File uploaded successfully.');
        } catch (\RuntimeException $e) {
            $this->session->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/media');
    }

    public function delete(int $id): void
    {
        (new MediaModel())->delete($id);
        (new ActivityLogModel())->record('deleted_media', $this->currentUser()['id'], 'media', $id);
        $this->session->flash('success', 'File deleted.');
        $this->redirect('/admin/media');
    }
}
