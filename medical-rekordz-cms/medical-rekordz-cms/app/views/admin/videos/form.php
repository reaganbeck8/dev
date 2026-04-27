<?php $isEdit = !empty($video); ?>
<div class="panel">
    <h2><?= $isEdit ? 'Edit' : 'Add' ?> Video</h2>

    <form method="POST" action="<?= url('/admin/videos/' . ($isEdit ? 'update/' . $video['id'] : 'store')) ?>">
        <?= \Core\Csrf::field() ?>

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" value="<?= e($video['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= e($video['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="video_type">Video Type</label>
            <select id="video_type" name="video_type">
                <option value="youtube" <?= ($video['video_type'] ?? 'youtube') === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                <option value="local" <?= ($video['video_type'] ?? '') === 'local' ? 'selected' : '' ?>>Local</option>
            </select>
        </div>

        <div class="form-group">
            <label for="youtube_url">YouTube URL</label>
            <input type="url" id="youtube_url" name="youtube_url" value="<?= e($video['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <div class="form-group">
            <label for="thumbnail_id">Thumbnail</label>
            <select id="thumbnail_id" name="thumbnail_id">
                <option value="">None</option>
                <?php foreach ($images as $img): ?>
                    <option value="<?= $img['id'] ?>" <?= ($video['thumbnail_id'] ?? '') == $img['id'] ? 'selected' : '' ?>>
                        <?= e($img['original_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="published" <?= ($video['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($video['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" value="<?= $video['sort_order'] ?? 0 ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?= $isEdit ? 'Update' : 'Create' ?> Video</button>
            <a href="<?= url('/admin/videos') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
