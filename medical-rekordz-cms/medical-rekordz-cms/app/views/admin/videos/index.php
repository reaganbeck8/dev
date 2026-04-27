<div class="panel-header">
    <h2>Videos</h2>
    <a href="<?= url('/admin/videos/create') ?>" class="btn">Add Video</a>
</div>

<?php if (empty($videos)): ?>
    <p class="text-muted">No videos yet.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr><th>Title</th><th>Type</th><th>Status</th><th>Order</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($videos as $video): ?>
                <tr>
                    <td><?= e($video['title']) ?></td>
                    <td><?= e($video['video_type']) ?></td>
                    <td><span class="badge badge-<?= $video['status'] === 'published' ? 'success' : 'muted' ?>"><?= e($video['status']) ?></span></td>
                    <td><?= $video['sort_order'] ?></td>
                    <td class="actions">
                        <a href="<?= url('/admin/videos/edit/' . $video['id']) ?>" class="btn btn-sm">Edit</a>
                        <form method="POST" action="<?= url('/admin/videos/delete/' . $video['id']) ?>" class="inline" onsubmit="return confirm('Delete this video?')">
                            <?= \Core\Csrf::field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
