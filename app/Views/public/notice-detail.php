<?php $title = 'Notice'; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb">
      <a href="<?= url('/') ?>">Home</a> <span>/</span>
      <a href="<?= url('/notices') ?>">Notices &amp; Events</a> <span>/</span> Notice
    </p>
    <h1>📢 Notice</h1>
    <p>Official announcement from <?= e(setting('site_name')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container detail-grid">

    <!-- Notice content -->
    <article class="detail-main">
      <div class="notice-card pinned reveal">
        <div class="notice-date-lg">
          <strong><?= e(date('d', strtotime($notice['published_at']))) ?></strong>
          <span><?= e(date('M Y', strtotime($notice['published_at']))) ?></span>
        </div>
        <div class="notice-content">
          <div class="notice-meta">
            <time><?= e(format_date($notice['published_at'])) ?></time>
            <?php if ($notice['is_pinned']): ?><span class="pin-badge">📌 Pinned</span><?php endif; ?>
          </div>
          <h1 class="detail-title"><?= e($notice['title']) ?></h1>
        </div>
      </div>

      <div class="detail-body reveal">
        <p><?= nl2br(e($notice['body'])) ?></p>
      </div>

      <div class="detail-actions mt">
        <a class="btn btn-outline" href="<?= url('/notices') ?>">← Back to Notices &amp; Events</a>
        <a class="btn btn-primary" href="<?= url('/contact') ?>">Any Question? Contact Us</a>
      </div>
    </article>

    <!-- Sidebar: other notices -->
    <aside class="detail-side">
      <div class="panel">
        <div class="panel-head"><h3>📢 Other Notices</h3><a class="see-all" href="<?= url('/notices') ?>">View all →</a></div>
        <?php if ($notices): ?>
          <?php foreach ($notices as $n): ?>
            <div class="side-item">
              <div class="side-date"><?= e(date('d M', strtotime($n['published_at']))) ?></div>
              <div>
                <a class="side-link" href="<?= url('/notices/' . (int) $n['id']) ?>"><?= e($n['title']) ?></a>
                <p><?= e(str_limit($n['body'], 80)) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="muted">No other notices right now.</p>
        <?php endif; ?>
      </div>
    </aside>

  </div>
</section>
