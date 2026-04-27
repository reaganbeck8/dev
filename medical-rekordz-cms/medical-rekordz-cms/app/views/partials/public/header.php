<?php
$navModel = new \App\Models\NavModel();
$menuItems = $navModel->getMenuItems();
?>
<nav class="nav" id="nav">
  <div class="nav-inner">
    <a href="<?= url('/') ?>" class="nav-logo">MEDICAL REKORDZ</a>
    <ul class="nav-links">
      <li><a href="<?= url('/artists') ?>">Artists</a></li>
      <li><a href="<?= url('/videos') ?>">Videos</a></li>
      <?php foreach ($menuItems as $item): ?>
        <?php $href = $item['url'] ?? ($item['page_slug'] ? url('/page/show/' . $item['page_slug']) : '#'); ?>
        <li><a href="<?= e($href) ?>"><?= e($item['label']) ?></a></li>
      <?php endforeach; ?>
      <li><a href="<?= url('/contact') ?>">Contact</a></li>
    </ul>
    <button class="nav-burger" id="menu-btn" aria-label="Open menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<div class="mobile-menu" id="mobile-menu">
  <button class="mobile-close" id="menu-close">&times;</button>
  <a href="<?= url('/artists') ?>" class="mobile-link">Artists</a>
  <a href="<?= url('/videos') ?>" class="mobile-link">Videos</a>
  <?php foreach ($menuItems as $item): ?>
    <?php $href = $item['url'] ?? ($item['page_slug'] ? url('/page/show/' . $item['page_slug']) : '#'); ?>
    <a href="<?= e($href) ?>" class="mobile-link"><?= e($item['label']) ?></a>
  <?php endforeach; ?>
  <a href="<?= url('/contact') ?>" class="mobile-link">Contact</a>
</div>
