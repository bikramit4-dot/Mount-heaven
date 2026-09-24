<?php $item = $item ?? null; $errors = $errors ?? []; $old = $old ?? []; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>
<?php if (isset($errors['name'])): ?><div class="alert alert-error">⚠️ <?php echo e($errors['name'][0]); ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" class="stack narrow-form"
      action="<?php echo $item ? url('/admin/teachers/' . (int) $item['id']) : url('/admin/teachers'); ?>">
  <?php echo csrf_field(); ?>

  <div class="admin-panel">
    <div class="panel-top"><h2><?php echo $item ? '✏️ Edit' : '➕ Add'; ?> Teacher</h2></div>

    <div class="form-group">
      <label for="name">Full Name *</label>
      <input type="text" id="name" name="name" required value="<?php echo e($old['name'] ?? $item['name'] ?? ''); ?>">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="designation">Designation / Subject</label>
        <input type="text" id="designation" name="designation" value="<?php echo e($old['designation'] ?? $item['designation'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label for="qualification">Qualification</label>
        <input type="text" id="qualification" name="qualification" value="<?php echo e($old['qualification'] ?? $item['qualification'] ?? ''); ?>">
      </div>
    </div>

    <div class="form-group">
      <label for="bio">Short Bio</label>
      <textarea id="bio" name="bio" rows="3"><?php echo e($old['bio'] ?? $item['bio'] ?? ''); ?></textarea>
    </div>

    <div class="form-group">
      <label for="photo">Photo</label>
      <div class="upload-row">
        <?php if (!empty($item['photo'])): ?>
          <img class="thumb avatar" src="<?php echo e(upload_url($item['photo'])); ?>" alt="">
        <?php endif; ?>
        <input type="file" id="photo" name="photo" accept="image/*">
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
    <button type="submit" class="btn btn-gold btn-lg">💾 Save Teacher</button>
    <a class="btn btn-ghost" href="<?php echo url('/admin/teachers'); ?>">Cancel</a>
  </div>
</form>
