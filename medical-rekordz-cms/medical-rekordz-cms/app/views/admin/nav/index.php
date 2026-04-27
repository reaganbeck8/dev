<div class="panel-header">
    <h2>Navigation</h2>
</div>

<div class="panel">
    <h3>Add Menu Item</h3>
    <form method="POST" action="<?= url('/admin/nav/store') ?>">
        <?= \Core\Csrf::field() ?>

        <div class="form-row">
            <div class="form-group">
                <label for="label">Label *</label>
                <input type="text" id="label" name="label" required>
            </div>
            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" id="url" name="url" placeholder="/about or https://...">
            </div>
            <div class="form-group">
                <label for="page_id">Or Link to Page</label>
                <select id="page_id" name="page_id">
                    <option value="">Custom URL</option>
                    <?php foreach ($pages as $page): ?>
                        <option value="<?= $page['id'] ?>"><?= e($page['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">Order</label>
                <input type="number" id="sort_order" name="sort_order" value="0">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_active" checked> Active</label>
            </div>
        </div>

        <button type="submit" class="btn">Add Item</button>
    </form>
</div>

<div class="panel">
    <h3>Current Menu</h3>
    <?php if (empty($items)): ?>
        <p class="text-muted">No menu items yet.</p>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="nav-item-row">
                <span><?= e($item['label']) ?></span>
                <span class="text-muted"><?= e($item['url'] ?? ($item['page_slug'] ? '/page/' . $item['page_slug'] : '')) ?></span>
                <span class="badge badge-<?= $item['is_active'] ? 'success' : 'muted' ?>"><?= $item['is_active'] ? 'Active' : 'Hidden' ?></span>
                <form method="POST" action="<?= url('/admin/nav/delete/' . $item['id']) ?>" class="inline" onsubmit="return confirm('Delete?')">
                    <?= \Core\Csrf::field() ?>
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>

                <?php if (!empty($item['children'])): ?>
                    <div class="nav-children">
                        <?php foreach ($item['children'] as $child): ?>
                            <div class="nav-item-row nav-child">
                                <span>&mdash; <?= e($child['label']) ?></span>
                                <form method="POST" action="<?= url('/admin/nav/delete/' . $child['id']) ?>" class="inline" onsubmit="return confirm('Delete?')">
                                    <?= \Core\Csrf::field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
