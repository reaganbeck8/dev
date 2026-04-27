<div class="panel">
    <h2>System Settings</h2>

    <form method="POST" action="<?= url('/' . SUPERADMIN_SLUG . '/settings/update') ?>">
        <?= \Core\Csrf::field() ?>

        <?php foreach ($settings as $key => $value): ?>
            <div class="form-group">
                <label for="setting_<?= e($key) ?>"><?= e(ucwords(str_replace('_', ' ', $key))) ?></label>
                <input type="text" id="setting_<?= e($key) ?>" name="settings[<?= e($key) ?>]" value="<?= e(is_array($value) ? json_encode($value) : $value) ?>">
                <input type="hidden" name="types[<?= e($key) ?>]" value="string">
                <input type="hidden" name="groups[<?= e($key) ?>]" value="general">
            </div>
        <?php endforeach; ?>

        <div class="form-actions">
            <button type="submit" class="btn">Save Settings</button>
        </div>
    </form>
</div>
