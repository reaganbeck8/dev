<div class="panel-header">
    <h2>Media Library</h2>
    <div>
        <a href="<?= url('/admin/media') ?>" class="btn btn-sm <?= !$type ? 'btn-active' : '' ?>">All</a>
        <a href="<?= url('/admin/media?type=image') ?>" class="btn btn-sm <?= $type === 'image' ? 'btn-active' : '' ?>">Images</a>
        <a href="<?= url('/admin/media?type=audio') ?>" class="btn btn-sm <?= $type === 'audio' ? 'btn-active' : '' ?>">Audio</a>
        <a href="<?= url('/admin/media?type=video') ?>" class="btn btn-sm <?= $type === 'video' ? 'btn-active' : '' ?>">Video</a>
        <a href="<?= url('/admin/media?type=document') ?>" class="btn btn-sm <?= $type === 'document' ? 'btn-active' : '' ?>">Docs</a>
        <a href="<?= url('/admin/media/upload') ?>" class="btn">Upload</a>
    </div>
</div>

<?php if (empty($media)): ?>
    <p class="text-muted">No media files.</p>
<?php else: ?>
    <div class="media-grid">
        <?php foreach ($media as $item): ?>
            <div class="media-card">
                <?php if ($item['file_type'] === 'image'): ?>
                    <img src="<?= url($item['file_path']) ?>" alt="<?= e($item['original_name']) ?>">
                <?php else: ?>
                    <div class="media-icon"><?= strtoupper($item['file_type']) ?></div>
                <?php endif; ?>
                <div class="media-meta">
                    <p class="media-name"><?= e(truncate($item['original_name'], 30)) ?></p>
                    <p class="text-muted"><?= humanFileSize($item['file_size']) ?></p>
                </div>
                <form method="POST" action="<?= url('/admin/media/delete/' . $item['id']) ?>" onsubmit="return confirm('Delete this file?')">
                    <?= \Core\Csrf::field() ?>
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
