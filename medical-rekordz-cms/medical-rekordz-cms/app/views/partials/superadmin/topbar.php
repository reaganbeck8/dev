<?php $user = (new \Core\Auth())->user(); ?>
<div class="sa-topbar">
    <h1 class="topbar-title"><?= e($title ?? 'System') ?></h1>

    <div class="topbar-actions">
        <a href="<?= url('/admin') ?>" class="btn btn-sm">Admin Panel</a>
        <span class="topbar-user"><?= e($user['name'] ?? 'Super Admin') ?></span>
        <a href="<?= url('/' . SUPERADMIN_SLUG . '/auth/logout') ?>" class="btn btn-sm btn-danger">Logout</a>
    </div>
</div>
