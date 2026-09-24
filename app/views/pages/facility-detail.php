<?php $title = $facility['title']; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb">
      <a href="<?= url('/') ?>">Home</a> <span>/</span>
      <a href="<?= url('/academics') ?>">Academics</a> <span>/</span> Facility
    </p>
    <h1>🏫 Campus Facility</h1>
    <p>Everything students need at <?= e(setting('site_name')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container detail-grid">

    <!-- Facility content -->
    <article class="detail-main">
      <div class="detail-card reveal">
        <div class="detail-media">
          <span class="ph-fallback"><?= e($facility['icon'] ?: '🏫') ?></span>
          <?php if (!empty($facility['photo'])): ?>
            <img class="detail-photo" src="<?= e(upload_url($facility['photo'])) ?>" alt="<?= e($facility['title']) ?>" onerror="this.remove()">
          <?php endif; ?>
        </div>
        <div class="detail-card-body">
          <h1 class="detail-title"><?= e($facility['title']) ?></h1>
        </div>
      </div>

      <div class="detail-body reveal">
        <h2>About this facility</h2>
        <p><?= nl2br(e($facility['description'] ?: 'Details for this facility are coming soon. Please contact the school office for more information.')) ?></p>
      </div>

      <div class="detail-actions mt">
        <a class="btn btn-outline" href="<?= url('/academics') ?>">← Back to Academics</a>
        <a class="btn btn-primary" href="<?= url('/contact') ?>">Schedule a Campus Visit</a>
      </div>
    </article>

    <!-- Sidebar: other facilities -->
    <aside class="detail-side">
      <div class="panel">
        <div class="panel-head"><h3>🏫 Other Facilities</h3><a class="see-all" href="<?= url('/academics') ?>">View all →</a></div>
        <?php if ($facilities): ?>
          <?php foreach ($facilities as $f): ?>
            <div class="side-item">
              <div class="side-icon"><?= e($f['icon'] ?: '🏫') ?></div>
              <div>
                <a class="side-link" href="<?= url('/facilities/' . (int) $f['id']) ?>"><?= e($f['title']) ?></a>
                <p><?= e(str_limit($f['description'], 80)) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="muted">No other facilities right now.</p>
        <?php endif; ?>
      </div>
    </aside>

  </div>
</section>
