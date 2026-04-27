<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ArtistModel;
use App\Models\MediaModel;
use App\Models\ActivityLogModel;

class ArtistController extends Controller
{
    public function index(): void
    {
        $this->view('admin/artists/index', [
            'title'   => 'Artists',
            'artists' => (new ArtistModel())->getAll(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/artists/form', [
            'title'  => 'Add Artist',
            'artist' => null,
            'images' => (new MediaModel())->findByType('image'),
        ]);
    }

    public function store(): void
    {
        $model = new ArtistModel();
        $data  = $this->getPostData();

        $id = $model->create($data);

        if (!empty($_POST['socials'])) {
            $model->setSocials($id, $_POST['socials']);
        }

        (new ActivityLogModel())->record('created_artist', $this->currentUser()['id'], 'artist', $id);
        $this->session->flash('success', 'Artist created successfully.');
        $this->redirect('/admin/artists');
    }

    public function edit(int $id): void
    {
        $model  = new ArtistModel();
        $artist = $model->findById($id);

        if (!$artist) {
            $this->abort(404);
        }

        $this->view('admin/artists/form', [
            'title'   => 'Edit Artist',
            'artist'  => $artist,
            'socials' => $model->getSocials($id),
            'images'  => (new MediaModel())->findByType('image'),
        ]);
    }

    public function update(int $id): void
    {
        $model = new ArtistModel();
        $data  = $this->getPostData();

        $model->update($id, $data);

        if (isset($_POST['socials'])) {
            $model->setSocials($id, $_POST['socials']);
        }

        (new ActivityLogModel())->record('updated_artist', $this->currentUser()['id'], 'artist', $id);
        $this->session->flash('success', 'Artist updated successfully.');
        $this->redirect('/admin/artists');
    }

    public function delete(int $id): void
    {
        (new ArtistModel())->delete($id);
        (new ActivityLogModel())->record('deleted_artist', $this->currentUser()['id'], 'artist', $id);
        $this->session->flash('success', 'Artist deleted.');
        $this->redirect('/admin/artists');
    }

    private function getPostData(): array
    {
        return [
            'name'             => trim($_POST['name'] ?? ''),
            'slug'             => slug(trim($_POST['slug'] ?? $_POST['name'] ?? '')),
            'tagline'          => trim($_POST['tagline'] ?? '') ?: null,
            'bio'              => trim($_POST['bio'] ?? '') ?: null,
            'profile_image_id' => !empty($_POST['profile_image_id']) ? (int) $_POST['profile_image_id'] : null,
            'status'           => $_POST['status'] ?? 'active',
            'sort_order'       => (int) ($_POST['sort_order'] ?? 0),
        ];
    }
}
