<?php

// ─── Environment ──────────────────────────────────────────────────────────────
define('APP_ENV',   'development'); // 'development' or 'production'
define('APP_DEBUG',  APP_ENV === 'development');

// ─── Application ──────────────────────────────────────────────────────────────
define('APP_NAME',     'Medical Rekordz CMS');
define('APP_VERSION',  '1.0.0');
define('APP_BASE_URL', 'http://localhost'); // e.g. https://yourdomain.co.za

// ─── Allowed Hosts (HTTP host header injection protection) ────────────────────
define('ALLOWED_HOSTS', [
    'localhost',
    'yourdomain.co.za',
    'www.yourdomain.co.za',
]);

// ─── Timezone ─────────────────────────────────────────────────────────────────
define('APP_TIMEZONE', 'Africa/Johannesburg');
date_default_timezone_set(APP_TIMEZONE);

// ─── Paths ────────────────────────────────────────────────────────────────────
define('CONFIG_PATH',  BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('VIEW_PATH',    BASE_PATH . '/app/views');
