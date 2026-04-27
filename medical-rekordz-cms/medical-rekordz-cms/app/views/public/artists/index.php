<!-- ─── PAGE HERO ──────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <p class="section-label">The Roster</p>
    <h1 class="section-title">Our Artists</h1>
  </div>
</section>

<!-- ─── ARTISTS GRID ──────────────────────────────────────── -->
<section class="spotlight fade-in">
  <div class="container">
    <?php if (empty($artists)): ?>
      <p>No artists to show yet.</p>
    <?php else: ?>
      <div class="artist-grid <?= count($artists) <= 2 ? 'two-col' : '' ?>">
        <?php foreach ($artists as $artist): ?>
          <a href="<?= url('/artists/show/' . e($artist['slug'])) ?>" class="artist-card">
            <div class="artist-img">
              <?php if ($artist['profile_image_id']):
                $media = (new \App\Models\MediaModel())->findById($artist['profile_image_id']);
                if ($media): ?>
                  <img src="<?= url($media['file_path']) ?>" alt="<?= e($artist['name']) ?>">
                <?php endif;
              endif; ?>
            </div>
            <span class="artist-name"><?= e($artist['name']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
