<div class="panel-header">
    <h2>All Users</h2>
    <a href="<?= url('/' . SUPERADMIN_SLUG . '/users/create') ?>" class="btn">Add User</a>
</div>

<table class="table">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Last Login</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= e($user['name']) ?></td>
                <td><?= e($user['email']) ?></td>
                <td><span class="badge badge-<?= $user['role'] === 'superadmin' ? 'warning' : 'info' ?>"><?= e($user['role']) ?></span></td>
                <td><?= $user['is_active'] ? 'Yes' : 'No' ?></td>
                <td><?= $user['last_login'] ? formatDateTime($user['last_login']) : 'Never' ?></td>
                <td class="actions">
                    <a href="<?= url('/' . SUPERADMIN_SLUG . '/users/edit/' . $user['id']) ?>" class="btn btn-sm">Edit</a>
                    <form method="POST" action="<?= url('/' . SUPERADMIN_SLUG . '/users/delete/' . $user['id']) ?>" class="inline" onsubmit="return confirm('Delete?')">
                        <?= \Core\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
