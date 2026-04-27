<div class="dashboard-stats">
    <div class="stat-card">
        <h3><?= $artistCount ?></h3>
        <p>Artists</p>
        <a href="<?= url('/admin/artists') ?>">Manage</a>
    </div>
    <div class="stat-card">
        <h3><?= $pageCount ?></h3>
        <p>Pages</p>
        <a href="<?= url('/admin/pages') ?>">Manage</a>
    </div>
    <div class="stat-card">
        <h3><?= $videoCount ?></h3>
        <p>Videos</p>
        <a href="<?= url('/admin/videos') ?>">Manage</a>
    </div>
    <div class="stat-card">
        <h3><?= $unreadMessages ?></h3>
        <p>Unread Messages</p>
        <a href="<?= url('/admin/contact') ?>">View</a>
    </div>
    <div class="stat-card">
        <h3><?= $mediaCount ?></h3>
        <p>Media Files</p>
        <a href="<?= url('/admin/media') ?>">Library</a>
    </div>
</div>

<div class="panel">
    <h2>Recent Activity</h2>
    <?php if (empty($recentActivity)): ?>
        <p class="text-muted">No activity yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr><th>Action</th><th>User</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentActivity as $log): ?>
                    <tr>
                        <td><?= e($log['action']) ?></td>
                        <td><?= e($log['user_name'] ?? 'System') ?></td>
                        <td><?= formatDateTime($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
