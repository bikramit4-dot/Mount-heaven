<?php $title = 'Notices & Events'; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb"><a href="<?= url('/') ?>">Home</a> <span>/</span> Notices &amp; Events</p>
    <h1>Notices &amp; Events</h1>
    <p>Stay updated with school announcements</p>
  </div>
</section>

<!-- Notices -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Official Announcements</p>
      <h2 class="section-title center">📢 Notices</h2>
      <p class="section-sub">Important information for students and parents — pinned notices appear first.</p>
    </div>

    <div class="notice-list">
      <?php foreach ($notices as $n): ?>
        <article class="notice-card reveal <?= $n['is_pinned'] ? 'pinned' : '' ?>">
          <div class="notice-date-lg">
            <strong><?= e(date('d', strtotime($n['published_at']))) ?></strong>
            <span><?= e(date('M Y', strtotime($n['published_at']))) ?></span>
          </div>
          <div class="notice-content">
            <div class="notice-meta">
              <time><?= e(format_date($n['published_at'])) ?></time>
              <?php if ($n['is_pinned']): ?><span class="pin-badge">📌 Pinned</span><?php endif; ?>
            </div>
            <h3><?= e($n['title']) ?></h3>
            <p><?= e($n['body']) ?></p>
            <a class="btn btn-outline btn-sm view-btn" href="<?= url('/notices/' . (int) $n['id']) ?>">👁 View Details</a>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (!$notices): ?><p class="muted center-text">No notices published yet.</p><?php endif; ?>
    </div>
  </div>
</section>

<!-- Events -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Mark Your Calendar</p>
      <h2 class="section-title center">📅 Upcoming Events</h2>
      <p class="section-sub">Activities, celebrations and important dates on the school calendar.</p>
    </div>

    <div class="event-list">
      <?php foreach ($events as $ev): ?>
        <?php $isUpcoming = strtotime($ev['event_date']) >= strtotime('today'); ?>
        <article class="event-card reveal <?= $isUpcoming ? 'upcoming' : '' ?>">
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
            <?php if ($isUpcoming): ?><span class="event-soon">✨ Coming up</span><?php endif; ?>
            <a class="btn btn-outline btn-sm view-btn" href="<?= url('/events/' . (int) $ev['id']) ?>">👁 View Details</a>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (!$events): ?><p class="muted center-text">No events scheduled yet.</p><?php endif; ?>
    </div>
  </div>
</section>
