<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Medical Rekordz') ?></title>
  <meta name="description" content="<?= e($meta_desc ?? 'Medical Rekordz — Independent music. No compromise.') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>

  <?php \Core\View::partial('public/header'); ?>

  <?php \Core\View::partial('public/flash'); ?>

  <?= $content ?>

  <?php \Core\View::partial('public/footer'); ?>

  <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
