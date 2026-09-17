<?php $enquiry = $enquiry ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>

<div class="panel-top detail-top">
  <a class="btn btn-sm btn-ghost" href="<?php echo url('/admin/admissions'); ?>">← All Enquiries</a>
  <div class="detail-status">
    <form method="post" action="<?php echo url('/admin/admissions/' . (int) $enquiry['id'] . '/status'); ?>" class="status-form">
      <?php echo csrf_field(); ?>
      <select name="status" onchange="this.form.submit()">
        <?php foreach (['new' => 'New', 'contacted' => 'Contacted', 'enrolled' => 'Enrolled', 'closed' => 'Closed'] as $key => $label): ?>
          <option value="<?php echo e($key); ?>" <?php echo $enquiry['status'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
        <?php endforeach; ?>
      </select>
    </form>
    <form method="post" action="<?php echo url('/admin/admissions/' . (int) $enquiry['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this enquiry permanently?">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-sm btn-danger-ghost">🗑 Delete</button>
    </form>
  </div>
</div>

<div class="admin-panel detail-panel">
  <div class="detail-header">
    <span class="detail-avatar"><?php echo e(mb_strtoupper(mb_substr($enquiry['student_name'], 0, 1))); ?></span>
    <div>
      <h2><?php echo e($enquiry['student_name']); ?></h2>
      <p class="muted small">
        Submitted <?php echo e(format_date($enquiry['created_at'], 'M j, Y g:i A')); ?>
        · <span class="pill <?php echo $enquiry['status'] === 'new' ? 'pill-green' : 'pill-gray'; ?>"><?php echo e(ucfirst($enquiry['status'])); ?></span>
      </p>
    </div>
  </div>

  <h3 class="detail-section-title">🎒 Student Information</h3>
  <table class="detail-table">
    <tr><th>Student Name</th><td><?php echo e($enquiry['student_name']); ?></td></tr>
    <tr><th>Date of Birth</th><td><?php echo $enquiry['dob'] ? e(format_date($enquiry['dob'], 'M j, Y')) : '—'; ?></td></tr>
    <tr><th>Gender</th><td><?php echo $enquiry['gender'] ? e(ucfirst($enquiry['gender'])) : '—'; ?></td></tr>
    <tr><th>Grade Applying For</th><td><strong><?php echo e($enquiry['grade_applying']); ?></strong></td></tr>
    <tr><th>Previous School</th><td><?php echo $enquiry['previous_school'] ? e($enquiry['previous_school']) : '—'; ?></td></tr>
  </table>

  <h3 class="detail-section-title">👨‍👩‍👧 Parent / Guardian &amp; Contact</h3>
  <table class="detail-table">
    <tr><th>Parent / Guardian</th><td><?php echo e($enquiry['parent_name']); ?></td></tr>
    <tr><th>Phone</th><td><a href="tel:<?php echo e($enquiry['phone']); ?>">📞 <?php echo e($enquiry['phone']); ?></a></td></tr>
    <tr><th>Email</th><td><a href="mailto:<?php echo e($enquiry['email']); ?>">✉️ <?php echo e($enquiry['email']); ?></a></td></tr>
    <tr><th>Address</th><td><?php echo $enquiry['address'] ? nl2br(e($enquiry['address'])) : '—'; ?></td></tr>
  </table>

  <h3 class="detail-section-title">💬 Message</h3>
  <div class="detail-message"><?php echo $enquiry['message'] ? nl2br(e($enquiry['message'])) : '<span class="muted">No message provided.</span>'; ?></div>

  <div class="detail-actions">
    <a class="btn btn-primary" href="tel:<?php echo e($enquiry['phone']); ?>">📞 Call Parent</a>
    <a class="btn btn-gold" href="mailto:<?php echo e($enquiry['email']); ?>?subject=<?php echo rawurlencode('Admission Enquiry — ' . $enquiry['student_name']); ?>">✉️ Email Parent</a>
  </div>
</div>
