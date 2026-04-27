<?php
$session = new \Core\Session();
$flashes = $session->getAllFlash();
foreach ($flashes as $type => $message): ?>
    <div class="alert alert-<?= e($type) ?>">
        <?= e($message) ?>
        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
<?php endforeach; ?>
