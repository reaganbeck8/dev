<?php $isEdit = !empty($page); ?>
<div class="panel">
    <h2><?= $isEdit ? 'Edit' : 'Add' ?> Page</h2>

    <form method="POST" action="<?= url('/admin/pages/' . ($isEdit ? 'update/' . $page['id'] : 'store')) ?>">
        <?= \Core\Csrf::field() ?>

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" value="<?= e($page['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?= e($page['slug'] ?? '') ?>" placeholder="Auto-generated from title">
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" rows="15" class="editor"><?= e($page['content'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="meta_title">Meta Title</label>
                <input type="text" id="meta_title" name="meta_title" value="<?= e($page['meta_title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="meta_desc">Meta Description</label>
                <input type="text" id="meta_desc" name="meta_desc" value="<?= e($page['meta_desc'] ?? '') ?>" maxlength="320">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?= ($page['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" value="<?= $page['sort_order'] ?? 0 ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?= $isEdit ? 'Update' : 'Create' ?> Page</button>
            <a href="<?= url('/admin/pages') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
