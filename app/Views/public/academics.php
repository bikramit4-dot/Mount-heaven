<?php $title = 'Academics'; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb"><a href="<?= url('/') ?>">Home</a> <span>/</span> Academics</p>
    <h1>Academics</h1>
    <p>Structured learning from Nursery to Grade X</p>
  </div>
</section>

<!-- Programs journey -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">What We Offer</p>
      <h2 class="section-title center">Our Academic Journey</h2>
      <p class="section-sub">Every stage builds on the one before it — from the first day in Nursery to board exams in Grade X.</p>
    </div>

    <div class="journey">
      <?php foreach ($programs as $i => $p): ?>
        <div class="journey-step reveal">
          <div class="journey-marker">
            <span class="journey-num"><?= $i + 1 ?></span>
            <?php if (!empty($p['photo'])): ?>
              <span class="timeline-photo"><img src="<?= e(upload_url($p['photo'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy"></span>
            <?php else: ?>
              <span class="journey-icon"><?= e($p['icon']) ?></span>
            <?php endif; ?>
          </div>
          <div class="journey-card">
            <h3><?= e($p['title']) ?></h3>
            <p class="grades"><?= e($p['grades']) ?></p>
            <p><?= e($p['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Facilities -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Everything Students Need</p>
      <h2 class="section-title center">Campus Facilities</h2>
      <p class="section-sub">A campus designed so students can study, play and explore safely every day.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($facilities as $f): ?>
        <div class="facility boxed reveal">
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

<!-- CTA -->
<section class="cta">
  <div class="container cta-inner">
    <div>
      <h2>Questions about the curriculum?</h2>
      <p>Our team is happy to walk you through the syllabus, assessments and activities.</p>
    </div>
    <a class="btn btn-gold btn-lg" href="<?= url('/contact') ?>">Get in Touch</a>
  </div>
</section>
