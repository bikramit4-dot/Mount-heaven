<?php $title = 'About Us'; ?>

<section class="page-head">
  <div class="container">
    <h1>About Us</h1>
    <p><?= e(setting('site_tagline')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container narrow">
    <h2 class="section-title">Our Story</h2>
    <p class="lead"><?= e(setting('about_history')) ?></p>
  </div>
</section>

<section class="section section-alt">
  <div class="container two-col">
    <div class="value-card">
      <span class="value-icon">🎯</span>
      <h3>Our Mission</h3>
      <p><?= e(setting('about_mission')) ?></p>
    </div>
    <div class="value-card">
      <span class="value-icon">🔭</span>
      <h3>Our Vision</h3>
      <p><?= e(setting('about_vision')) ?></p>
    </div>
  </div>
</section>

<!-- Principal message -->
<section class="section">
  <div class="container split">
    <div class="principal-photo">
      <?php if (setting('principal_photo')): ?>
        <img src="<?= e(upload_url(setting('principal_photo'))) ?>" alt="<?= e(setting('principal_name')) ?>">
      <?php else: ?>
        <div class="principal-placeholder">👩‍🏫</div>
      <?php endif; ?>
    </div>
    <div>
      <p class="eyebrow">From the Principal's Desk</p>
      <h2 class="section-title left"><?= e(setting('principal_name')) ?></h2>
      <p class="lead" style="margin-bottom:.75rem"><?= e(setting('principal_designation')) ?></p>
      <p class="quote">“<?= e(setting('principal_message')) ?>”</p>
    </div>
  </div>
</section>

<!-- Teachers -->
<section class="section section-alt" id="teachers">
  <div class="container">
    <p class="eyebrow center">Meet Our Team</p>
    <h2 class="section-title center">Our Faculty</h2>
    <div class="grid grid-3">
      <?php foreach ($teachers as $t): ?>
        <article class="card teacher-card">
          <div class="teacher-photo">
            <?php if ($t['photo']): ?>
              <img src="<?= e(upload_url($t['photo'])) ?>" alt="<?= e($t['name']) ?>">
            <?php else: ?>
              <span><?= e(mb_strtoupper(mb_substr($t['name'], 0, 1))) ?></span>
            <?php endif; ?>
          </div>
          <h3><?= e($t['name']) ?></h3>
          <p class="teacher-role"><?= e($t['designation']) ?></p>
          <p class="teacher-qual"><?= e($t['qualification']) ?></p>
          <?php if ($t['bio']): ?><p class="teacher-bio"><?= e($t['bio']) ?></p><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
