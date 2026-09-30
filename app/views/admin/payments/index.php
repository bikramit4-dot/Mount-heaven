<?php $payments = $payments ?? []; $status = $status ?? 'all'; $pendingCount = $pendingCount ?? 0; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="filter-tabs">
  <?php foreach (['all' => 'All', 'pending' => 'Pending', 'verified' => 'Verified', 'failed' => 'Failed'] as $key => $label): ?>
    <a class="filter-tab <?php echo $status === $key ? 'active' : ''; ?>" href="<?php echo url('/admin/payments'); ?>?status=<?php echo e($key); ?>">
      <?php echo e($label); ?><?php if ($key === 'pending' && $pendingCount > 0): ?> <b class="badge"><?php echo (int) $pendingCount; ?></b><?php endif; ?>
    </a>
  <?php endforeach; ?>
</div>

<?php if (!$payments): ?>
  <div class="admin-panel"><p class="muted">No fee payments submitted yet.</p></div>
<?php endif; ?>

<?php foreach ($payments as $p): ?>
  <div class="admin-panel enquiry-card">
    <div class="enquiry-head">
      <div>
        <h3><a class="row-link" href="<?php echo url('/admin/payments/' . (int) $p['id']); ?>"><?php echo e($p['student_name']); ?></a> <span class="muted small">· <?php echo e($p['class']); ?> · Roll <?php echo e($p['roll_no']); ?></span></h3>
        <p class="muted small">
          <?php echo $p['status'] === 'pending' ? '⏳ ' : ($p['status'] === 'verified' ? '✅ ' : '❌ '); ?>
          <?php echo e(ucfirst($p['status'])); ?> · submitted <?php echo e(format_date($p['created_at'], 'M j, Y g:i A')); ?>
        </p>
      </div>
      <form method="post" action="<?php echo url('/admin/payments/' . (int) $p['id'] . '/status'); ?>" class="status-form">
        <?php echo csrf_field(); ?>
        <select name="status" onchange="this.form.submit()">
          <?php foreach (['pending' => 'Pending', 'verified' => 'Verified', 'failed' => 'Failed'] as $key => $label): ?>
            <option value="<?php echo e($key); ?>" <?php echo $p['status'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <div class="enquiry-grid">
      <div><strong>Phone:</strong> <a href="tel:<?php echo e($p['phone']); ?>"><?php echo e($p['phone']); ?></a></div>
      <div><strong>Paying for:</strong> <?php echo e($p['payment_for'] ?: '—'); ?></div>
      <div><strong>Amount:</strong> <?php echo $p['amount'] !== null ? '₹ ' . e(number_format((float) $p['amount'], 2)) : '—'; ?> <?php if (!empty($p['voucher'])): ?><span class="pill pill-gold" title="Voucher attached">🧾 Voucher</span><?php endif; ?></div>
      <div><strong>Method:</strong> <?php echo e(ucfirst($p['payment_method'])); ?> QR</div>
      <div class="full"><strong>Transaction ref:</strong> <?php echo e($p['transaction_ref'] ?: '—'); ?>
        <?php if (!empty($p['voucher'])): ?> · <a href="<?php echo e(upload_url($p['voucher'])); ?>" target="_blank" rel="noopener">🧾 View voucher ↗</a><?php endif; ?>
      </div>
    </div>

    <div class="enquiry-foot">
      <a class="btn btn-sm btn-primary" href="<?php echo url('/admin/payments/' . (int) $p['id']); ?>">👁 View Full Details</a>
      <form method="post" action="<?php echo url('/admin/payments/' . (int) $p['id'] . '/delete'); ?>" class="inline-form"
            onsubmit="return confirm('Delete this payment record?');">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
      </form>
    </div>
  </div>
<?php endforeach; ?>
