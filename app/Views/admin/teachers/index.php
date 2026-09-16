<?php $teachers = $teachers ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="admin-panel">
  <div class="panel-top">
    <h2>Teachers (<?php echo count($teachers); ?>)</h2>
    <a class="btn btn-primary btn-sm" href="<?php echo url('/admin/teachers/create'); ?>">➕ Add Teacher</a>
  </div>

  <table class="table">
    <thead><tr><th>Photo</th><th>Name</th><th>Designation</th><th>Order</th><th>Active</th><th class="th-actions">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($teachers as $t): ?>
        <tr>
          <td>
            <?php if ($t['photo']): ?>
              <img class="thumb avatar" src="<?php echo e(upload_url($t['photo'])); ?>" alt="">
            <?php else: ?>
              <span class="thumb avatar avatar-ph"><?php echo e(mb_strtoupper(mb_substr($t['name'], 0, 1))); ?></span>
            <?php endif; ?>
          </td>
          <td><strong><?php echo e($t['name']); ?></strong><br><small class="muted"><?php echo e($t['qualification']); ?></small></td>
          <td class="muted"><?php echo e($t['designation']); ?></td>
          <td><?php echo (int) $t['sort_order']; ?></td>
          <td><?php echo $t['active'] ? '<span class="pill pill-green">Yes</span>' : '<span class="pill pill-gray">No</span>'; ?></td>
          <td class="row-actions">
            <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/teachers/' . (int) $t['id'] . '/edit'); ?>">✏️ Edit</a>
            <form method="post" action="<?php echo url('/admin/teachers/' . (int) $t['id'] . '/delete'); ?>" class="inline-form" data-confirm="Remove this teacher?">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-sm btn-danger-ghost">🗑</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
