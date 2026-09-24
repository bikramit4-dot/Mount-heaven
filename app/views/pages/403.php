<?php $title = 'Access Denied'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>403 — Access Denied</title>
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="error-page dark">
<div class="error-box">
  <h1>403</h1>
  <p><?= e($message ?? 'You do not have permission to access this area.') ?></p>
  <a class="btn btn-gold" href="<?= url('/admin') ?>">← Back to Dashboard</a>
</div>
</body>
</html>
