<?php
$settingModel = new \App\Models\SettingModel();
$socialSettings = $settingModel->getGroup('social');
?>
<footer class="footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <span class="footer-logo">MEDICAL REKORDZ</span>
      <p>Independent music. No compromise.</p>
    </div>
    <div class="footer-links">
      <a href="<?= url('/artists') ?>">Artists</a>
      <a href="<?= url('/contact') ?>">Contact</a>
    </div>
    <div class="footer-social">
      <?php if (!empty($socialSettings['social_instagram'])): ?>
        <a href="<?= e($socialSettings['social_instagram']) ?>" aria-label="Instagram" target="_blank" rel="noopener">IG</a>
      <?php endif; ?>
      <?php if (!empty($socialSettings['social_twitter'])): ?>
        <a href="<?= e($socialSettings['social_twitter']) ?>" aria-label="Twitter" target="_blank" rel="noopener">TW</a>
      <?php endif; ?>
      <?php if (!empty($socialSettings['social_spotify'])): ?>
        <a href="<?= e($socialSettings['social_spotify']) ?>" aria-label="Spotify" target="_blank" rel="noopener">SP</a>
      <?php endif; ?>
      <?php if (empty($socialSettings)): ?>
        <a href="#" aria-label="Instagram">IG</a>
        <a href="#" aria-label="Twitter">TW</a>
        <a href="#" aria-label="Spotify">SP</a>
      <?php endif; ?>
    </div>
  </div>
  <p class="footer-copy">&copy; <span id="year"></span> Medical Rekordz. All rights reserved.</p>
</footer>
