<?php if ($error = flash('error')): ?>
  <div class="alert alert-error">⚠️ <?php echo e($error); ?></div>
<?php endif; ?>

<form method="post" action="<?php echo url('/admin/login'); ?>" class="login-form">
  <?php echo csrf_field(); ?>
  <div class="form-group">
    <label for="login">Username or Email</label>
    <input type="text" id="login" name="login" value="<?php echo e(flash('old', '')); ?>" autofocus required>
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <div class="pw-wrap">
      <input type="password" id="password" name="password" required>
      <button type="button" class="pw-toggle" data-target="password">👁</button>
    </div>
  </div>
  <button type="submit" class="btn btn-gold btn-block">Sign In →</button>
</form>

<p class="login-help"><a href="<?php echo url('/'); ?>">← Back to website</a></p>
