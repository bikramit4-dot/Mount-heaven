<?php $banners = $banners ?? []; $edit = $edit ?? null; $errors = $errors ?? []; $old = $old ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<?php $isEdit = $edit !== null; ?>

<!-- Add / edit form -->
<div class="admin-panel">
  <div class="panel-top">
    <h2><?php echo $isEdit ? '✏️ Edit Popup Banner' : '➕ Add Popup Banner'; ?></h2>
    <?php if ($isEdit): ?><a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/popup'); ?>">Cancel edit</a><?php endif; ?>
  </div>

  <form method="post" enctype="multipart/form-data" class="stack"
        action="<?php echo $isEdit ? url('/admin/popup/' . (int) $edit['id']) : url('/admin/popup'); ?>">
    <?php echo csrf_field(); ?>

    <div class="form-row">
      <div class="form-group">
        <label for="title">Title (optional)</label>
        <input type="text" id="title" name="title" maxlength="150"
               value="<?php echo e($old['title'] ?? ($edit['title'] ?? '')); ?>"
               placeholder="e.g. Admission Open 2026-27">
      </div>
      <div class="form-group">
        <label for="duration">Show duration (seconds)</label>
        <input type="number" id="duration" name="duration" min="2" max="30"
               value="<?php echo e((string) ($old['duration'] ?? ($edit['duration'] ?? '5'))); ?>">
        <small class="muted">How long the popup stays before it disappears by itself (2–30).</small>
      </div>
    </div>

    <div class="form-group">
      <label for="link_url">Link when clicked (optional)</label>
      <input type="text" id="link_url" name="link_url" maxlength="255"
             value="<?php echo e($old['link_url'] ?? ($edit['link_url'] ?? '')); ?>"
             placeholder="/admissions or https://example.com">
    </div>

    <div class="form-group">
      <label for="image">Popup Image * <?php echo $isEdit ? '(leave empty to keep current)' : ''; ?></label>
      <div class="upload-row">
        <?php $current = $old['image'] ?? ($edit['image'] ?? null); ?>
        <?php if ($current): ?>
          <img class="thumb thumb-slider" src="<?php echo e(upload_url($current)); ?>" alt="">
          <input type="hidden" name="_current_photo" value="<?php echo e($current); ?>">
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/*" <?php echo $isEdit ? '' : 'required'; ?>>
      </div>
      <small class="muted">Recommended: a wide announcement image (e.g. 900×500). Shown centred with rounded corners.</small>
    </div>

    <div class="form-group checkbox-group">
      <label class="check">
        <input type="checkbox" name="active" <?php echo ($old['active'] ?? ($edit['active'] ?? '1')) ? 'checked' : ''; ?>>
        Active (show on the website when it opens)
      </label>
      <small class="muted">Only one popup can be active at a time — adding an active one replaces the previous.</small>
    </div>

    <div class="save-bar">
      <button type="submit" class="btn btn-gold btn-lg">💾 <?php echo $isEdit ? 'Update' : 'Add'; ?> Popup Banner</button>
    </div>
  </form>
</div>

<!-- Existing banners -->
<div class="admin-panel">
  <div class="panel-top"><h2>Popup Banners (<?php echo count($banners); ?>)</h2></div>

  <?php if (!$banners): ?>
    <p class="muted">No popup banners yet. Add one above and it will automatically show when the website opens.</p>
  <?php endif; ?>

  <table class="table">
    <thead><tr><th>Preview</th><th>Title</th><th>Link</th><th>Duration</th><th>Status</th><th class="th-actions">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($banners as $b): ?>
        <tr>
          <td><img class="thumb thumb-slider" src="<?php echo e(upload_url($b['image'])); ?>" alt=""></td>
          <td><strong><?php echo e($b['title'] ?: '—'); ?></strong></td>
          <td class="muted"><?php echo e($b['link_url'] ?: '—'); ?></td>
          <td><?php echo (int) $b['duration']; ?>s</td>
          <td><?php echo $b['active'] ? '<span class="pill pill-green">Live</span>' : '<span class="pill pill-gray">Hidden</span>'; ?></td>
          <td class="row-actions">
            <form method="post" action="<?php echo url('/admin/popup/' . (int) $b['id'] . '/toggle'); ?>" class="inline-form">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-sm btn-ghost"><?php echo $b['active'] ? '⏸ Hide' : '▶️ Show'; ?></button>
            </form>
            <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/popup?edit=' . (int) $b['id']); ?>">✏️ Edit</a>
            <form method="post" action="<?php echo url('/admin/popup/' . (int) $b['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this popup banner?">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-sm btn-danger-ghost">🗑</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
