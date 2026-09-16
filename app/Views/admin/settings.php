<?php $errors = $errors ?? []; $old = $old ?? []; $groups = $groups ?? []; $settings = $settings ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<form method="post" action="<?php echo url('/admin/settings'); ?>" enctype="multipart/form-data" class="stack">
  <?php echo csrf_field(); ?>

  <?php foreach ($groups as $groupKey => $group): ?>
    <div class="admin-panel">
      <div class="panel-top"><h2><?php echo e($group[0]); ?></h2></div>

      <?php foreach ($group[1] as $key => $meta): ?>
        <?php [$label, $type] = $meta; ?>
        <?php if ($type === 'textarea'): ?>
          <div class="form-group">
            <label for="<?php echo e($key); ?>"><?php echo e($label); ?></label>
            <textarea id="<?php echo e($key); ?>" name="<?php echo e($key); ?>" rows="4"><?php echo e($settings[$key] ?? ''); ?></textarea>
          </div>
        <?php elseif ($type === 'image'): ?>
          <div class="form-group">
            <label><?php echo e($label); ?></label>
            <div class="upload-row">
              <?php if (!empty($settings[$key])): ?>
                <img class="thumb" src="<?php echo e(upload_url($settings[$key])); ?>" alt="">
                <input type="hidden" name="_current_<?php echo e($key); ?>" value="<?php echo e($settings[$key]); ?>">
              <?php endif; ?>
              <input type="file" name="<?php echo e($key); ?>" accept="image/*">
            </div>
          </div>
        <?php elseif ($type === 'password'): ?>
          <div class="form-group">
            <label for="<?php echo e($key); ?>"><?php echo e($label); ?></label>
            <input type="password" id="<?php echo e($key); ?>" name="<?php echo e($key); ?>" value="<?php echo e($settings[$key] ?? ''); ?>" autocomplete="new-password">
          </div>
        <?php else: ?>
          <div class="form-group">
            <label for="<?php echo e($key); ?>"><?php echo e($label); ?></label>
            <input type="text" id="<?php echo e($key); ?>" name="<?php echo e($key); ?>" value="<?php echo e($settings[$key] ?? ''); ?>">
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

  <div class="save-bar">
    <button type="submit" class="btn btn-gold btn-lg">💾 Save All Settings</button>
  </div>
</form>
