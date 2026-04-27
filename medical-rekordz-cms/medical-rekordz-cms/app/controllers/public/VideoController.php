<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\VideoModel;

class VideoController extends Controller
{
    public function index(): void
    {
        $videos = (new VideoModel())->getPublished();

        $this->view('public/videos/index', [
            'videos' => $videos,
            'title'  => 'Videos',
        ]);
    }
}
