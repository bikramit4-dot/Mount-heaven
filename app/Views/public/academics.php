<?php $title = 'Academics'; ?>

<section class="page-head">
  <div class="container">
    <h1>Academics</h1>
    <p>Structured learning from Nursery to Grade X</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="section-title center">Academic Programs</h2>
    <div class="timeline">
      <?php foreach ($programs as $p): ?>
        <div class="timeline-item">
          <?php if (!empty($p['photo'])): ?>
            <div class="timeline-photo"><img src="<?= e(upload_url($p['photo'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy"></div>
          <?php else: ?>
            <div class="timeline-icon"><?= e($p['icon']) ?></div>
          <?php endif; ?>
          <div class="timeline-body">
            <h3><?= e($p['title']) ?></h3>
            <p class="grades"><?= e($p['grades']) ?></p>
            <p><?= e($p['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <p class="eyebrow center">Everything Students Need</p>
    <h2 class="section-title center">Campus Facilities</h2>
    <div class="grid grid-3">
      <?php foreach ($facilities as $f): ?>
        <div class="facility boxed">
          <?php if (!empty($f['photo'])): ?>
            <span class="facility-photo"><img src="<?= e(upload_url($f['photo'])) ?>" alt="<?= e($f['title']) ?>" loading="lazy"></span>
          <?php else: ?>
            <span class="facility-icon"><?= e($f['icon']) ?></span>
          <?php endif; ?>
          <div>
            <h4><?= e($f['title']) ?></h4>
            <p><?= e($f['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta">
  <div class="container cta-inner">
    <div>
      <h2>Questions about the curriculum?</h2>
      <p>Our team is happy to walk you through the syllabus, assessments and activities.</p>
    </div>
    <a class="btn btn-gold btn-lg" href="<?= url('/contact') ?>">Get in Touch</a>
  </div>
</section>
