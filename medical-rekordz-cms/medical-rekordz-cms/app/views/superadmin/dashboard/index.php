<div class="dashboard-stats">
    <div class="stat-card">
        <h3><?= $userCount ?></h3>
        <p>Users</p>
    </div>
    <div class="stat-card">
        <h3><?= $mediaCount ?></h3>
        <p>Media Files</p>
    </div>
    <div class="stat-card">
        <h3><?= e($phpVersion) ?></h3>
        <p>PHP Version</p>
    </div>
</div>

<div class="panel">
    <h2>Recent Activity</h2>
    <?php if (empty($recentActivity)): ?>
        <p class="text-muted">No activity.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr><th>Action</th><th>User</th><th>Entity</th><th>IP</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentActivity as $log): ?>
                    <tr>
                        <td><?= e($log['action']) ?></td>
                        <td><?= e($log['user_name'] ?? 'System') ?></td>
                        <td><?= e(($log['entity_type'] ?? '') . ($log['entity_id'] ? '#' . $log['entity_id'] : '')) ?></td>
                        <td><?= e($log['ip_address'] ?? '') ?></td>
                        <td><?= formatDateTime($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
