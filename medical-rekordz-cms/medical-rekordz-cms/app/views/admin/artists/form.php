<?php $isEdit = !empty($artist); ?>
<div class="panel">
    <h2><?= $isEdit ? 'Edit' : 'Add' ?> Artist</h2>

    <form method="POST" action="<?= url('/admin/artists/' . ($isEdit ? 'update/' . $artist['id'] : 'store')) ?>">
        <?= \Core\Csrf::field() ?>

        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" value="<?= e($artist['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?= e($artist['slug'] ?? '') ?>" placeholder="Auto-generated from name">
        </div>

        <div class="form-group">
            <label for="tagline">Tagline</label>
            <input type="text" id="tagline" name="tagline" value="<?= e($artist['tagline'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="6"><?= e($artist['bio'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="profile_image_id">Profile Image</label>
            <select id="profile_image_id" name="profile_image_id">
                <option value="">None</option>
                <?php foreach ($images as $img): ?>
                    <option value="<?= $img['id'] ?>" <?= ($artist['profile_image_id'] ?? '') == $img['id'] ? 'selected' : '' ?>>
                        <?= e($img['original_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active" <?= ($artist['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($artist['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" value="<?= $artist['sort_order'] ?? 0 ?>">
            </div>
        </div>

        <h3>Social Links</h3>
        <div id="socials-list">
            <?php
            $existingSocials = $socials ?? [];
            if (empty($existingSocials)) {
                $existingSocials = [['platform' => '', 'url' => '']];
            }
            foreach ($existingSocials as $i => $social): ?>
                <div class="form-row social-row">
                    <div class="form-group">
                        <input type="text" name="socials[<?= $i ?>][platform]" placeholder="Platform" value="<?= e($social['platform']) ?>">
                    </div>
                    <div class="form-group">
                        <input type="url" name="socials[<?= $i ?>][url]" placeholder="URL" value="<?= e($social['url']) ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm" onclick="addSocialRow()">+ Add Social</button>

        <div class="form-actions">
            <button type="submit" class="btn"><?= $isEdit ? 'Update' : 'Create' ?> Artist</button>
            <a href="<?= url('/admin/artists') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function addSocialRow() {
    var list = document.getElementById('socials-list');
    var i = list.children.length;
    var row = document.createElement('div');
    row.className = 'form-row social-row';
    row.innerHTML = '<div class="form-group"><input type="text" name="socials[' + i + '][platform]" placeholder="Platform"></div>'
        + '<div class="form-group"><input type="url" name="socials[' + i + '][url]" placeholder="URL"></div>';
    list.appendChild(row);
}
</script>
