<div class="panel-header">
    <h2>File Manager</h2>
    <?php if ($currentDir): ?>
        <a href="<?= url('/' . SUPERADMIN_SLUG . '/filemanager?dir=' . urlencode($parentDir)) ?>" class="btn btn-sm">&larr; Back</a>
    <?php endif; ?>
</div>

<p class="text-muted">Current: /storage/uploads/<?= e($currentDir) ?></p>

<?php if (empty($items)): ?>
    <p class="text-muted">Empty directory.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr><th>Name</th><th>Size</th><th>Modified</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php if ($item['is_dir']): ?>
                            <a href="<?= url('/' . SUPERADMIN_SLUG . '/filemanager?dir=' . urlencode($item['path'])) ?>"><?= e($item['name']) ?>/</a>
                        <?php else: ?>
                            <?= e($item['name']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $item['is_dir'] ? '-' : humanFileSize($item['size']) ?></td>
                    <td><?= formatDateTime(date('Y-m-d H:i:s', $item['modified'])) ?></td>
                    <td>
                        <?php if (!$item['is_dir']): ?>
                            <form method="POST" action="<?= url('/' . SUPERADMIN_SLUG . '/filemanager/delete') ?>" class="inline" onsubmit="return confirm('Delete this file?')">
                                <?= \Core\Csrf::field() ?>
                                <input type="hidden" name="file" value="<?= e($item['path']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
