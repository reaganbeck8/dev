<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\ArtistModel;
use App\Models\VideoModel;
use App\Models\SettingModel;

class HomeController extends Controller
{
    public function index(): void
    {
        $artists  = (new ArtistModel())->getActive();
        $videos   = (new VideoModel())->getPublished();
        $settings = (new SettingModel())->getGroup('general');

        $this->view('public/home/index', [
            'artists'  => $artists,
            'videos'   => $videos,
            'settings' => $settings,
            'title'    => $settings['site_name'] ?? 'Medical Rekordz',
        ]);
    }
}
