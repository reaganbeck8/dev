<?php $user = (new \Core\Auth())->user(); ?>
<div class="admin-topbar">
    <h1 class="topbar-title"><?= e($title ?? 'Dashboard') ?></h1>

    <div class="topbar-actions">
        <a href="<?= url('/') ?>" target="_blank" class="btn btn-sm">View Site</a>
        <span class="topbar-user"><?= e($user['name'] ?? 'Admin') ?></span>
        <a href="<?= url('/admin/auth/logout') ?>" class="btn btn-sm btn-danger">Logout</a>
    </div>
</div>
