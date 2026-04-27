<?php

// ─── SMTP Configuration ───────────────────────────────────────────────────────
define('MAIL_HOST',       'smtp.yourdomain.co.za');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'noreply@yourdomain.co.za');
define('MAIL_PASSWORD',   'your_smtp_password');
define('MAIL_ENCRYPTION', 'tls'); // 'tls' (port 587) or 'ssl' (port 465)

// ─── Sender Identity ──────────────────────────────────────────────────────────
define('MAIL_FROM_ADDRESS', 'noreply@yourdomain.co.za');
define('MAIL_FROM_NAME',    APP_NAME);

// ─── Reply-To ─────────────────────────────────────────────────────────────────
define('MAIL_REPLY_TO', 'support@yourdomain.co.za');

// ─── Delivery Address ─────────────────────────────────────────────────────────
define('MAIL_TO_ADDRESS', 'admin@yourdomain.co.za');
define('MAIL_TO_NAME',    APP_NAME . ' Admin');

// ─── Debug Level (ties into APP_DEBUG — silent in production) ─────────────────
define('MAIL_DEBUG', APP_DEBUG ? 2 : 0);
