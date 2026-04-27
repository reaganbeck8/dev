<?php

namespace Core;

class View
{
    // ─── Layout map per scope ─────────────────────────────────────────────────
    private const LAYOUTS = [
        'public'     => 'layouts/public',
        'admin'      => 'layouts/admin',
        'superadmin' => 'layouts/superadmin',
    ];

    // ─── Render a view wrapped in its layout ──────────────────────────────────
    public static function render(string $view, array $data = [], string $scope = 'public'): void
    {
        // Extract data into local scope for the view
        extract($data, EXTR_SKIP);

        // Capture the inner view content
        ob_start();
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Wrap in layout — layout echoes $content at its yield slot
        $layout     = self::LAYOUTS[$scope] ?? self::LAYOUTS['public'];
        $layoutFile = VIEW_PATH . '/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layoutFile}");
        }
        require $layoutFile;
    }

    // ─── Render a partial (no layout) ─────────────────────────────────────────
    public static function partial(string $partial, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $file = VIEW_PATH . '/partials/' . $partial . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("Partial not found: {$file}");
        }
        require $file;
    }

    // ─── Render view to string (email templates etc.) ────────────────────────
    public static function toString(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);

        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }

        ob_start();
        require $viewFile;
        return ob_get_clean();
    }
}
