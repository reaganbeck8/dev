<div class="panel-header">
    <h2>Users</h2>
    <a href="<?= url('/admin/users/create') ?>" class="btn">Add User</a>
</div>

<?php if (empty($users)): ?>
    <p class="text-muted">No users.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Active</th><th>Last Login</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= $user['is_active'] ? 'Yes' : 'No' ?></td>
                    <td><?= $user['last_login'] ? formatDateTime($user['last_login']) : 'Never' ?></td>
                    <td class="actions">
                        <a href="<?= url('/admin/users/edit/' . $user['id']) ?>" class="btn btn-sm">Edit</a>
                        <form method="POST" action="<?= url('/admin/users/delete/' . $user['id']) ?>" class="inline" onsubmit="return confirm('Delete this user?')">
                            <?= \Core\Csrf::field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
