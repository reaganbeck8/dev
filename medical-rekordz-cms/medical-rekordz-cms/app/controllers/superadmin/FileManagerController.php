<?php

namespace App\Controllers\Superadmin;

use Core\Controller;

class FileManagerController extends Controller
{
    private const ALLOWED_ROOT = 'storage/uploads/';

    public function index(): void
    {
        $subdir = $_GET['dir'] ?? '';
        $path   = $this->resolvePath($subdir);

        if (!is_dir($path)) {
            $this->abort(404);
        }

        $items = $this->scanDirectory($path, $subdir);

        $this->view('superadmin/filemanager/index', [
            'title'      => 'File Manager',
            'items'      => $items,
            'currentDir' => $subdir,
            'parentDir'  => dirname($subdir) !== '.' ? dirname($subdir) : '',
        ]);
    }

    public function delete(): void
    {
        $file = $_POST['file'] ?? '';
        $path = $this->resolvePath($file);

        if (file_exists($path) && is_file($path)) {
            unlink($path);
            $this->session->flash('success', 'File deleted.');
        } else {
            $this->session->flash('error', 'File not found.');
        }

        $dir = dirname($file);
        $this->redirect('/' . SUPERADMIN_SLUG . '/filemanager?dir=' . urlencode($dir !== '.' ? $dir : ''));
    }

    private function resolvePath(string $subdir): string
    {
        $base = BASE_PATH . '/' . self::ALLOWED_ROOT;
        $full = realpath($base . $subdir) ?: $base . $subdir;

        // Prevent directory traversal
        if (!str_starts_with($full, realpath($base))) {
            $this->abort(403);
        }

        return $full;
    }

    private function scanDirectory(string $path, string $subdir): array
    {
        $items = [];
        $entries = scandir($path);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || $entry === '.gitkeep') {
                continue;
            }

            $fullPath = $path . '/' . $entry;
            $relPath  = $subdir ? $subdir . '/' . $entry : $entry;

            $items[] = [
                'name'     => $entry,
                'path'     => $relPath,
                'is_dir'   => is_dir($fullPath),
                'size'     => is_file($fullPath) ? filesize($fullPath) : 0,
                'modified' => filemtime($fullPath),
            ];
        }

        // Directories first, then files
        usort($items, function ($a, $b) {
            if ($a['is_dir'] !== $b['is_dir']) {
                return $b['is_dir'] - $a['is_dir'];
            }
            return strcasecmp($a['name'], $b['name']);
        });

        return $items;
    }
}
