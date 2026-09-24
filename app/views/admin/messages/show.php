<?php $message = $message ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>

<div class="panel-top detail-top">
  <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/messages'); ?>">← All Messages</a>
  <div class="detail-status">
    <form method="post" action="<?php echo url('/admin/messages/' . (int) $message['id'] . '/' . ($message['is_read'] ? 'unread' : 'read')); ?>" class="inline-form">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-sm btn-ghost"><?php echo $message['is_read'] ? 'Mark unread' : '✓ Mark read'; ?></button>
    </form>
    <form method="post" action="<?php echo url('/admin/messages/' . (int) $message['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this message permanently?">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-sm btn-danger-ghost">🗑 Delete</button>
    </form>
  </div>
</div>

<div class="admin-panel detail-panel">
  <div class="detail-header">
    <span class="detail-avatar"><?php echo e(mb_strtoupper(mb_substr($message['name'], 0, 1))); ?></span>
    <div>
      <h2><?php echo e($message['name']); ?></h2>
      <p class="muted small">
        <?php echo $message['is_read'] ? 'Read' : 'Unread'; ?>
        · sent <?php echo e(format_date($message['created_at'], 'M j, Y g:i A')); ?>
      </p>
    </div>
  </div>

  <h3 class="detail-section-title">👤 Sender Details</h3>
  <table class="detail-table">
    <tr><th>Name</th><td><?php echo e($message['name']); ?></td></tr>
    <tr><th>Email</th><td><a href="mailto:<?php echo e($message['email']); ?>">✉️ <?php echo e($message['email']); ?></a></td></tr>
    <tr><th>Phone</th><td><?php echo $message['phone'] ? '<a href="tel:' . e($message['phone']) . '">📞 ' . e($message['phone']) . '</a>' : '—'; ?></td></tr>
    <tr><th>Subject</th><td><strong><?php echo $message['subject'] ? e($message['subject']) : '—'; ?></strong></td></tr>
  </table>

  <h3 class="detail-section-title">💬 Message</h3>
  <div class="detail-message"><?php echo nl2br(e($message['message'])); ?></div>

  <div class="detail-actions">
    <a class="btn btn-gold" href="mailto:<?php echo e($message['email']); ?>?subject=<?php echo rawurlencode('Re: ' . ($message['subject'] ?: 'Your message')); ?>">✉️ Reply by Email</a>
    <?php if ($message['phone']): ?><a class="btn btn-primary" href="tel:<?php echo e($message['phone']); ?>">📞 Call Sender</a><?php endif; ?>
  </div>
</div>
