<?php $item = $item ?? null; $errors = $errors ?? []; $old = $old ?? []; $label = $label ?? 'Item'; $section = $section ?? 'programs'; $hasGrades = ($section !== 'facilities'); ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>
<?php if (isset($errors['title'])): ?><div class="alert alert-error">⚠️ <?php echo e($errors['title'][0]); ?></div><?php endif; ?>

<form method="post" class="stack narrow-form"
      action="<?php echo $item ? url('/admin/' . $section . '/' . (int) $item['id']) : url('/admin/' . $section); ?>">
  <?php echo csrf_field(); ?>

  <div class="admin-panel">
    <div class="panel-top"><h2><?php echo $item ? '✏️ Edit' : '➕ Add'; ?> <?php echo e($label); ?></h2></div>

    <div class="form-group">
      <label for="title">Title *</label>
      <input type="text" id="title" name="title" required value="<?php echo e($old['title'] ?? $item['title'] ?? ''); ?>">
    </div>

    <?php if ($hasGrades): ?>
    <div class="form-group">
      <label for="grades">Grades / Subtitle</label>
      <input type="text" id="grades" name="grades" value="<?php echo e($old['grades'] ?? $item['grades'] ?? ''); ?>">
    </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="icon">Emoji Icon (used if no photo)</label>
      <input type="text" id="icon" name="icon" maxlength="12" placeholder="📚" value="<?php echo e($old['icon'] ?? $item['icon'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label>Photo (replaces the emoji on the website)</label>
      <div class="upload-row">
        <?php $currentPhoto = $old['photo'] ?? $item['photo'] ?? null; ?>
        <?php if ($currentPhoto): ?>
          <img class="thumb" src="<?php echo e(upload_url($currentPhoto)); ?>" alt="">
          <input type="hidden" name="_current_photo" value="<?php echo e($currentPhoto); ?>">
        <?php endif; ?>
        <input type="file" name="photo" accept="image/*">
      </div>
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="4"><?php echo e($old['description'] ?? $item['description'] ?? ''); ?></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="sort_order">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="<?php echo e((string) ($old['sort_order'] ?? $item['sort_order'] ?? '0')); ?>">
      </div>
      <div class="form-group checkbox-group">
        <label class="check">
          <input type="checkbox" name="active" <?php echo ($old['active'] ?? $item['active'] ?? '1') ? 'checked' : ''; ?>> Active (visible on website)
        </label>
      </div>
    </div>
  </div>

  <div class="save-bar">
    <button type="submit" class="btn btn-gold btn-lg">💾 Save</button>
    <a class="btn btn-ghost" href="<?php echo url('/admin/' . $section); ?>">Cancel</a>
  </div>
</form>
