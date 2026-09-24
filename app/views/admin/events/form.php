<?php $item = $item ?? null; $errors = $errors ?? []; $old = $old ?? []; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>
<?php if (isset($errors['title'])): ?><div class="alert alert-error">⚠️ <?php echo e($errors['title'][0]); ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" class="stack narrow-form"
      action="<?php echo $item ? url('/admin/events/' . (int) $item['id']) : url('/admin/events'); ?>">
  <?php echo csrf_field(); ?>

  <div class="admin-panel">
    <div class="panel-top"><h2><?php echo $item ? '✏️ Edit' : '➕ Add'; ?> Event</h2></div>

    <div class="form-group">
      <label for="title">Event Title *</label>
      <input type="text" id="title" name="title" required value="<?php echo e($old['title'] ?? $item['title'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label for="event_date">Event Date *</label>
      <input type="date" id="event_date" name="event_date" required
             value="<?php echo e($old['event_date'] ?? ($item['event_date'] ?? date('Y-m-d'))); ?>">
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="4"><?php echo e($old['description'] ?? $item['description'] ?? ''); ?></textarea>
    </div>

    <div class="form-group">
      <label for="image">Event Image (optional)</label>
      <div class="upload-row">
        <?php if (!empty($item['image'])): ?>
          <img class="thumb" src="<?php echo e(upload_url($item['image'])); ?>" alt="">
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/*">
      </div>
    </div>
  </div>

  <div class="save-bar">
    <button type="submit" class="btn btn-gold btn-lg">💾 Save Event</button>
    <a class="btn btn-ghost" href="<?php echo url('/admin/events'); ?>">Cancel</a>
  </div>
</form>
