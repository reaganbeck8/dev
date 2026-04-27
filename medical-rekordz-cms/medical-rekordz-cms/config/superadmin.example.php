<?php

// ─── Super Admin Hidden URL ───────────────────────────────────────────────────
// Generated during install — do not edit manually
// Example: 'vault-9f3a2c' — never use /superadmin or /sa as the slug
define('SUPERADMIN_SLUG', 'change-me-via-installer');

// ─── Session ──────────────────────────────────────────────────────────────────
// Both generated independently during install — fully decoupled from the slug
define('SUPERADMIN_SESSION_KEY',     'change-me-via-installer');
define('SUPERADMIN_SESSION_TIMEOUT', 1800); // 30 minutes idle timeout

// ─── Session Secret ───────────────────────────────────────────────────────────
// Generated during install via bin2hex(random_bytes(32)) — do not edit manually
define('SUPERADMIN_SESSION_SECRET', 'change-me-via-installer');

// ─── IP Whitelist ─────────────────────────────────────────────────────────────
// Add your static IP(s). Empty array disables the check (not recommended in production).
define('SUPERADMIN_IP_WHITELIST', [
    '127.0.0.1',
    // 'your.static.ip.here',
]);

// ─── Brute Force Protection ───────────────────────────────────────────────────
define('SUPERADMIN_MAX_ATTEMPTS',  5);
define('SUPERADMIN_LOCKOUT_TIME',  900); // 15 minutes in seconds

// ─── CSRF ─────────────────────────────────────────────────────────────────────
define('SUPERADMIN_CSRF_LENGTH', 64);
