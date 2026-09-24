<?php $item = $item ?? null; $errors = $errors ?? []; $old = $old ?? []; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>
<?php if (isset($errors['title'])): ?><div class="alert alert-error">⚠️ <?php echo e($errors['title'][0]); ?></div><?php endif; ?>

<form method="post" class="stack narrow-form"
      action="<?php echo $item ? url('/admin/notices/' . (int) $item['id']) : url('/admin/notices'); ?>">
  <?php echo csrf_field(); ?>

  <div class="admin-panel">
    <div class="panel-top"><h2><?php echo $item ? '✏️ Edit' : '➕ Add'; ?> Notice</h2></div>

    <div class="form-group">
      <label for="title">Title *</label>
      <input type="text" id="title" name="title" required value="<?php echo e($old['title'] ?? $item['title'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label for="body">Notice Text *</label>
      <textarea id="body" name="body" rows="7" required><?php echo e($old['body'] ?? $item['body'] ?? ''); ?></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="published_at">Publish Date</label>
        <input type="datetime-local" id="published_at" name="published_at"
               value="<?php echo e(date('Y-m-d\TH:i', strtotime($old['published_at'] ?? $item['published_at'] ?? 'now'))); ?>">
      </div>
      <div class="form-group checkbox-group">
        <label class="check">
          <input type="checkbox" name="is_pinned" <?php echo ($old['is_pinned'] ?? $item['is_pinned'] ?? '0') ? 'checked' : ''; ?>> 📌 Pin to top
        </label>
      </div>
    </div>
  </div>

  <div class="save-bar">
    <button type="submit" class="btn btn-gold btn-lg">💾 Save Notice</button>
    <a class="btn btn-ghost" href="<?php echo url('/admin/notices'); ?>">Cancel</a>
  </div>
</form>
