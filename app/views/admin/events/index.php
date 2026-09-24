<?php $events = $events ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="admin-panel">
  <div class="panel-top">
    <h2>Events (<?php echo count($events); ?>)</h2>
    <a class="btn btn-primary btn-sm" href="<?php echo url('/admin/events/create'); ?>">➕ Add Event</a>
  </div>

  <div class="table-wrap">
  <table class="table">
    <thead><tr><th>Date</th><th>Event</th><th class="th-actions">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($events as $ev): ?>
        <tr>
          <td><span class="pill pill-blue"><?php echo e(format_date($ev['event_date'], 'M j, Y')); ?></span></td>
          <td><strong><?php echo e(str_limit($ev['title'], 70)); ?></strong><br><small class="muted"><?php echo e(str_limit($ev['description'], 90)); ?></small></td>
          <td class="row-actions">
            <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/events/' . (int) $ev['id'] . '/edit'); ?>">✏️ Edit</a>
            <form method="post" action="<?php echo url('/admin/events/' . (int) $ev['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this event?">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-sm btn-danger-ghost">🗑</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</div>
