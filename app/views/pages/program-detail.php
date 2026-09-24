<?php $title = $program['title']; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb">
      <a href="<?= url('/') ?>">Home</a> <span>/</span>
      <a href="<?= url('/academics') ?>">Academics</a> <span>/</span> Program
    </p>
    <h1>📚 Academic Program</h1>
    <p>Learning path at <?= e(setting('site_name')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container detail-grid">

    <!-- Program content -->
    <article class="detail-main">
      <div class="detail-card reveal">
        <div class="detail-media">
          <span class="ph-fallback"><?= e($program['icon'] ?: '📚') ?></span>
          <?php if (!empty($program['photo'])): ?>
            <img class="detail-photo" src="<?= e(upload_url($program['photo'])) ?>" alt="<?= e($program['title']) ?>" onerror="this.remove()">
          <?php endif; ?>
        </div>
        <div class="detail-card-body">
          <h1 class="detail-title"><?= e($program['title']) ?></h1>
          <?php if ($program['grades']): ?>
            <p class="detail-grades">🎓 <?= e($program['grades']) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <div class="detail-body reveal">
        <h2>About this program</h2>
        <p><?= nl2br(e($program['description'] ?: 'Details for this program are coming soon. Please contact the school office for the latest curriculum information.')) ?></p>
      </div>

      <div class="detail-actions mt">
        <a class="btn btn-outline" href="<?= url('/academics') ?>">← Back to Academics</a>
        <a class="btn btn-primary" href="<?= url('/admissions') ?>">Apply for Admission</a>
      </div>
    </article>

    <!-- Sidebar: other programs -->
    <aside class="detail-side">
      <div class="panel">
        <div class="panel-head"><h3>📚 Other Programs</h3><a class="see-all" href="<?= url('/academics') ?>">View all →</a></div>
        <?php if ($programs): ?>
          <?php foreach ($programs as $p): ?>
            <div class="side-item">
              <div class="side-icon"><?= e($p['icon'] ?: '📚') ?></div>
              <div>
                <a class="side-link" href="<?= url('/programs/' . (int) $p['id']) ?>"><?= e($p['title']) ?></a>
                <?php if ($p['grades']): ?><span class="side-bs"><?= e($p['grades']) ?></span><?php endif; ?>
                <p><?= e(str_limit($p['description'], 80)) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="muted">No other programs right now.</p>
        <?php endif; ?>
      </div>
    </aside>

  </div>
</section>
