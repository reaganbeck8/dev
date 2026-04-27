<?php
$session = new \Core\Session();
$flashes = $session->getAllFlash();
foreach ($flashes as $type => $message): ?>
    <div class="flash flash-<?= e($type) ?>">
        <?= e($message) ?>
    </div>
<?php endforeach; ?>
