<div class="panel-header">
    <h2>Database Backups</h2>
    <form method="POST" action="<?= url('/' . SUPERADMIN_SLUG . '/backup/create') ?>" class="inline">
        <?= \Core\Csrf::field() ?>
        <button type="submit" class="btn">Create Backup</button>
    </form>
</div>

<?php if (empty($backups)): ?>
    <p class="text-muted">No backups yet.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr><th>File</th><th>Size</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($backups as $backup): ?>
                <tr>
                    <td><?= e($backup['name']) ?></td>
                    <td><?= humanFileSize($backup['size']) ?></td>
                    <td><?= formatDateTime(date('Y-m-d H:i:s', $backup['modified'])) ?></td>
                    <td class="actions">
                        <a href="<?= url('/' . SUPERADMIN_SLUG . '/backup/download/0?file=' . urlencode($backup['name'])) ?>" class="btn btn-sm">Download</a>
                        <form method="POST" action="<?= url('/' . SUPERADMIN_SLUG . '/backup/delete') ?>" class="inline" onsubmit="return confirm('Delete?')">
                            <?= \Core\Csrf::field() ?>
                            <input type="hidden" name="file" value="<?= e($backup['name']) ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
