<div class="panel-header">
    <h2>Log Viewer</h2>
    <div>
        <span class="text-muted">Log size: <?= humanFileSize($logSize) ?></span>
        <form method="POST" action="<?= url('/' . SUPERADMIN_SLUG . '/logs/clear') ?>" class="inline" onsubmit="return confirm('Clear the log?')">
            <?= \Core\Csrf::field() ?>
            <button type="submit" class="btn btn-sm btn-danger">Clear Log</button>
        </form>
    </div>
</div>

<div class="log-viewer">
    <?php if (empty($lines)): ?>
        <p class="text-muted">Log is empty.</p>
    <?php else: ?>
        <pre class="log-output"><?php foreach ($lines as $line): ?><?= e($line) ?>
<?php endforeach; ?></pre>
    <?php endif; ?>
</div>
