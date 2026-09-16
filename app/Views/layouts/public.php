<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? setting('site_name')) ?> — <?= e(setting('site_tagline')) ?></title>
<meta name="description" content="<?= e(str_limit(setting('about_short'), 160)) ?>">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏔️</text></svg>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<header class="site-header">
  <div class="topbar">
    <div class="container topbar-inner">
      <?php if (setting('site_logo')): ?>
        <span class="topbar-logo"><img src="<?= e(upload_url(setting('site_logo'))) ?>" alt="<?= e(setting('site_name')) ?> logo"></span>
      <?php endif; ?>
      <span>📞 <?= e(setting('phone')) ?></span>
      <span>✉️ <?= e(setting('email')) ?></span>
      <?php if (setting('admission_open')): ?>
        <span class="topbar-badge">🎉 Admissions Open <?= e(date('Y')) ?>-<?= e(date('y') + 1) ?></span>
      <?php endif; ?>
    </div>
  </div>
  <nav class="navbar container">
    <a class="brand" href="<?= url('/') ?>">
      <span class="brand-logo"><?php if (setting('site_logo')): ?>
        <img src="<?= e(upload_url(setting('site_logo'))) ?>" alt="<?= e(setting('site_name')) ?>">
      <?php else: ?>🏔️<?php endif; ?></span>
      <span class="brand-text">
        <strong><?= e(setting('site_name')) ?></strong>
        <small><?= e(setting('site_tagline')) ?></small>
      </span>
    </a>
    <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">☰</button>
    <ul class="nav-links" id="navLinks">
      <li><a href="<?= url('/') ?>" class="<?= is_active_path('/') ? 'active' : '' ?>">Home</a></li>
      <li><a href="<?= url('/about') ?>" class="<?= is_active_path('about') ? 'active' : '' ?>">About</a></li>
      <li><a href="<?= url('/academics') ?>" class="<?= is_active_path('academics') ? 'active' : '' ?>">Academics</a></li>
      <li><a href="<?= url('/admissions') ?>" class="<?= is_active_path('admissions') ? 'active' : '' ?>">Admissions</a></li>
      <li><a href="<?= url('/gallery') ?>" class="<?= is_active_path('gallery') ? 'active' : '' ?>">Gallery</a></li>
      <li><a href="<?= url('/notices') ?>" class="<?= is_active_path('notices') ? 'active' : '' ?>">Notices</a></li>
      <li><a href="<?= url('/contact') ?>" class="<?= is_active_path('contact') ? 'active' : '' ?>">Contact</a></li>
      <li><a class="btn btn-gold btn-sm" href="<?= url('/admissions') ?>">Apply Now</a></li>
    </ul>
  </nav>
</header>

<main>
<?= $content ?>
</main>

<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <h3>🏔️ <?= e(setting('site_name')) ?></h3>
      <p><?= e(str_limit(setting('about_short'), 180)) ?></p>
      <div class="socials">
        <?php if (setting('facebook_url')): ?><a href="<?= e(setting('facebook_url')) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
        <?php if (setting('instagram_url')): ?><a href="<?= e(setting('instagram_url')) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
        <?php if (setting('youtube_url')): ?><a href="<?= e(setting('youtube_url')) ?>" target="_blank" rel="noopener">YouTube</a><?php endif; ?>
      </div>
    </div>
    <div>
      <h3>Quick Links</h3>
      <ul>
        <li><a href="<?= url('/about') ?>">About Us</a></li>
        <li><a href="<?= url('/academics') ?>">Academics</a></li>
        <li><a href="<?= url('/admissions') ?>">Admissions</a></li>
        <li><a href="<?= url('/gallery') ?>">Gallery</a></li>
        <li><a href="<?= url('/notices') ?>">Notices &amp; Events</a></li>
      </ul>
    </div>
    <div>
      <h3>Contact</h3>
      <ul class="contact-list">
        <li>📍 <?= e(setting('address')) ?></li>
        <li>📞 <?= e(setting('phone')) ?></li>
        <li>✉️ <?= e(setting('email')) ?></li>
        <li>🕘 <?= e(setting('office_hours')) ?></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <p><?= e(str_replace('{year}', (string) date('Y'), setting('footer_note'))) ?></p>
    </div>
  </div>
</footer>

<script src="<?= asset('js/public.js') ?>"></script>
</body>
</html>
