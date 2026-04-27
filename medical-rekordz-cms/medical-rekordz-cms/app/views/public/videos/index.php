<!-- ─── PAGE HERO ──────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <p class="section-label">Watch</p>
    <h1 class="section-title">Videos</h1>
  </div>
</section>

<!-- ─── VIDEOS GRID ───────────────────────────────────────── -->
<section class="videos fade-in">
  <div class="container">
    <?php if (empty($videos)): ?>
      <p>No videos to show yet.</p>
    <?php else: ?>
      <div class="video-grid">
        <?php foreach ($videos as $video): ?>
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
    <?php endif; ?>
  </div>
</section>
