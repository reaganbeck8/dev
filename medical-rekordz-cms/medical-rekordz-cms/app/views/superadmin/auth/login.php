<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access</title>
    <link rel="stylesheet" href="<?= asset('css/superadmin.css') ?>">
</head>
<body class="login-body sa-login">
    <div class="login-card">
        <h1>System Access</h1>

        <?php
        $session = new \Core\Session();
        $flashes = $session->getAllFlash();
        foreach ($flashes as $type => $message): ?>
            <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
        <?php endforeach; ?>

        <form method="POST" action="<?= url('/' . SUPERADMIN_SLUG . '/auth/attempt') ?>">
            <?= \Core\Csrf::field() ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-full">Access</button>
        </form>
    </div>
</body>
</html>
