<div class="panel-header">
    <h2>Pages</h2>
    <a href="<?= url('/admin/pages/create') ?>" class="btn">Add Page</a>
</div>

<?php if (empty($pages)): ?>
    <p class="text-muted">No pages yet.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr><th>Title</th><th>Slug</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page): ?>
                <tr>
                    <td><?= e($page['title']) ?></td>
                    <td><code><?= e($page['slug']) ?></code></td>
                    <td><span class="badge badge-<?= $page['status'] === 'published' ? 'success' : 'muted' ?>"><?= e($page['status']) ?></span></td>
                    <td class="actions">
                        <a href="<?= url('/admin/pages/edit/' . $page['id']) ?>" class="btn btn-sm">Edit</a>
                        <form method="POST" action="<?= url('/admin/pages/delete/' . $page['id']) ?>" class="inline" onsubmit="return confirm('Delete this page?')">
                            <?= \Core\Csrf::field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
