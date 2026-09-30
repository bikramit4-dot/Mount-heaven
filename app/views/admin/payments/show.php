<?php $payment = $payment ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="panel-top detail-top">
  <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/payments'); ?>">← All Payments</a>
  <div class="detail-status">
    <form method="post" action="<?php echo url('/admin/payments/' . (int) $payment['id'] . '/status'); ?>" class="status-form">
      <?php echo csrf_field(); ?>
      <select name="status" onchange="this.form.submit()">
        <?php foreach (['pending' => 'Pending', 'verified' => 'Verified', 'failed' => 'Failed'] as $key => $label): ?>
          <option value="<?php echo e($key); ?>" <?php echo $payment['status'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
        <?php endforeach; ?>
      </select>
    </form>
    <form method="post" action="<?php echo url('/admin/payments/' . (int) $payment['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this payment record permanently?">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-sm btn-danger-ghost">🗑 Delete</button>
    </form>
  </div>
</div>

<div class="admin-panel detail-panel">
  <div class="detail-header">
    <span class="detail-avatar"><?php echo e(mb_strtoupper(mb_substr($payment['student_name'], 0, 1))); ?></span>
    <div>
      <h2><?php echo e($payment['student_name']); ?></h2>
      <p class="muted small">
        Submitted <?php echo e(format_date($payment['created_at'], 'M j, Y g:i A')); ?>
        · <span class="pill <?php echo $payment['status'] === 'verified' ? 'pill-green' : ($payment['status'] === 'failed' ? 'pill-red' : 'pill-gray'); ?>">
          <?php echo $payment['status'] === 'pending' ? '⏳' : ($payment['status'] === 'verified' ? '✅' : '❌'); ?>
          <?php echo e(ucfirst($payment['status'])); ?>
        </span>
      </p>
    </div>
  </div>

  <h3 class="detail-section-title">🎒 Student Information</h3>
  <table class="detail-table">
    <tr><th>Student Name</th><td><strong><?php echo e($payment['student_name']); ?></strong></td></tr>
    <tr><th>Class</th><td><?php echo e($payment['class']); ?></td></tr>
    <tr><th>Roll No</th><td><?php echo e($payment['roll_no']); ?></td></tr>
    <tr><th>Phone</th><td><a href="tel:<?php echo e($payment['phone']); ?>">📞 <?php echo e($payment['phone']); ?></a></td></tr>
  </table>

  <h3 class="detail-section-title">💳 Payment Details</h3>
  <table class="detail-table">
    <tr><th>Paying For</th><td><?php echo $payment['payment_for'] ? e($payment['payment_for']) : '—'; ?></td></tr>
    <tr><th>Amount</th><td><strong><?php echo $payment['amount'] !== null ? '₹ ' . e(number_format((float) $payment['amount'], 2)) : '—'; ?></strong></td></tr>
    <tr><th>Method</th><td><?php echo e(ucfirst($payment['payment_method'])); ?> QR</td></tr>
    <tr><th>Transaction Ref</th><td>
      <?php if ($payment['transaction_ref']): ?>
        <code><?php echo e($payment['transaction_ref']); ?></code>
      <?php else: ?>
        <span class="muted">Not provided</span>
      <?php endif; ?>
    </td></tr>
    <tr><th>Voucher</th><td>
      <?php if (!empty($payment['voucher'])):
        $vPath = $payment['voucher'];
        $isPdf = str_ends_with(strtolower($vPath), '.pdf');
      ?>
        <?php if ($isPdf): ?>
          <a class="voucher-link" href="<?php echo e(upload_url($vPath)); ?>" target="_blank" rel="noopener">📄 Open PDF voucher ↗</a>
        <?php else: ?>
          <a class="voucher-thumb" href="<?php echo e(upload_url($vPath)); ?>" target="_blank" rel="noopener">
            <img src="<?php echo e(upload_url($vPath)); ?>" alt="Payment voucher" loading="lazy">
            <span class="voucher-zoom">🔍 Click to enlarge</span>
          </a>
        <?php endif; ?>
      <?php else: ?>
        <span class="muted">No voucher attached</span>
      <?php endif; ?>
    </td></tr>
    <tr><th>Submitted At</th><td><?php echo e(format_date($payment['created_at'], 'l, M j, Y — g:i A')); ?></td></tr>
    <tr><th>Record ID</th><td>#<?php echo (int) $payment['id']; ?></td></tr>
  </table>

  <div class="detail-actions">
    <a class="btn btn-primary" href="tel:<?php echo e($payment['phone']); ?>">📞 Call Parent</a>
    <button type="button" class="btn btn-gold" onclick="window.print()">🖨 Print Receipt</button>
  </div>
</div>
