<?php

namespace App\Controllers\Superadmin;

use Core\Controller;
use Core\Database;

class BackupController extends Controller
{
    public function index(): void
    {
        $backupDir = STORAGE_PATH . '/backups/';
        $backups   = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'name'     => basename($file),
                    'size'     => filesize($file),
                    'modified' => filemtime($file),
                ];
            }
            usort($backups, fn($a, $b) => $b['modified'] - $a['modified']);
        }

        $this->view('superadmin/backup/index', [
            'title'   => 'Database Backups',
            'backups' => $backups,
        ]);
    }

    public function create(): void
    {
        $db     = Database::getInstance();
        $config = require CONFIG_PATH . '/database.php';

        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $filepath = STORAGE_PATH . '/backups/' . $filename;

        $cmd = sprintf(
            'mysqldump -h %s -u %s -p%s %s > %s 2>&1',
            escapeshellarg($config['DB_HOST']),
            escapeshellarg($config['DB_USER']),
            escapeshellarg($config['DB_PASS']),
            escapeshellarg($config['DB_NAME']),
            escapeshellarg($filepath)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode === 0 && file_exists($filepath) && filesize($filepath) > 0) {
            $this->session->flash('success', 'Backup created: ' . $filename);
        } else {
            $this->session->flash('error', 'Backup failed. Check server configuration.');
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        $this->redirect('/' . SUPERADMIN_SLUG . '/backup');
    }

    public function download(int $id): void
    {
        // ID is not used — filename comes from GET
        $filename = basename($_GET['file'] ?? '');
        $filepath = STORAGE_PATH . '/backups/' . $filename;

        if (!file_exists($filepath) || !str_ends_with($filename, '.sql')) {
            $this->abort(404);
        }

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    public function delete(): void
    {
        $filename = basename($_POST['file'] ?? '');
        $filepath = STORAGE_PATH . '/backups/' . $filename;

        if (file_exists($filepath) && str_ends_with($filename, '.sql')) {
            unlink($filepath);
            $this->session->flash('success', 'Backup deleted.');
        }

        $this->redirect('/' . SUPERADMIN_SLUG . '/backup');
    }
}
