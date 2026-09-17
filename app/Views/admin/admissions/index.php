<?php $enquiries = $enquiries ?? []; $status = $status ?? 'all'; $newCount = $newCount ?? 0; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="filter-tabs">
  <?php foreach (['all' => 'All', 'new' => 'New', 'contacted' => 'Contacted', 'enrolled' => 'Enrolled', 'closed' => 'Closed'] as $key => $label): ?>
    <a class="filter-tab <?php echo $status === $key ? 'active' : ''; ?>" href="<?php echo url('/admin/admissions'); ?>?status=<?php echo e($key); ?>">
      <?php echo e($label); ?><?php if ($key === 'new' && $newCount > 0): ?> <b class="badge"><?php echo (int) $newCount; ?></b><?php endif; ?>
    </a>
  <?php endforeach; ?>
</div>

<?php if (!$enquiries): ?>
  <div class="admin-panel"><p class="muted">No admission enquiries yet.</p></div>
<?php endif; ?>

<?php foreach ($enquiries as $q): ?>
  <div class="admin-panel enquiry-card">
    <div class="enquiry-head">
      <div>
        <h3><a class="row-link" href="<?php echo url('/admin/admissions/' . (int) $q['id']); ?>"><?php echo e($q['student_name']); ?></a> <span class="muted small">· Grade <?php echo e($q['grade_applying']); ?></span></h3>
        <p class="muted small">
          <?php echo $q['status'] === 'new' ? '🆕 ' : ''; ?>
          <?php echo e(ucfirst($q['status'])); ?> · submitted <?php echo e(format_date($q['created_at'])); ?>
          <?php if ($q['dob']): ?> · DOB <?php echo e(format_date($q['dob'])); ?><?php endif; ?>
          <?php if ($q['gender']): ?> · <?php echo e(ucfirst($q['gender'])); ?><?php endif; ?>
        </p>
      </div>
      <form method="post" action="<?php echo url('/admin/admissions/' . (int) $q['id'] . '/status'); ?>" class="status-form">
        <?php echo csrf_field(); ?>
        <select name="status" onchange="this.form.submit()">
          <?php foreach (['new' => 'New', 'contacted' => 'Contacted', 'enrolled' => 'Enrolled', 'closed' => 'Closed'] as $key => $label): ?>
            <option value="<?php echo e($key); ?>" <?php echo $q['status'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <div class="enquiry-grid">
      <div><strong>Parent/Guardian:</strong> <?php echo e($q['parent_name']); ?></div>
      <div><strong>Phone:</strong> <a href="tel:<?php echo e($q['phone']); ?>"><?php echo e($q['phone']); ?></a></div>
      <div><strong>Email:</strong> <a href="mailto:<?php echo e($q['email']); ?>"><?php echo e($q['email']); ?></a></div>
      <?php if ($q['previous_school']): ?><div><strong>Previous school:</strong> <?php echo e($q['previous_school']); ?></div><?php endif; ?>
      <?php if ($q['address']): ?><div class="full"><strong>Address:</strong> <?php echo e($q['address']); ?></div><?php endif; ?>
      <?php if ($q['message']): ?><div class="full"><strong>Message:</strong><br><?php echo nl2br(e($q['message'])); ?></div><?php endif; ?>
    </div>

    <div class="enquiry-foot">
      <a class="btn btn-sm btn-primary" href="<?php echo url('/admin/admissions/' . (int) $q['id']); ?>">👁 View Full Details</a>
      <form method="post" action="<?php echo url('/admin/admissions/' . (int) $q['id'] . '/delete'); ?>" class="inline-form"
            onsubmit="return confirm('Delete this enquiry?');">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
      </form>
    </div>
  </div>
<?php endforeach; ?>
