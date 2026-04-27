<?php

namespace Core;

class App
{
    // ─── Boot and dispatch ────────────────────────────────────────────────────
    public function run(): void
    {
        $this->setCsrfToken();

        try {
            $router = new Router();
            $router->dispatch();
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    // ─── CSRF token generation ────────────────────────────────────────────────
    private function setCsrfToken(): void
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // ─── Top-level exception handler ──────────────────────────────────────────
    private function handleException(\Throwable $e): void
    {
        $this->logException($e);

        if (APP_DEBUG) {
            throw $e;
        }

        http_response_code(500);

        $view = VIEW_PATH . '/public/errors/500.php';
        if (file_exists($view)) {
            require $view;
        } else {
            echo 'Something went wrong. Please try again later.';
        }

        exit;
    }

    // ─── Exception logger ─────────────────────────────────────────────────────
    private function logException(\Throwable $e): void
    {
        $logFile = STORAGE_PATH . '/logs/app.log';
        $entry   = sprintf(
            '[%s] [EXCEPTION] %s in %s on line %d%s%s',
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            PHP_EOL,
            $e->getTraceAsString()
        ) . PHP_EOL;

        error_log($entry, 3, $logFile);
    }
}
