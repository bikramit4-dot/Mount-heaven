<?php $messages = $messages ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="admin-panel">
  <div class="panel-top">
    <h2>💬 Contact Messages (<?php echo count($messages); ?>)</h2>
  </div>

  <?php if (!$messages): ?>
    <p class="muted">No messages received yet.</p>
  <?php endif; ?>

  <?php foreach ($messages as $m): ?>
    <div class="msg-card <?php echo $m['is_read'] ? 'read' : 'unread'; ?>">
      <div class="msg-head">
        <div>
          <strong><?php echo e($m['name']); ?></strong>
          <span class="muted"> &lt;<?php echo e($m['email']); ?>&gt;</span>
          <?php if ($m['phone']): ?><span class="muted"> · 📞 <?php echo e($m['phone']); ?></span><?php endif; ?>
          <?php if (!$m['is_read']): ?><b class="badge">new</b><?php endif; ?>
        </div>
        <span class="muted msg-date"><?php echo e(date('M j, Y g:i A', strtotime($m['created_at']))); ?></span>
      </div>
      <?php if ($m['subject']): ?><p class="msg-subject"><strong><?php echo e($m['subject']); ?></strong></p><?php endif; ?>
      <p class="msg-body"><?php echo nl2br(e($m['message'])); ?></p>
      <div class="msg-actions">
        <?php if ($m['is_read']): ?>
          <form method="post" action="<?php echo url('/admin/messages/' . (int) $m['id'] . '/unread'); ?>" class="inline-form">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm btn-ghost">Mark unread</button>
          </form>
        <?php else: ?>
          <form method="post" action="<?php echo url('/admin/messages/' . (int) $m['id'] . '/read'); ?>" class="inline-form">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm btn-primary">✓ Mark read</button>
          </form>
        <?php endif; ?>
        <a class="btn btn-sm btn-ghost" href="mailto:<?php echo e($m['email']); ?>">Reply by email</a>
        <form method="post" action="<?php echo url('/admin/messages/' . (int) $m['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this message?">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-sm btn-danger-ghost">🗑 Delete</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>
