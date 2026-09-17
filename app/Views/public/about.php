<?php $title = 'About Us'; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb"><a href="<?= url('/') ?>">Home</a> <span>/</span> About Us</p>
    <h1>About Us</h1>
    <p><?= e(setting('site_tagline')) ?></p>
  </div>
</section>

<!-- Our story + stats badge -->
<section class="section">
  <div class="container about-story">
    <div class="about-story-text reveal">
      <p class="eyebrow">Get to know us</p>
      <h2 class="section-title left">Our Story</h2>
      <p class="lead"><?= e(setting('about_history')) ?></p>

      <ul class="about-points">
        <li><span>✅</span> Caring, experienced and qualified teachers</li>
        <li><span>✅</span> Modern classrooms with smart learning tools</li>
        <li><span>✅</span> Sports, arts and cultural activities for every child</li>
        <li><span>✅</span> Safe, friendly and inclusive campus</li>
      </ul>

      <div class="about-actions">
        <a class="btn btn-primary" href="<?= url('/admissions') ?>">Admissions</a>
        <a class="btn btn-outline" href="<?= url('/contact') ?>">Contact Us</a>
      </div>
    </div>

    <aside class="about-badge reveal delay-1">
      <span class="about-badge-icon">🏔️</span>
      <strong><?= e(setting('stat_established', 'Since 1959')) ?></strong>
      <small><?= e(setting('stat_established_sub', 'Years of excellence')) ?></small>
      <div class="about-badge-row">
        <div><strong class="num" data-countup="<?= e(setting('stat_students', '800+')) ?>"><?= e(setting('stat_students', '800+')) ?></strong><small><?= e(setting('stat_students_sub', 'Happy students')) ?></small></div>
        <div><strong class="num" data-countup="<?= e(setting('stat_teachers', '35+')) ?>"><?= e(setting('stat_teachers', '35+')) ?></strong><small><?= e(setting('stat_teachers_sub', 'Expert teachers')) ?></small></div>
      </div>
    </aside>
  </div>
</section>

<!-- Mission & Vision -->
<section class="section section-alt">
  <div class="container">
    <p class="eyebrow center">What drives us</p>
    <h2 class="section-title center">Mission &amp; Vision</h2>
    <div class="grid grid-2-value">
      <article class="value-card reveal">
        <span class="value-icon">🎯</span>
        <h3>Our Mission</h3>
        <p><?= e(setting('about_mission')) ?></p>
      </article>
      <article class="value-card reveal delay-1">
        <span class="value-icon">🔭</span>
        <h3>Our Vision</h3>
        <p><?= e(setting('about_vision')) ?></p>
      </article>
    </div>
  </div>
</section>

<!-- Principal message -->
<section class="section">
  <div class="container">
    <article class="principal-card reveal">
      <div class="principal-card-photo">
        <?php if (setting('principal_photo')): ?>
          <img src="<?= e(upload_url(setting('principal_photo'))) ?>" alt="<?= e(setting('principal_name')) ?>">
        <?php else: ?>
          <div class="principal-placeholder">👩‍🏫</div>
        <?php endif; ?>
        <span class="principal-ring" aria-hidden="true"></span>
      </div>
      <div class="principal-card-body">
        <p class="eyebrow">From the Principal's Desk</p>
        <h2 class="section-title left"><?= e(setting('principal_name')) ?></h2>
        <p class="principal-role"><?= e(setting('principal_designation')) ?></p>
        <p class="quote">“<?= e(setting('principal_message')) ?>”</p>
        <div class="principal-sign">
          <span class="principal-sign-name"><?= e(setting('principal_name')) ?></span>
          <span class="principal-sign-role"><?= e(setting('principal_designation')) ?></span>
        </div>
      </div>
    </article>
  </div>
</section>

<!-- Faculty -->
<section class="section section-alt" id="teachers">
  <div class="container">
    <p class="eyebrow center">Meet Our Team</p>
    <h2 class="section-title center">Our Faculty</h2>
    <div class="grid grid-3">
      <?php foreach ($teachers as $t): ?>
        <article class="card teacher-card reveal">
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

<!-- CTA -->
<section class="cta">
  <div class="container cta-inner">
    <div>
      <h2>Want to know more?</h2>
      <p>Visit our campus or get in touch — we would love to show you around and answer your questions.</p>
    </div>
    <a class="btn btn-gold btn-lg" href="<?= url('/contact') ?>">Get in Touch</a>
  </div>
</section>
