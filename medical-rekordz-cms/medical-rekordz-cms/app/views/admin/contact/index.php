<div class="panel-header">
    <h2>Contact Submissions</h2>
</div>

<?php if (empty($submissions)): ?>
    <p class="text-muted">No messages yet.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Date</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $sub): ?>
                <tr class="<?= !$sub['is_read'] ? 'row-unread' : '' ?>">
                    <td><?= e($sub['name']) ?></td>
                    <td><?= e($sub['email']) ?></td>
                    <td><?= formatDateTime($sub['created_at']) ?></td>
                    <td><?= $sub['is_read'] ? 'Read' : '<strong>New</strong>' ?></td>
                    <td class="actions">
                        <a href="<?= url('/admin/contact/show/' . $sub['id']) ?>" class="btn btn-sm">View</a>
                        <form method="POST" action="<?= url('/admin/contact/delete/' . $sub['id']) ?>" class="inline" onsubmit="return confirm('Delete this message?')">
                            <?= \Core\Csrf::field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
