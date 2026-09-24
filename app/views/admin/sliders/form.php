<?php $item = $item ?? null; $errors = $errors ?? []; $old = $old ?? []; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>
<?php if (isset($errors['title'])): ?><div class="alert alert-error">⚠️ <?php echo e($errors['title'][0]); ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" class="stack narrow-form"
      action="<?php echo $item ? url('/admin/sliders/' . (int) $item['id']) : url('/admin/sliders'); ?>">
  <?php echo csrf_field(); ?>

  <div class="admin-panel">
    <div class="panel-top"><h2><?php echo $item ? '✏️ Edit' : '➕ Add'; ?> Slider</h2></div>

    <div class="form-group">
      <label for="title">Title *</label>
      <input type="text" id="title" name="title" required value="<?php echo e($old['title'] ?? $item['title'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label for="subtitle">Subtitle</label>
      <input type="text" id="subtitle" name="subtitle" value="<?php echo e($old['subtitle'] ?? $item['subtitle'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label for="image">Background Image * <?php echo $item ? '(leave empty to keep current)' : ''; ?></label>
      <div class="upload-row">
        <?php if (!empty($item['image'])): ?>
          <img class="thumb thumb-slider" src="<?php echo e(upload_url($item['image'])); ?>" alt="">
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/*" <?php echo $item ? '' : 'required'; ?>>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="button_text">Button Text</label>
        <input type="text" id="button_text" name="button_text" value="<?php echo e($old['button_text'] ?? $item['button_text'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label for="button_url">Button URL</label>
        <input type="text" id="button_url" name="button_url" value="<?php echo e($old['button_url'] ?? $item['button_url'] ?? ''); ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="sort_order">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="<?php echo e((string) ($old['sort_order'] ?? $item['sort_order'] ?? '0')); ?>">
      </div>
      <div class="form-group checkbox-group">
        <label class="check">
          <input type="checkbox" name="active" <?php echo ($old['active'] ?? $item['active'] ?? '1') ? 'checked' : ''; ?>> Active
        </label>
      </div>
    </div>
  </div>

  <div class="save-bar">
    <button type="submit" class="btn btn-gold btn-lg">💾 Save Slider</button>
    <a class="btn btn-ghost" href="<?php echo url('/admin/sliders'); ?>">Cancel</a>
  </div>
</form>
