<div class="panel">
    <h2>Message from <?= e($submission['name']) ?></h2>

    <dl class="detail-list">
        <dt>Email</dt>
        <dd><a href="mailto:<?= e($submission['email']) ?>"><?= e($submission['email']) ?></a></dd>

        <?php if ($submission['phone']): ?>
            <dt>Phone</dt>
            <dd><?= e($submission['phone']) ?></dd>
        <?php endif; ?>

        <dt>Date</dt>
        <dd><?= formatDateTime($submission['created_at']) ?></dd>

        <dt>IP</dt>
        <dd><?= e($submission['ip_address'] ?? 'N/A') ?></dd>

        <dt>Message</dt>
        <dd class="message-body"><?= nl2br(e($submission['message'])) ?></dd>
    </dl>

    <div class="form-actions">
        <a href="<?= url('/admin/contact') ?>" class="btn btn-outline">&larr; Back</a>
        <form method="POST" action="<?= url('/admin/contact/delete/' . $submission['id']) ?>" class="inline" onsubmit="return confirm('Delete this message?')">
            <?= \Core\Csrf::field() ?>
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
