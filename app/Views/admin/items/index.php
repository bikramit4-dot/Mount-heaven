<?php $rows = $rows ?? []; $label = $label ?? 'Item'; $section = $section ?? 'programs'; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="admin-panel">
  <div class="panel-top">
    <h2><?php echo e($label); ?>s (<?php echo count($rows); ?>)</h2>
    <a class="btn btn-primary btn-sm" href="<?php echo url('/admin/' . $section . '/create'); ?>">➕ Add <?php echo e($label); ?></a>
  </div>

  <?php if (!$rows): ?>
    <p class="muted">Nothing here yet — add the first one.</p>
  <?php endif; ?>

  <table class="table">
    <thead><tr><th>#</th><th>Icon</th><th>Title</th><th>Grades / Icon code</th><th>Order</th><th>Active</th><th class="th-actions">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?php echo (int) $r['id']; ?></td>
          <td><span class="big-ico"><?php echo e($r['icon']); ?></span></td>
          <td><strong><?php echo e($r['title']); ?></strong></td>
          <td class="muted"><?php echo e($r['grades']); ?></td>
          <td><?php echo (int) $r['sort_order']; ?></td>
          <td><?php echo $r['active'] ? '<span class="pill pill-green">Yes</span>' : '<span class="pill pill-gray">No</span>'; ?></td>
          <td class="row-actions">
            <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/' . $section . '/' . (int) $r['id'] . '/edit'); ?>">✏️ Edit</a>
            <form method="post" action="<?php echo url('/admin/' . $section . '/' . (int) $r['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this <?php echo e(strtolower($label)); ?>?">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-sm btn-danger-ghost">🗑</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
