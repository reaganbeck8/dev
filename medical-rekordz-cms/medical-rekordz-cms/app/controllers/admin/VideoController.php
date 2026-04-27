<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\VideoModel;
use App\Models\MediaModel;
use App\Models\ActivityLogModel;

class VideoController extends Controller
{
    public function index(): void
    {
        $this->view('admin/videos/index', [
            'title'  => 'Videos',
            'videos' => (new VideoModel())->getAll(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/videos/form', [
            'title'  => 'Add Video',
            'video'  => null,
            'images' => (new MediaModel())->findByType('image'),
        ]);
    }

    public function store(): void
    {
        $data = $this->getPostData();
        $id   = (new VideoModel())->create($data);

        (new ActivityLogModel())->record('created_video', $this->currentUser()['id'], 'video', $id);
        $this->session->flash('success', 'Video added successfully.');
        $this->redirect('/admin/videos');
    }

    public function edit(int $id): void
    {
        $video = (new VideoModel())->findById($id);

        if (!$video) {
            $this->abort(404);
        }

        $this->view('admin/videos/form', [
            'title'  => 'Edit Video',
            'video'  => $video,
            'images' => (new MediaModel())->findByType('image'),
        ]);
    }

    public function update(int $id): void
    {
        (new VideoModel())->update($id, $this->getPostData());
        (new ActivityLogModel())->record('updated_video', $this->currentUser()['id'], 'video', $id);
        $this->session->flash('success', 'Video updated successfully.');
        $this->redirect('/admin/videos');
    }

    public function delete(int $id): void
    {
        (new VideoModel())->delete($id);
        (new ActivityLogModel())->record('deleted_video', $this->currentUser()['id'], 'video', $id);
        $this->session->flash('success', 'Video deleted.');
        $this->redirect('/admin/videos');
    }

    public function reorder(): void
    {
        $ids = $_POST['order'] ?? [];
        if (!empty($ids)) {
            (new VideoModel())->reorder($ids);
        }
        $this->json(['success' => true]);
    }

    private function getPostData(): array
    {
        return [
            'title'        => trim($_POST['title'] ?? ''),
            'description'  => trim($_POST['description'] ?? '') ?: null,
            'video_type'   => $_POST['video_type'] ?? 'youtube',
            'youtube_url'  => trim($_POST['youtube_url'] ?? '') ?: null,
            'media_id'     => !empty($_POST['media_id']) ? (int) $_POST['media_id'] : null,
            'thumbnail_id' => !empty($_POST['thumbnail_id']) ? (int) $_POST['thumbnail_id'] : null,
            'sort_order'   => (int) ($_POST['sort_order'] ?? 0),
            'status'       => $_POST['status'] ?? 'published',
        ];
    }
}
