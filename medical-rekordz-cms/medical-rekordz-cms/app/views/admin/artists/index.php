<div class="panel-header">
    <h2>Artists</h2>
    <a href="<?= url('/admin/artists/create') ?>" class="btn">Add Artist</a>
</div>

<?php if (empty($artists)): ?>
    <p class="text-muted">No artists yet.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr><th>Name</th><th>Status</th><th>Order</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($artists as $artist): ?>
                <tr>
                    <td><?= e($artist['name']) ?></td>
                    <td><span class="badge badge-<?= $artist['status'] === 'active' ? 'success' : 'muted' ?>"><?= e($artist['status']) ?></span></td>
                    <td><?= $artist['sort_order'] ?></td>
                    <td class="actions">
                        <a href="<?= url('/admin/artists/edit/' . $artist['id']) ?>" class="btn btn-sm">Edit</a>
                        <form method="POST" action="<?= url('/admin/artists/delete/' . $artist['id']) ?>" class="inline" onsubmit="return confirm('Delete this artist?')">
                            <?= \Core\Csrf::field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
