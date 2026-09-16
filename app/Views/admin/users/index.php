<?php $users = $users ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="admin-panel">
  <div class="panel-top">
    <h2>👥 Admin Users (<?php echo count($users); ?>)</h2>
    <a class="btn btn-primary btn-sm" href="<?php echo url('/admin/users/create'); ?>">➕ Add User</a>
  </div>

  <table class="table">
    <thead><tr><th>User</th><th>Role</th><th>Status</th><th>Last Login</th><th class="th-actions">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td>
            <strong><?php echo e($u['name']); ?></strong>
            <span class="muted">(<?php echo e($u['username']); ?>)</span><br>
            <small class="muted"><?php echo e($u['email']); ?></small>
          </td>
          <td><?php echo $u['role'] === 'admin' ? '<span class="pill pill-gold">admin</span>' : '<span class="pill pill-blue">editor</span>'; ?></td>
          <td><?php echo $u['active'] ? '<span class="pill pill-green">active</span>' : '<span class="pill pill-gray">disabled</span>'; ?></td>
          <td class="muted"><?php echo $u['last_login'] ? e(date('M j, Y g:i A', strtotime($u['last_login']))) : 'never'; ?></td>
          <td class="row-actions">
            <form method="post" action="<?php echo url('/admin/users/' . (int) $u['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this user?">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-sm btn-danger-ghost" <?php echo (int) $u['id'] === (int) \App\Core\Auth::user()['id'] ? 'disabled title="You cannot delete yourself"' : ''; ?>>🗑</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
