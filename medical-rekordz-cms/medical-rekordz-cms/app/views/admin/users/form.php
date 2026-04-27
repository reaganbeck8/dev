<?php $isEdit = !empty($user); ?>
<div class="panel">
    <h2><?= $isEdit ? 'Edit' : 'Add' ?> User</h2>

    <form method="POST" action="<?= url('/admin/users/' . ($isEdit ? 'update/' . $user['id'] : 'store')) ?>">
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
            <label for="password">Password <?= $isEdit ? '(leave blank to keep current)' : '*' ?></label>
            <input type="password" id="password" name="password" <?= !$isEdit ? 'required' : '' ?>>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="is_active" <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?= $isEdit ? 'Update' : 'Create' ?> User</button>
            <a href="<?= url('/admin/users') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
