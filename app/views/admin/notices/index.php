<?php $notices = $notices ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="admin-panel">
  <div class="panel-top">
    <h2>Notices (<?php echo count($notices); ?>)</h2>
    <a class="btn btn-primary btn-sm" href="<?php echo url('/admin/notices/create'); ?>">➕ Add Notice</a>
  </div>

  <div class="table-wrap">
  <table class="table">
    <thead><tr><th>Title</th><th>Published</th><th>Pinned</th><th class="th-actions">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($notices as $n): ?>
        <tr>
          <td><strong><?php echo e(str_limit($n['title'], 70)); ?></strong><br><small class="muted"><?php echo e(str_limit(strip_tags($n['body']), 80)); ?></small></td>
          <td class="muted"><?php echo e(format_date($n['published_at'])); ?></td>
          <td><?php echo $n['is_pinned'] ? '📌' : '—'; ?></td>
          <td class="row-actions">
            <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/notices/' . (int) $n['id'] . '/edit'); ?>">✏️ Edit</a>
            <form method="post" action="<?php echo url('/admin/notices/' . (int) $n['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this notice?">
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
