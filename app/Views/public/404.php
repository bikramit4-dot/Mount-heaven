<?php $title = 'Page Not Found'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>404 — Page Not Found</title>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="error-page">
<div class="error-box">
  <h1>404</h1>
  <p><?= e($message ?? 'Sorry, the page you are looking for does not exist.') ?></p>
  <a class="btn btn-primary" href="<?= url('/') ?>">← Back to Home</a>
</div>
</body>
</html>
