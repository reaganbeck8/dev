<?php

namespace App\Controllers\Superadmin;

use Core\Controller;
use App\Models\UserModel;
use App\Models\MediaModel;
use App\Models\ActivityLogModel;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('superadmin/dashboard/index', [
            'title'          => 'Super Admin',
            'userCount'      => count((new UserModel())->getAll()),
            'mediaCount'     => (new MediaModel())->count(),
            'recentActivity' => (new ActivityLogModel())->getAll(20),
            'phpVersion'     => PHP_VERSION,
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        ]);
    }
}
