<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\ArtistModel;

class ArtistController extends Controller
{
    public function index(): void
    {
        $artists = (new ArtistModel())->getActive();

        $this->view('public/artists/index', [
            'artists' => $artists,
            'title'   => 'Artists',
        ]);
    }

    public function show(string $slug = ''): void
    {
        $model  = new ArtistModel();
        $artist = $model->findBySlug($slug);

        if (!$artist) {
            $this->abort(404);
        }

        $socials = $model->getSocials($artist['id']);

        $this->view('public/artists/show', [
            'artist'  => $artist,
            'socials' => $socials,
            'title'   => $artist['name'],
        ]);
    }
}
