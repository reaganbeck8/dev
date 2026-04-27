<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin') ?> — Medical Rekordz</title>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php \Core\View::partial('admin/sidebar'); ?>

        <div class="admin-main">
            <?php \Core\View::partial('admin/topbar'); ?>

            <div class="admin-content">
                <?php \Core\View::partial('admin/flash'); ?>
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
