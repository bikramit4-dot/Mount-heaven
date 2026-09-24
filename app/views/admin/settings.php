<?php /** @var string $tab @var array $groups @var array $settings */ ?>
<?php $group = $groups[$tab]; ?>

<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="page-intro">
  <h1>Site Settings</h1>
  <p>Everything here updates your live website instantly. Pick a section below — each one has its own save button.</p>
</div>

<!-- Section switcher -->
<nav class="tab-bar settings-tabs" aria-label="Settings sections">
  <?php foreach ($groups as $key => $g): ?>
    <a class="tab <?php echo $key === $tab ? 'is-active' : ''; ?>"
       href="<?php echo url('/admin/settings'); ?>?tab=<?php echo e($key); ?>"><?php echo e($g['title']); ?></a>
  <?php endforeach; ?>
</nav>

<div class="admin-panel settings-panel">
  <div class="panel-top">
    <h2><?php echo e($group['title']); ?></h2>
    <p class="panel-intro"><?php echo e($group['intro']); ?></p>
  </div>

  <form method="post" action="<?php echo url('/admin/settings'); ?>?tab=<?php echo e($tab); ?>"
        enctype="multipart/form-data" class="stack">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="tab" value="<?php echo e($tab); ?>">

    <?php foreach ($group['fields'] as $key => $meta): ?>
      <?php [$label, $type, $help] = $meta; ?>
      <?php $value = $settings[$key] ?? ''; ?>

      <?php if ($type === 'bool'): ?>
        <div class="form-group setting-row">
          <label class="check setting-check" for="<?php echo e($key); ?>">
            <input type="checkbox" id="<?php echo e($key); ?>" name="<?php echo e($key); ?>" value="1"
                   <?php echo ($value === '1' || $value === '') ? 'checked' : ''; ?>>
            <span>
              <strong><?php echo e($label); ?></strong>
              <small><?php echo e($help); ?></small>
            </span>
          </label>
        </div>

      <?php elseif ($type === 'image'): ?>
        <div class="form-group setting-row">
          <label><?php echo e($label); ?></label>
          <div class="upload-row setting-upload">
            <?php if ($value !== ''): ?>
              <img class="thumb thumb-lg" src="<?php echo e(upload_url($value)); ?>" alt="<?php echo e($label); ?>">
              <input type="hidden" name="_current_<?php echo e($key); ?>" value="<?php echo e($value); ?>">
            <?php else: ?>
              <span class="thumb thumb-lg thumb-empty">No image</span>
            <?php endif; ?>
            <div class="upload-controls">
              <input type="file" name="<?php echo e($key); ?>" accept="image/*">
              <small><?php echo e($help); ?></small>
            </div>
          </div>
        </div>

      <?php else: ?>
        <?php
          $inputType = match ($type) {
              'email'    => 'email',
              'url'      => 'url',
              'number'   => 'number',
              'password' => 'password',
              default    => 'text',
          };
        ?>
        <div class="form-group setting-row">
          <label for="<?php echo e($key); ?>"><?php echo e($label); ?></label>
          <?php if ($type === 'textarea'): ?>
            <textarea id="<?php echo e($key); ?>" name="<?php echo e($key); ?>" rows="4"
                      placeholder="<?php echo e($label); ?>"><?php echo e($value); ?></textarea>
          <?php else: ?>
            <input type="<?php echo e($inputType); ?>" id="<?php echo e($key); ?>" name="<?php echo e($key); ?>"
                   value="<?php echo e($value); ?>" placeholder="<?php echo e($help); ?>"
                   <?php if ($type === 'password'): ?>autocomplete="new-password"<?php endif; ?>>
          <?php endif; ?>
          <small class="help-text"><?php echo e($help); ?></small>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>

    <div class="save-bar">
      <button type="submit" class="btn btn-gold btn-lg">💾 Save <?php echo e(trim(str_replace(['🏷️', '👤', '📊', '📞', '🌐', '✉️'], '', $group['title']))); ?> Settings</button>
      <span class="muted">Changes appear on the website immediately.</span>
      <a class="btn btn-ghost" href="<?php echo url('/'); ?>" target="_blank">👁 View Website</a>
    </div>
  </form>
</div>
