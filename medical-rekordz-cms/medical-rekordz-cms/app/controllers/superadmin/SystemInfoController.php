<?php

namespace App\Controllers\Superadmin;

use Core\Controller;
use Core\Database;

class SystemInfoController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();

        $this->view('superadmin/system/index', [
            'title' => 'System Info',
            'info'  => [
                'PHP Version'      => PHP_VERSION,
                'Server Software'  => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'Document Root'    => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
                'OS'               => PHP_OS,
                'Max Upload'       => ini_get('upload_max_filesize'),
                'Post Max Size'    => ini_get('post_max_size'),
                'Memory Limit'     => ini_get('memory_limit'),
                'Max Execution'    => ini_get('max_execution_time') . 's',
                'PDO Drivers'      => implode(', ', \PDO::getAvailableDrivers()),
                'Session Handler'  => ini_get('session.save_handler'),
                'Disk Free Space'  => humanFileSize((int) disk_free_space(BASE_PATH)),
            ],
            'extensions' => get_loaded_extensions(),
        ]);
    }
}
