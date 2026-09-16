<?php $title = 'Notices & Events'; ?>

<section class="page-head">
  <div class="container">
    <h1>Notices &amp; Events</h1>
    <p>Stay updated with school announcements</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="section-title">📢 Notices</h2>
    <div class="notice-list">
      <?php foreach ($notices as $n): ?>
        <article class="notice-card <?= $n['is_pinned'] ? 'pinned' : '' ?>">
          <div class="notice-meta">
            <time><?= e(format_date($n['published_at'])) ?></time>
            <?php if ($n['is_pinned']): ?><span class="pin-badge">📌 Pinned</span><?php endif; ?>
          </div>
          <h3><?= e($n['title']) ?></h3>
          <p><?= e($n['body']) ?></p>
        </article>
      <?php endforeach; ?>
      <?php if (!$notices): ?><p class="muted">No notices published yet.</p><?php endif; ?>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <h2 class="section-title">📅 Events</h2>
    <div class="event-list">
      <?php foreach ($events as $ev): ?>
        <article class="event-card">
          <?php if (!empty($ev['image'])): ?>
            <img class="event-photo" src="<?= e(upload_url($ev['image'])) ?>" alt="<?= e($ev['title']) ?>" loading="lazy">
          <?php endif; ?>
          <div class="event-when">
            <strong><?= e(date('d', strtotime($ev['event_date']))) ?></strong>
            <span><?= e(date('M Y', strtotime($ev['event_date']))) ?></span>
          </div>
          <div>
            <h3><?= e($ev['title']) ?></h3>
            <p><?= e($ev['description']) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (!$events): ?><p class="muted">No events scheduled yet.</p><?php endif; ?>
    </div>
  </div>
</section>
