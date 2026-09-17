<?php
/** @var array $user */
/** @var int $unreadCount */
/** @var string $activeSection */
/** @var string $tab */
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($title ?? 'Dashboard'); ?> — Admin · <?php echo e(setting('site_name')); ?></title>
<link rel="stylesheet" href="<?php echo asset('css/admin.css'); ?>">
</head>
<body class="admin-body">

<div class="admin-shell">

  <aside class="sidebar" id="sidebar">
    <div class="side-brand">
      <span class="side-logo"><?php if (setting('site_logo')): ?>
        <img src="<?php echo e(upload_url(setting('site_logo'))); ?>" alt="<?php echo e(setting('site_name')); ?>">
      <?php else: ?>🏔️<?php endif; ?></span>
      <span>
        <strong><?php echo e(setting('site_name')); ?></strong>
        <small>Admin Panel</small>
      </span>
    </div>

    <nav class="side-nav">
      <a href="<?php echo url('/admin'); ?>" class="<?php echo $activeSection === 'dashboard' ? 'active' : ''; ?>">
        <span>📊</span> Dashboard
      </a>

      <p class="side-label">Content</p>
      <a href="<?php echo url('/admin/settings'); ?>" class="<?php echo $activeSection === 'settings' ? 'active' : ''; ?>">
        <span>⚙️</span> Site Settings
      </a>
      <a href="<?php echo url('/admin/sliders'); ?>" class="<?php echo $activeSection === 'sliders' ? 'active' : ''; ?>">
        <span>🖼️</span> Home Sliders
      </a>
      <a href="<?php echo url('/admin/popup'); ?>" class="<?php echo $activeSection === 'popup' ? 'active' : ''; ?>">
        <span>🔔</span> Popup Banner
      </a>
      <a href="<?php echo url('/admin/notices'); ?>" class="<?php echo $activeSection === 'notices' ? 'active' : ''; ?>">
        <span>📢</span> Notices
      </a>
      <a href="<?php echo url('/admin/events'); ?>" class="<?php echo $activeSection === 'events' ? 'active' : ''; ?>">
        <span>📅</span> Events
      </a>
      <a href="<?php echo url('/admin/programs'); ?>" class="<?php echo $activeSection === 'programs' ? 'active' : ''; ?>">
        <span>📚</span> Programs
      </a>
      <a href="<?php echo url('/admin/facilities'); ?>" class="<?php echo $activeSection === 'facilities' ? 'active' : ''; ?>">
        <span>🏫</span> Facilities
      </a>
      <a href="<?php echo url('/admin/teachers'); ?>" class="<?php echo $activeSection === 'teachers' ? 'active' : ''; ?>">
        <span>👩‍🏫</span> Teachers
      </a>
      <a href="<?php echo url('/admin/gallery'); ?>" class="<?php echo $activeSection === 'gallery' ? 'active' : ''; ?>">
        <span>📷</span> Gallery
      </a>

      <p class="side-label">Communication</p>
      <a href="<?php echo url('/admin/admissions'); ?>" class="<?php echo $activeSection === 'admissions' ? 'active' : ''; ?>">
        <span>📝</span> Admissions
      </a>
      <a href="<?php echo url('/admin/messages'); ?>" class="<?php echo $activeSection === 'messages' ? 'active' : ''; ?>">
        <span>💬</span> Messages
        <?php if ($unreadCount > 0): ?><b class="badge"><?php echo (int) $unreadCount; ?></b><?php endif; ?>
      </a>

      <p class="side-label">System</p>
      <a href="<?php echo url('/admin/backup'); ?>?tab=backup" class="side-tab <?php echo $activeSection === 'backup' && $tab === 'backup' ? 'active' : ''; ?>">
        <span>💾</span> Backup
      </a>
      <a href="<?php echo url('/admin/backup'); ?>?tab=restore" class="side-tab <?php echo $activeSection === 'backup' && $tab === 'restore' ? 'active' : ''; ?>">
        <span>♻️</span> Restore
      </a>
      <a href="<?php echo url('/admin/users'); ?>" class="<?php echo $activeSection === 'users' ? 'active' : ''; ?>">
        <span>👥</span> Users
      </a>
      <a href="<?php echo url('/admin/profile'); ?>" class="<?php echo $activeSection === 'profile' ? 'active' : ''; ?>">
        <span>🔑</span> My Profile
      </a>
    </nav>

    <div class="side-foot">
      <div class="side-user">
        <span class="side-avatar"><?php echo e(mb_strtoupper(mb_substr($user['name'], 0, 1))); ?></span>
        <span>
          <strong><?php echo e($user['name']); ?></strong>
          <small><?php echo e(ucfirst($user['role'])); ?></small>
        </span>
      </div>
      <form method="post" action="<?php echo url('/admin/logout'); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-block btn-logout">Logout</button>
      </form>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="admin-burger" id="adminBurger" aria-label="Menu">☰</button>
      <h1 class="admin-page-title"><?php echo e($title ?? 'Dashboard'); ?></h1>
      <div class="topbar-right">
        <a href="<?php echo url('/'); ?>" target="_blank" class="btn btn-sm btn-ghost">🌐 View Site</a>
      </div>
    </header>

    <main class="admin-content">
      <?php echo $content; ?>
    </main>

    <footer class="admin-footer">
      <p>© <?php echo date('Y'); ?> <?php echo e(setting('site_name')); ?> · Admin Panel</p>
    </footer>
  </div>

</div>

<script src="<?php echo asset('js/admin.js'); ?>"></script>
</body>
</html>
