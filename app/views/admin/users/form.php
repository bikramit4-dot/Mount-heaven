<?php $errors = $errors ?? []; $old = $old ?? []; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>
<?php foreach ($errors as $fieldErrors): ?>
  <div class="alert alert-error">⚠️ <?php echo e($fieldErrors[0]); ?></div>
<?php endforeach; ?>

<form method="post" class="stack narrow-form" action="<?php echo url('/admin/users'); ?>">
  <?php echo csrf_field(); ?>

  <div class="admin-panel">
    <div class="panel-top"><h2>➕ Add Admin User</h2></div>

    <div class="form-row">
      <div class="form-group">
        <label for="name">Full Name *</label>
        <input type="text" id="name" name="name" required value="<?php echo e($old['name'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label for="username">Username *</label>
        <input type="text" id="username" name="username" required value="<?php echo e($old['username'] ?? ''); ?>">
      </div>
    </div>

    <div class="form-group">
      <label for="email">Email *</label>
      <input type="email" id="email" name="email" required value="<?php echo e($old['email'] ?? ''); ?>">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="password">Password * (min 8 characters)</label>
        <input type="password" id="password" name="password" required minlength="8">
      </div>
      <div class="form-group">
        <label for="password_confirmation">Confirm Password *</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role">
          <option value="editor">Editor — manage content only</option>
          <option value="admin">Admin — full access incl. users</option>
        </select>
      </div>
      <div class="form-group checkbox-group">
        <label class="check">
          <input type="checkbox" name="active" checked> Active
        </label>
      </div>
    </div>
  </div>

  <div class="save-bar">
    <button type="submit" class="btn btn-gold btn-lg">💾 Create User</button>
    <a class="btn btn-ghost" href="<?php echo url('/admin/users'); ?>">Cancel</a>
  </div>
</form>
