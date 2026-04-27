<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'System') ?> — MRK System</title>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/superadmin.css') ?>">
</head>
<body class="sa-body">
    <div class="sa-wrapper">
        <?php \Core\View::partial('superadmin/sidebar'); ?>

        <div class="sa-main">
            <?php \Core\View::partial('superadmin/topbar'); ?>

            <div class="sa-content">
                <?php \Core\View::partial('admin/flash'); ?>
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/superadmin.js') ?>"></script>
</body>
</html>
