<?php

/**
 * Medical Rekordz CMS — Install Wizard
 * Runs once on first deploy. Blocked after install.lock is written.
 */

define('BASE_PATH', dirname(__DIR__));

// ─── Block if already installed ───────────────────────────────────────────────
if (file_exists(BASE_PATH . '/storage/install.lock')) {
    http_response_code(403);
    exit('Already installed. Remove storage/install.lock to reinstall.');
}

// ─── Load app config if it exists (for APP_NAME etc.) ────────────────────────
if (file_exists(BASE_PATH . '/config/app.php')) {
    require_once BASE_PATH . '/config/app.php';
} else {
    define('APP_NAME', 'Medical Rekordz CMS');
}

$errors  = [];
$success = false;
$step    = 'form';

// ─── Environment checks ───────────────────────────────────────────────────────
$envChecks = [
    'PHP >= 8.0'             => version_compare(PHP_VERSION, '8.0.0', '>='),
    'PDO extension'          => extension_loaded('pdo'),
    'PDO MySQL driver'       => extension_loaded('pdo_mysql'),
    'OpenSSL extension'      => extension_loaded('openssl'),
    'storage/ writable'      => is_writable(BASE_PATH . '/storage'),
    'storage/logs/ writable' => is_writable(BASE_PATH . '/storage/logs'),
    'config/ writable'       => is_writable(BASE_PATH . '/config'),
];

$envPassed = !in_array(false, $envChecks, true);

// ─── Handle form submission ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $envPassed) {
    $dbHost    = trim($_POST['db_host']        ?? 'localhost');
    $dbPort    = trim($_POST['db_port']        ?? '3306');
    $dbName    = trim($_POST['db_name']        ?? '');
    $dbUser    = trim($_POST['db_user']        ?? '');
    $dbPass    = trim($_POST['db_pass']        ?? '');
    $appName   = trim($_POST['app_name']       ?? 'Medical Rekordz CMS');
    $appUrl    = rtrim(trim($_POST['app_url']  ?? ''), '/');
    $saName    = trim($_POST['sa_name']        ?? '');
    $saEmail   = trim($_POST['sa_email']       ?? '');
    $saPass    = trim($_POST['sa_pass']        ?? '');
    $saPassCon = trim($_POST['sa_pass_confirm']?? '');
    $saSlug    = trim($_POST['sa_slug']        ?? '');

    // ── Validate inputs
    if (empty($dbName))  $errors[] = 'Database name is required.';
    if (empty($dbUser))  $errors[] = 'Database username is required.';
    if (empty($appUrl))  $errors[] = 'Application URL is required.';
    if (empty($saName))  $errors[] = 'Super admin name is required.';
    if (empty($saEmail) || !filter_var($saEmail, FILTER_VALIDATE_EMAIL))
        $errors[] = 'A valid super admin email is required.';
    if (strlen($saPass) < 10)
        $errors[] = 'Super admin password must be at least 10 characters.';
    if ($saPass !== $saPassCon)
        $errors[] = 'Passwords do not match.';
    if (empty($saSlug) || !preg_match('/^[a-z0-9-]{6,}$/', $saSlug))
        $errors[] = 'Super admin slug must be at least 6 characters (lowercase letters, numbers, hyphens only).';

    // ── Test DB connection
    if (empty($errors)) {
        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            $errors[] = 'Database connection failed: ' . $e->getMessage();
        }
    }

    // ── Run schema
    if (empty($errors)) {
        try {
            $schema     = file_get_contents(BASE_PATH . '/install/schema.sql');
            $statements = array_filter(
                array_map('trim', explode(';', $schema)),
                fn($s) => !empty($s) && !str_starts_with(ltrim($s), '--')
            );
            foreach ($statements as $statement) {
                $pdo->exec($statement);
            }
        } catch (PDOException $e) {
            $errors[] = 'Schema import failed: ' . $e->getMessage();
        }
    }

    // ── Seed default settings
    if (empty($errors)) {
        $now      = date('Y-m-d H:i:s');
        $defaults = [
            ['site_name',        $appName, 'string', 'general'],
            ['site_url',         $appUrl,  'string', 'general'],
            ['site_tagline',     'Independent music. No compromise.', 'string', 'general'],
            ['contact_email',    $saEmail, 'string', 'general'],
            ['maintenance_mode', '0',      'bool',   'general'],
            ['social_instagram', '',       'string', 'social'],
            ['social_twitter',   '',       'string', 'social'],
            ['social_spotify',   '',       'string', 'social'],
            ['social_youtube',   '',       'string', 'social'],
        ];
        foreach ($defaults as [$key, $value, $type, $group]) {
            $pdo->prepare(
                "INSERT IGNORE INTO mrk_settings (setting_key, setting_value, setting_type, setting_group, updated_at)
                 VALUES (?, ?, ?, ?, ?)"
            )->execute([$key, $value, $type, $group, $now]);
        }
    }

    // ── Create superadmin account
    if (empty($errors)) {
        $now          = date('Y-m-d H:i:s');
        $passwordHash = password_hash($saPass, PASSWORD_BCRYPT, ['cost' => 12]);
        try {
            $pdo->prepare(
                "INSERT INTO mrk_users (name, email, password_hash, role, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, 'superadmin', 1, ?, ?)"
            )->execute([$saName, strtolower($saEmail), $passwordHash, $now, $now]);
        } catch (PDOException $e) {
            $errors[] = 'Failed to create super admin account: ' . $e->getMessage();
        }
    }

    // ── Generate session credentials and write config files
    if (empty($errors)) {
        $sessionKey    = bin2hex(random_bytes(16));
        $sessionSecret = bin2hex(random_bytes(32));
        $allowedHost   = parse_url($appUrl, PHP_URL_HOST);

        $dbConfig = <<<PHP
        <?php

        define('DB_HOST',    '{$dbHost}');
        define('DB_NAME',    '{$dbName}');
        define('DB_USER',    '{$dbUser}');
        define('DB_PASS',    '{$dbPass}');
        define('DB_CHARSET', 'utf8mb4');
        define('DB_PORT',    '{$dbPort}');
        define('DB_PREFIX',  'mrk_');
        define('DB_DSN', 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);
        define('DB_OPTIONS', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ]);
        PHP;

        $appConfig = <<<PHP
        <?php

        define('APP_ENV',     'production');
        define('APP_DEBUG',    APP_ENV === 'development');
        define('APP_NAME',    '{$appName}');
        define('APP_VERSION', '1.0.0');
        define('APP_BASE_URL','{$appUrl}');
        define('ALLOWED_HOSTS', [
            '{$allowedHost}',
            'www.{$allowedHost}',
        ]);
        define('APP_TIMEZONE', 'Africa/Johannesburg');
        date_default_timezone_set(APP_TIMEZONE);
        define('CONFIG_PATH',  BASE_PATH . '/config');
        define('STORAGE_PATH', BASE_PATH . '/storage');
        define('VIEW_PATH',    BASE_PATH . '/app/views');
        PHP;

        $saConfig = <<<PHP
        <?php

        define('SUPERADMIN_SLUG',           '{$saSlug}');
        define('SUPERADMIN_SESSION_KEY',    '{$sessionKey}');
        define('SUPERADMIN_SESSION_TIMEOUT', 1800);
        define('SUPERADMIN_SESSION_SECRET', '{$sessionSecret}');
        define('SUPERADMIN_IP_WHITELIST',   []);
        define('SUPERADMIN_MAX_ATTEMPTS',   5);
        define('SUPERADMIN_LOCKOUT_TIME',   900);
        define('SUPERADMIN_CSRF_LENGTH',    64);
        PHP;

        $writeErrors = [];
        if (file_put_contents(BASE_PATH . '/config/database.php',   $dbConfig) === false) $writeErrors[] = 'config/database.php';
        if (file_put_contents(BASE_PATH . '/config/app.php',        $appConfig) === false) $writeErrors[] = 'config/app.php';
        if (file_put_contents(BASE_PATH . '/config/superadmin.php', $saConfig) === false)  $writeErrors[] = 'config/superadmin.php';

        if (!empty($writeErrors)) {
            $errors[] = 'Failed to write config files: ' . implode(', ', $writeErrors);
        }
    }

    // ── Write install.lock last — only on full success
    if (empty($errors)) {
        file_put_contents(BASE_PATH . '/storage/install.lock', 'Installed: ' . date('Y-m-d H:i:s') . PHP_EOL);
        $step    = 'result';
        $success = true;
    } else {
        $step = 'result';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install — <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0d0f14; color: #e4e8f2; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #161920; border: 1px solid #252a33; border-radius: 12px; width: 100%; max-width: 580px; padding: 40px; }
        h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .subtitle { color: #8892aa; font-size: 14px; margin-bottom: 32px; }
        .section { margin-bottom: 28px; }
        .section-title { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #4d8fff; font-weight: 600; margin-bottom: 14px; }
        .check-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #1d2129; font-size: 14px; }
        .check-row:last-child { border-bottom: none; }
        .badge { font-size: 12px; font-weight: 600; padding: 2px 10px; border-radius: 20px; }
        .badge.pass { background: #1a3a2a; color: #4caf7d; }
        .badge.fail { background: #3a1a1a; color: #ff5252; }
        label { display: block; font-size: 13px; color: #8892aa; margin-bottom: 6px; }
        input[type=text], input[type=email], input[type=password], input[type=url] {
            width: 100%; background: #0f1117; border: 1px solid #252a33; border-radius: 6px;
            color: #e4e8f2; font-size: 14px; padding: 10px 12px; margin-bottom: 16px; transition: border-color .2s;
        }
        input:focus { outline: none; border-color: #4d8fff; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .slug-row { display: flex; gap: 8px; align-items: flex-start; }
        .slug-row input { margin-bottom: 16px; }
        .gen-btn { white-space: nowrap; background: #1d2129; border: 1px solid #252a33; color: #8892aa; border-radius: 6px; font-size: 13px; padding: 10px 14px; cursor: pointer; transition: background .2s; margin-top: 0; }
        .gen-btn:hover { background: #252a33; color: #e4e8f2; }
        .btn { width: 100%; background: #4d8fff; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; padding: 13px; cursor: pointer; transition: background .2s; }
        .btn:hover { background: #3a7aff; }
        .btn:disabled { background: #252a33; color: #4a5570; cursor: not-allowed; }
        .errors { background: #3a1a1a; border: 1px solid #ff525244; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .errors p { color: #ff5252; font-size: 13px; margin-bottom: 4px; }
        .errors p:last-child { margin-bottom: 0; }
        .success-box { text-align: center; }
        .success-box h2 { font-size: 24px; margin-bottom: 12px; color: #4caf7d; }
        .success-box p { color: #8892aa; font-size: 14px; line-height: 1.6; margin-bottom: 8px; }
        .cred-box { background: #0f1117; border: 1px solid #252a33; border-radius: 8px; padding: 16px; margin: 20px 0; text-align: left; }
        .cred-box p { font-size: 13px; color: #8892aa; margin-bottom: 6px; }
        .cred-box p:last-child { margin-bottom: 0; }
        .cred-box strong { color: #e4e8f2; }
        .login-btn { display: inline-block; margin-top: 16px; background: #4d8fff; color: #fff; padding: 11px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>
<div class="card">
    <h1><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="subtitle">Installation Wizard</p>

    <?php if ($step === 'result' && $success): ?>
    <div class="success-box">
        <h2>&#10003; Installation Complete</h2>
        <p>Your CMS is ready. Save the details below — the super admin URL will <strong>not</strong> be shown again.</p>
        <div class="cred-box">
            <p>Admin login: <strong><?= htmlspecialchars($appUrl . '/admin/login', ENT_QUOTES, 'UTF-8') ?></strong></p>
            <p>Super admin login: <strong><?= htmlspecialchars($appUrl . '/' . $saSlug . '/login', ENT_QUOTES, 'UTF-8') ?></strong></p>
            <p>Super admin email: <strong><?= htmlspecialchars($saEmail, ENT_QUOTES, 'UTF-8') ?></strong></p>
        </div>
        <p>The install wizard is now locked. For additional security, delete the <code>/install</code> directory from the server.</p>
        <a class="login-btn" href="<?= htmlspecialchars($appUrl . '/admin/login', ENT_QUOTES, 'UTF-8') ?>">Go to Admin Login</a>
    </div>

    <?php elseif ($step === 'result' && !$success): ?>
    <div class="errors">
        <?php foreach ($errors as $err): ?>
            <p>&#9888; <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
    <p style="color:#8892aa;font-size:13px;">Fix the errors above and <a href="" style="color:#4d8fff;">try again</a>.</p>

    <?php else: ?>
    <div class="section">
        <div class="section-title">Environment Checks</div>
        <?php foreach ($envChecks as $label => $passed): ?>
        <div class="check-row">
            <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="badge <?= $passed ? 'pass' : 'fail' ?>"><?= $passed ? 'Pass' : 'Fail' ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="errors">
        <?php foreach ($errors as $err): ?>
            <p>&#9888; <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="section">
            <div class="section-title">Database</div>
            <div class="row">
                <div>
                    <label>Host</label>
                    <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div>
                    <label>Port</label>
                    <input type="text" name="db_port" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
            </div>
            <label>Database Name</label>
            <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            <label>Username</label>
            <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            <label>Password</label>
            <input type="password" name="db_pass">
        </div>

        <div class="section">
            <div class="section-title">Application</div>
            <label>Site Name</label>
            <input type="text" name="app_name" value="<?= htmlspecialchars($_POST['app_name'] ?? 'Medical Rekordz', ENT_QUOTES, 'UTF-8') ?>" required>
            <label>Site URL (no trailing slash)</label>
            <input type="url" name="app_url" placeholder="https://yourdomain.co.za" value="<?= htmlspecialchars($_POST['app_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="section">
            <div class="section-title">Super Admin Account</div>
            <label>Full Name</label>
            <input type="text" name="sa_name" value="<?= htmlspecialchars($_POST['sa_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            <label>Email</label>
            <input type="email" name="sa_email" value="<?= htmlspecialchars($_POST['sa_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            <div class="row">
                <div>
                    <label>Password (min. 10 chars)</label>
                    <input type="password" name="sa_pass" required>
                </div>
                <div>
                    <label>Confirm Password</label>
                    <input type="password" name="sa_pass_confirm" required>
                </div>
            </div>
            <label>Secret Login Slug</label>
            <div class="slug-row">
                <input type="text" name="sa_slug" id="sa_slug" placeholder="e.g. vault-a9f3b2 — min. 6 chars"
                       value="<?= htmlspecialchars($_POST['sa_slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                <button type="button" class="gen-btn" onclick="generateSlug()">Generate</button>
            </div>
        </div>

        <button type="submit" class="btn" <?= !$envPassed ? 'disabled' : '' ?>>
            <?= $envPassed ? 'Install Now' : 'Fix Environment Issues First' ?>
        </button>
    </form>
    <?php endif; ?>
</div>

<script>
function generateSlug() {
    const chars = 'abcdefghijkmnpqrstuvwxyz23456789';
    const rand  = Array.from(crypto.getRandomValues(new Uint8Array(8)))
        .map(b => chars[b % chars.length]).join('');
    document.getElementById('sa_slug').value = 'vault-' + rand;
}
</script>
</body>
</html>
