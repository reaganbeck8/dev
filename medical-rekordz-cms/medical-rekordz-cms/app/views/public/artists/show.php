<!-- ─── PROFILE HERO ───────────────────────────────────────── -->
<?php
$bgMedia = null;
if ($artist['profile_image_id']) {
    $bgMedia = (new \App\Models\MediaModel())->findById($artist['profile_image_id']);
}
?>
<section class="profile-hero" style="<?= $bgMedia ? 'background-image:url(' . url($bgMedia['file_path']) . ')' : '' ?>">
  <div class="profile-bg"></div>
  <div class="profile-info">
    <p class="section-label">Artist</p>
    <h1 class="profile-name"><?= e($artist['name']) ?></h1>
    <?php if ($artist['tagline']): ?>
      <p class="hero-sub"><?= e($artist['tagline']) ?></p>
    <?php endif; ?>
    <?php if (!empty($socials)): ?>
      <div class="profile-socials">
        <?php foreach ($socials as $social): ?>
          <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener"><?= e($social['platform']) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ─── PROFILE BODY ──────────────────────────────────────── -->
<?php if ($artist['bio']): ?>
<section class="profile-body fade-in">
  <div class="container">
    <div class="profile-content">
      <?= nl2br(e($artist['bio'])) ?>
    </div>
    <p class="mt-10">
      <a href="<?= url('/artists') ?>" class="btn-outline">&larr; All Artists</a>
    </p>
  </div>
</section>
<?php endif; ?>
