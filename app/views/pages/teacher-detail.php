<?php $title = $teacher['name']; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb">
      <a href="<?= url('/') ?>">Home</a> <span>/</span>
      <a href="<?= url('/about') ?>#teachers">About Us</a> <span>/</span> Faculty
    </p>
    <h1>👩‍🏫 Our Faculty</h1>
    <p>Meet the team behind <?= e(setting('site_name')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <!-- Teacher profile -->
    <article class="teacher-profile reveal">
      <div class="teacher-profile-photo">
        <span class="ph-fallback"><?= e(mb_strtoupper(mb_substr($teacher['name'], 0, 1))) ?></span>
        <?php if ($teacher['photo']): ?>
          <img src="<?= e(upload_url($teacher['photo'])) ?>" alt="<?= e($teacher['name']) ?>" onerror="this.remove()">
        <?php endif; ?>
      </div>
      <div class="teacher-profile-body">
        <h1 class="detail-title"><?= e($teacher['name']) ?></h1>
        <?php if ($teacher['designation']): ?>
          <p class="teacher-role"><?= e($teacher['designation']) ?></p>
        <?php endif; ?>
        <?php if ($teacher['qualification']): ?>
          <p class="teacher-qual">🎓 <?= e($teacher['qualification']) ?></p>
        <?php endif; ?>
      </div>
    </article>

    <div class="detail-body reveal">
      <h2>About <?= e($teacher['name']) ?></h2>
      <p><?= nl2br(e($teacher['bio'] ?: "{$teacher['name']} is part of the teaching team at " . setting('site_name') . '. Full profile coming soon.')) ?></p>
    </div>

    <div class="detail-actions mt">
      <a class="btn btn-outline" href="<?= url('/about') ?>#teachers">← Back to Our Faculty</a>
      <a class="btn btn-primary" href="<?= url('/contact') ?>">Get in Touch</a>
    </div>
  </div>
</section>

<?php if ($teachers): ?>
<!-- Full team grid -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Meet the rest</p>
      <h2 class="section-title center">Our Full Team</h2>
      <p class="section-sub">Click any member to see their full profile.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($teachers as $t): ?>
        <a class="teacher-card reveal" href="<?= url('/teachers/' . (int) $t['id']) ?>">
          <div class="teacher-photo">
            <span class="ph-fallback"><?= e(mb_strtoupper(mb_substr($t['name'], 0, 1))) ?></span>
            <?php if ($t['photo']): ?>
              <img src="<?= e(upload_url($t['photo'])) ?>" alt="<?= e($t['name']) ?>" loading="lazy" onerror="this.remove()">
            <?php endif; ?>
            <span class="teacher-view-pill">View profile</span>
          </div>
          <h3><?= e($t['name']) ?></h3>
          <p class="teacher-role"><?= e($t['designation']) ?></p>
          <p class="teacher-qual"><?= e($t['qualification']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
