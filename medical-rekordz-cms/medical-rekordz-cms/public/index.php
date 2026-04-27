<?php

// ─── 1. Session name (must be set before session_start) ───────────────────────
session_name('mrekordz_cms');

// ─── 2. Session start + secure cookie params ──────────────────────────────────
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ─── 3. Base path constant ────────────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));

// ─── 4. Load application config ───────────────────────────────────────────────
require BASE_PATH . '/config/app.php';
require BASE_PATH . '/config/superadmin.php';

// ─── 5. Error handling (reads APP_ENV from config) ────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ─── 6. PSR-4 Autoloader ──────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $namespaceMap = [
        'Core\\'                       => BASE_PATH . '/core/',
        'App\\Models\\'                => BASE_PATH . '/app/models/',
        'App\\Controllers\\Admin\\'      => BASE_PATH . '/app/controllers/admin/',
        'App\\Controllers\\Superadmin\\' => BASE_PATH . '/app/controllers/superadmin/',
        'App\\Controllers\\'             => BASE_PATH . '/app/controllers/public/',
    ];

    foreach ($namespaceMap as $prefix => $baseDir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file     = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// ─── 7. Global helpers (functions — not autoloaded) ──────────────────────────
require BASE_PATH . '/core/Helpers.php';

// ─── 8. Base URL constant ─────────────────────────────────────────────────────
define('BASE_URL', rtrim(APP_BASE_URL, '/'));

// ─── 9. Boot ──────────────────────────────────────────────────────────────────
(new Core\App())->run();
