<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ArtistModel;
use App\Models\PageModel;
use App\Models\VideoModel;
use App\Models\ContactModel;
use App\Models\MediaModel;
use App\Models\ActivityLogModel;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin/dashboard/index', [
            'title'          => 'Dashboard',
            'artistCount'    => count((new ArtistModel())->getAll()),
            'pageCount'      => count((new PageModel())->getAll()),
            'videoCount'     => count((new VideoModel())->getAll()),
            'unreadMessages' => (new ContactModel())->countUnread(),
            'mediaCount'     => (new MediaModel())->count(),
            'recentActivity' => (new ActivityLogModel())->getAll(10),
        ]);
    }
}
