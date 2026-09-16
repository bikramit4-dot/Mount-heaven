<?php $title = 'Home'; ?>

<!-- Hero slider -->
<section class="hero">
  <div class="hero-slides" id="heroSlides">
    <?php foreach ($sliders as $i => $s): ?>
      <div class="hero-slide <?= $i === 0 ? 'is-active' : '' ?>" style="background-image:url('<?= e(upload_url($s['image'])) ?>')">
        <div class="hero-overlay"></div>
        <div class="container hero-caption">
          <h1><?= e($s['title']) ?></h1>
          <p><?= e($s['subtitle']) ?></p>
          <?php if ($s['button_text']): ?>
            <a class="btn btn-gold" href="<?= e($s['button_url'] ?: url('/')) ?>"><?= e($s['button_text']) ?></a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($sliders) > 1): ?>
    <div class="hero-dots" id="heroDots">
      <?php foreach ($sliders as $i => $s): ?>
        <button class="<?= $i === 0 ? 'is-active' : '' ?>" data-slide="<?= $i ?>" aria-label="Slide <?= $i + 1 ?>"></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- Value strip -->
<section class="strip">
  <div class="container strip-grid">
    <div class="strip-item"><span>🎓</span><strong>Experienced Faculty</strong><small>Qualified &amp; caring teachers</small></div>
    <div class="strip-item"><span>🏆</span><strong>Academic Excellence</strong><small>Consistent board results</small></div>
    <div class="strip-item"><span>🧑‍🤝‍🧑</span><strong>Small Class Sizes</strong><small>Personal attention</small></div>
    <div class="strip-item"><span>🚌</span><strong>Safe Transport</strong><small>GPS-enabled buses</small></div>
  </div>
</section>

<!-- About preview -->
<section class="section">
  <div class="container split">
    <div>
      <p class="eyebrow">About Our School</p>
      <h2 class="section-title left"><?= e(setting('site_name')) ?></h2>
      <p class="lead"><?= e(setting('about_short')) ?></p>
      <p><?= e(str_limit(setting('about_history'), 260)) ?></p>
      <a class="btn btn-primary" href="<?= url('/about') ?>">Learn More About Us</a>
    </div>
    <div class="split-cards">
      <div class="mini-card"><h4>🎯 Our Mission</h4><p><?= e(str_limit(setting('about_mission'), 130)) ?></p></div>
      <div class="mini-card alt"><h4>🔭 Our Vision</h4><p><?= e(str_limit(setting('about_vision'), 130)) ?></p></div>
    </div>
  </div>
</section>

<!-- Programs -->
<section class="section section-alt">
  <div class="container">
    <p class="eyebrow center">What We Offer</p>
    <h2 class="section-title center">Academic Programs</h2>
    <div class="grid grid-4">
      <?php foreach ($programs as $p): ?>
        <article class="card program-card">
          <?php if (!empty($p['photo'])): ?>
            <div class="program-photo"><img src="<?= e(upload_url($p['photo'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy"></div>
          <?php else: ?>
            <div class="program-icon"><?= e($p['icon']) ?></div>
          <?php endif; ?>
          <h3><?= e($p['title']) ?></h3>
          <p class="grades"><?= e($p['grades']) ?></p>
          <p><?= e(str_limit($p['description'], 110)) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="center mt"><a class="btn btn-outline" href="<?= url('/academics') ?>">View All Academics →</a></p>
  </div>
</section>

<!-- Facilities -->
<section class="section">
  <div class="container">
    <p class="eyebrow center">World-Class Campus</p>
    <h2 class="section-title center">Our Facilities</h2>
    <div class="grid grid-3">
      <?php foreach ($facilities as $f): ?>
        <div class="facility">
          <?php if (!empty($f['photo'])): ?>
            <span class="facility-photo"><img src="<?= e(upload_url($f['photo'])) ?>" alt="<?= e($f['title']) ?>" loading="lazy"></span>
          <?php else: ?>
            <span class="facility-icon"><?= e($f['icon']) ?></span>
          <?php endif; ?>
          <div>
            <h4><?= e($f['title']) ?></h4>
            <p><?= e(str_limit($f['description'], 110)) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Notices & Events -->
<section class="section section-alt">
  <div class="container two-col">
    <div class="panel">
      <div class="panel-head"><h3>📢 Latest Notices</h3><a class="see-all" href="<?= url('/notices') ?>">View all</a></div>
      <?php foreach ($notices as $n): ?>
        <div class="notice-row">
          <div class="notice-date"><strong><?= e(date('d', strtotime($n['published_at']))) ?></strong><span><?= e(date('M', strtotime($n['published_at']))) ?></span></div>
          <div>
            <h4><?= e($n['title']) ?> <?= $n['is_pinned'] ? '<span class="pin">📌</span>' : '' ?></h4>
            <p><?= e(str_limit($n['body'], 100)) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="panel">
      <div class="panel-head"><h3>📅 Upcoming Events</h3><a class="see-all" href="<?= url('/notices') ?>">View all</a></div>
      <?php foreach ($events as $ev): ?>
        <div class="event-row">
          <div class="event-date"><strong><?= e(date('d', strtotime($ev['event_date']))) ?></strong><span><?= e(date('M Y', strtotime($ev['event_date']))) ?></span></div>
          <div>
            <h4><?= e($ev['title']) ?></h4>
            <p><?= e(str_limit($ev['description'], 90)) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta">
  <div class="container cta-inner">
    <div>
      <h2>Ready to join the Mount Heaven family?</h2>
      <p><?= e(str_limit(setting('admission_info'), 150)) ?></p>
    </div>
    <a class="btn btn-gold btn-lg" href="<?= url('/admissions') ?>">Apply for Admission</a>
  </div>
</section>
