<?php $sliders = $sliders ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="admin-panel">
  <div class="panel-top">
    <h2>Home Sliders (<?php echo count($sliders); ?>)</h2>
    <a class="btn btn-primary btn-sm" href="<?php echo url('/admin/sliders/create'); ?>">➕ Add Slider</a>
  </div>

  <?php if (!$sliders): ?>
    <p class="muted">No sliders yet. The homepage hero will be empty until you add one.</p>
  <?php endif; ?>

  <table class="table">
    <thead><tr><th>Preview</th><th>Title</th><th>Button</th><th>Order</th><th>Active</th><th class="th-actions">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($sliders as $s): ?>
        <tr>
          <td>
            <?php if ($s['image']): ?>
              <img class="thumb thumb-slider" src="<?php echo e(upload_url($s['image'])); ?>" alt="">
            <?php else: ?>
              <span class="thumb thumb-slider thumb-empty">—</span>
            <?php endif; ?>
          </td>
          <td><strong><?php echo e($s['title']); ?></strong><br><small class="muted"><?php echo e(str_limit($s['subtitle'], 60)); ?></small></td>
          <td class="muted"><?php echo e($s['button_text'] ?: '—'); ?></td>
          <td><?php echo (int) $s['sort_order']; ?></td>
          <td><?php echo $s['active'] ? '<span class="pill pill-green">Yes</span>' : '<span class="pill pill-gray">No</span>'; ?></td>
          <td class="row-actions">
            <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/sliders/' . (int) $s['id'] . '/edit'); ?>">✏️ Edit</a>
            <form method="post" action="<?php echo url('/admin/sliders/' . (int) $s['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this slider?">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-sm btn-danger-ghost">🗑</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
