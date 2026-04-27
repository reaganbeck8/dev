<?php $isEdit = !empty($user); ?>
<div class="panel">
    <h2><?= $isEdit ? 'Edit' : 'Add' ?> User</h2>

    <form method="POST" action="<?= url('/' . SUPERADMIN_SLUG . '/users/' . ($isEdit ? 'update/' . $user['id'] : 'store')) ?>">
        <?= \Core\Csrf::field() ?>

        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" value="<?= e($user['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= e($user['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password <?= $isEdit ? '(leave blank to keep)' : '*' ?></label>
            <input type="password" id="password" name="password" <?= !$isEdit ? 'required' : '' ?>>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="admin" <?= ($user['role'] ?? 'admin') === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="superadmin" <?= ($user['role'] ?? '') === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="is_active" <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?= $isEdit ? 'Update' : 'Create' ?></button>
            <a href="<?= url('/' . SUPERADMIN_SLUG . '/users') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
