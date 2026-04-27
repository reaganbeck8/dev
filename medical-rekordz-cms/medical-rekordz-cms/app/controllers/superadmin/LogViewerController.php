<?php

namespace App\Controllers\Superadmin;

use Core\Controller;

class LogViewerController extends Controller
{
    public function index(): void
    {
        $logFile = STORAGE_PATH . '/logs/app.log';
        $lines   = [];

        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $lines   = array_filter(explode("\n", $content));
            $lines   = array_reverse($lines); // Newest first
            $lines   = array_slice($lines, 0, 200); // Last 200 lines
        }

        $this->view('superadmin/logs/index', [
            'title'   => 'Log Viewer',
            'lines'   => $lines,
            'logSize' => file_exists($logFile) ? filesize($logFile) : 0,
        ]);
    }

    public function clear(): void
    {
        $logFile = STORAGE_PATH . '/logs/app.log';
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }

        $this->session->flash('success', 'Log cleared.');
        $this->redirect('/' . SUPERADMIN_SLUG . '/logs');
    }
}
