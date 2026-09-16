<div class="stat-grid">
  <a class="stat-card" href="<?php echo url('/admin/notices'); ?>">
    <span class="stat-icon">📢</span>
    <div><strong><?php echo (int) $stats['notices']; ?></strong><small>Notices</small></div>
  </a>
  <a class="stat-card" href="<?php echo url('/admin/events'); ?>">
    <span class="stat-icon">📅</span>
    <div><strong><?php echo (int) $stats['events']; ?></strong><small>Events</small></div>
  </a>
  <a class="stat-card" href="<?php echo url('/admin/teachers'); ?>">
    <span class="stat-icon">👩‍🏫</span>
    <div><strong><?php echo (int) $stats['teachers']; ?></strong><small>Teachers</small></div>
  </a>
  <a class="stat-card" href="<?php echo url('/admin/gallery'); ?>">
    <span class="stat-icon">📷</span>
    <div><strong><?php echo (int) $stats['photos']; ?></strong><small>Gallery Photos</small></div>
  </a>
  <a class="stat-card" href="<?php echo url('/admin/sliders'); ?>">
    <span class="stat-icon">🖼️</span>
    <div><strong><?php echo (int) $stats['sliders']; ?></strong><small>Home Sliders</small></div>
  </a>
  <a class="stat-card <?php echo $stats['messages'] > 0 ? 'has-unread' : ''; ?>" href="<?php echo url('/admin/messages'); ?>">
    <span class="stat-icon">💬</span>
    <div><strong><?php echo (int) $stats['messages']; ?></strong><small>Contact Messages</small></div>
  </a>
</div>

<div class="admin-grid-2">
  <div class="admin-panel">
    <div class="panel-top">
      <h2>💬 Latest Messages</h2>
      <a class="see-all" href="<?php echo url('/admin/messages'); ?>">View all</a>
    </div>
    <?php if (!$latestMessages): ?>
      <p class="muted">No messages yet.</p>
    <?php endif; ?>
    <?php foreach ($latestMessages as $m): ?>
      <div class="mini-row <?php echo $m['is_read'] ? '' : 'unread'; ?>">
        <div>
          <strong><?php echo e($m['name']); ?></strong>
          <span class="muted"> · <?php echo e(date('M j, g:i A', strtotime($m['created_at']))); ?></span>
          <?php if (!$m['is_read']): ?><b class="badge">new</b><?php endif; ?>
          <p class="muted"><?php echo e(str_limit($m['message'], 90)); ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="admin-panel">
    <div class="panel-top">
      <h2>📢 Recent Notices</h2>
      <a class="see-all" href="<?php echo url('/admin/notices'); ?>">Manage</a>
    </div>
    <?php if (!$latestNotices): ?>
      <p class="muted">No notices yet.</p>
    <?php endif; ?>
    <?php foreach ($latestNotices as $n): ?>
      <div class="mini-row">
        <div>
          <strong><?php echo e($n['title']); ?></strong>
          <span class="muted"> · <?php echo e(format_date($n['published_at'])); ?></span>
          <p class="muted"><?php echo e(str_limit($n['body'], 90)); ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="admin-panel">
  <div class="panel-top"><h2>⚡ Quick Actions</h2></div>
  <div class="quick-actions">
    <a class="quick-btn" href="<?php echo url('/admin/notices/create'); ?>">➕ Add Notice</a>
    <a class="quick-btn" href="<?php echo url('/admin/events/create'); ?>">➕ Add Event</a>
    <a class="quick-btn" href="<?php echo url('/admin/teachers/create'); ?>">➕ Add Teacher</a>
    <a class="quick-btn" href="<?php echo url('/admin/gallery'); ?>">📷 Upload Photos</a>
    <a class="quick-btn" href="<?php echo url('/admin/settings'); ?>">⚙️ Site Settings</a>
    <a class="quick-btn" href="<?php echo url('/admin/backup'); ?>">💾 Backup Now</a>
  </div>
</div>
