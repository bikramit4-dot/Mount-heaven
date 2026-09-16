<?php $user = \App\Core\Auth::user(); $errors = $errors ?? []; $old = $old ?? []; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>
<?php foreach ($errors as $fieldErrors): ?>
  <div class="alert alert-error">⚠️ <?php echo e($fieldErrors[0]); ?></div>
<?php endforeach; ?>

<form method="post" class="stack narrow-form" action="<?php echo url('/admin/profile'); ?>">
  <?php echo csrf_field(); ?>

  <div class="admin-panel">
    <div class="panel-top"><h2>👤 My Profile</h2></div>

    <div class="form-row">
      <div class="form-group">
        <label>Username</label>
        <input type="text" value="<?php echo e($user['username']); ?>" disabled>
      </div>
      <div class="form-group">
        <label>Role</label>
        <input type="text" value="<?php echo e(ucfirst($user['role'])); ?>" disabled>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="name">Full Name *</label>
        <input type="text" id="name" name="name" required value="<?php echo e($old['name'] ?? $user['name']); ?>">
      </div>
      <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required value="<?php echo e($old['email'] ?? $user['email']); ?>">
      </div>
    </div>
  </div>

  <div class="admin-panel">
    <div class="panel-top"><h2>🔑 Change Password</h2></div>
    <p class="muted" style="margin-bottom:1rem">Leave these fields empty to keep your current password. Changing it will sign you out.</p>

    <div class="form-group">
      <label for="current_password">Current Password</label>
      <input type="password" id="current_password" name="current_password" autocomplete="current-password">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="new_password">New Password (min 8 chars)</label>
        <input type="password" id="new_password" name="new_password" minlength="8" autocomplete="new-password">
      </div>
      <div class="form-group">
        <label for="new_password_confirmation">Confirm New Password</label>
        <input type="password" id="new_password_confirmation" name="new_password_confirmation" minlength="8" autocomplete="new-password">
      </div>
    </div>
  </div>

  <div class="save-bar">
    <button type="submit" class="btn btn-gold btn-lg">💾 Save Profile</button>
  </div>
</form>
