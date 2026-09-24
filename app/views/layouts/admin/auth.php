<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login — <?= e(setting('site_name')) ?></title>
<link rel="icon" href="<?= setting('site_logo') ? e(upload_url(setting('site_logo'))) : 'data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'><text y=\'.9em\' font-size=\'90\'>🏔️</text></svg>' ?>">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="auth-body">
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo"><?php if (setting('site_logo')): ?>
      <img src="<?= e(upload_url(setting('site_logo'))) ?>" alt="<?= e(setting('site_name')) ?>">
    <?php else: ?>🏔️<?php endif; ?></div>
    <h1><?= e(setting('site_name')) ?></h1>
    <p class="auth-sub">Administration Panel</p>
    <?= $content ?>
  </div>
  <p class="auth-foot">Protected area · Authorized staff only</p>
</div>
</body>
</html>
