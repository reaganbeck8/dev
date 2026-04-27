<!-- ─── VIDEO HERO ──────────────────────────────────────── -->
<section class="hero" id="hero">
  <div class="hero-slide active" data-index="0" style="background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);"></div>
  <div class="hero-slide" data-index="1" style="background: linear-gradient(135deg, #0d0d0d 0%, #0a1020 100%);"></div>
  <div class="hero-slide" data-index="2" style="background: linear-gradient(135deg, #080808 0%, #101010 100%);"></div>

  <div class="hero-overlay">
    <p class="hero-eyebrow">Medical Rekordz</p>
    <h1 class="hero-headline"><?= e($artists[0]['name'] ?? 'Medical Rekordz') ?></h1>
    <p class="hero-sub"><?= e($artists[0]['tagline'] ?? 'Independent music. No compromise.') ?></p>
    <?php if (!empty($artists[0])): ?>
      <a href="<?= url('/artists/show/' . e($artists[0]['slug'])) ?>" class="btn-primary">Discover</a>
    <?php endif; ?>
    <div class="hero-dots" id="dots">
      <button class="dot active" data-target="0"></button>
      <button class="dot" data-target="1"></button>
      <button class="dot" data-target="2"></button>
    </div>
  </div>
</section>

<!-- ─── ARTISTS ──────────────────────────────────────────── -->
<?php if (!empty($artists)): ?>
<section class="spotlight fade-in">
  <div class="container">
    <p class="section-label">The Artists</p>
    <h2 class="section-title">Who We Are</h2>
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
    <div class="center mt-10">
      <a href="<?= url('/artists') ?>" class="btn-outline">View Artists</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ─── VIDEOS ─────────────────────────────────────────────── -->
<?php if (!empty($videos)): ?>
<section class="videos fade-in" id="videos">
  <div class="container">
    <p class="section-label">Watch</p>
    <h2 class="section-title">Latest Videos</h2>
    <div class="video-grid">
      <?php foreach (array_slice($videos, 0, 3) as $video): ?>
        <?php if ($video['video_type'] === 'youtube' && $video['youtube_url']):
          preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video['youtube_url'], $m);
          $ytId = $m[1] ?? '';
          if ($ytId): ?>
            <div class="yt-facade" data-yt="<?= e($ytId) ?>" role="button" aria-label="Play video">
              <img class="yt-thumb" src="https://i.ytimg.com/vi/<?= e($ytId) ?>/maxresdefault.jpg" alt="<?= e($video['title']) ?>" loading="lazy" onerror="this.src='https://i.ytimg.com/vi/<?= e($ytId) ?>/hqdefault.jpg'">
              <div class="yt-overlay"></div>
              <div class="yt-play"></div>
            </div>
          <?php endif;
        endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ─── ABOUT ────────────────────────────────────────────── -->
<section class="about fade-in" id="about">
  <div class="container about-inner">
    <div class="about-text">
      <p class="section-label">About</p>
      <h2 class="section-title">Medical Rekordz</h2>
      <p><?= e($settings['about_text'] ?? 'Medical Rekordz is an independent music brand built on authenticity and artistic freedom. Home of artists who create without compromise and perform without apology.') ?></p>
      <a href="<?= url('/contact') ?>" class="btn-primary mt-6" style="display:inline-block;">Get In Touch</a>
    </div>
    <div class="about-visual" style="background:#111; border-radius:16px; min-height:360px;"></div>
  </div>
</section>
