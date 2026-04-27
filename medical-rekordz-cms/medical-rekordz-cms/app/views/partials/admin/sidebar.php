<?php $user = (new \Core\Auth())->user(); ?>
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?= url('/admin') ?>">MRK Admin</a>
    </div>

    <nav class="sidebar-nav">
        <a href="<?= url('/admin') ?>" class="<?= isActive('admin') && !isActive('admin/') ? 'active' : '' ?>">Dashboard</a>
        <a href="<?= url('/admin/artists') ?>" class="<?= isActive('admin/artists') ? 'active' : '' ?>">Artists</a>
        <a href="<?= url('/admin/pages') ?>" class="<?= isActive('admin/pages') ? 'active' : '' ?>">Pages</a>
        <a href="<?= url('/admin/videos') ?>" class="<?= isActive('admin/videos') ? 'active' : '' ?>">Videos</a>
        <a href="<?= url('/admin/media') ?>" class="<?= isActive('admin/media') ? 'active' : '' ?>">Media</a>
        <a href="<?= url('/admin/contact') ?>" class="<?= isActive('admin/contact') ? 'active' : '' ?>">Messages</a>
        <a href="<?= url('/admin/nav') ?>" class="<?= isActive('admin/nav') ? 'active' : '' ?>">Navigation</a>
        <a href="<?= url('/admin/users') ?>" class="<?= isActive('admin/users') ? 'active' : '' ?>">Users</a>
        <a href="<?= url('/admin/settings') ?>" class="<?= isActive('admin/settings') ? 'active' : '' ?>">Settings</a>
    </nav>
</aside>
