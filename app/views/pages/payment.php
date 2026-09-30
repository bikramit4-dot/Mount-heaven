<?php $title = 'Pay Fees Online'; $errors = $errors ?? []; $old = $old ?? []; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb"><a href="<?= url('/') ?>">Home</a> <span>/</span> Pay Fees Online</p>
    <h1>Pay Fees Online</h1>
    <p>Fill in the student details, scan the school QR code and pay in seconds</p>
  </div>
</section>

<!-- Form + QR -->
<section class="section">
  <div class="container">
    <div class="payment-grid">

      <!-- Student info form -->
      <div class="panel form-panel reveal">
        <div class="section-head">
          <p class="eyebrow">Step 1 · Student Information</p>
          <h2 class="section-title left">Enter Student Details</h2>
          <p class="section-sub">Fill in the student information, pay through the QR code, then submit this form so the office can verify your payment.</p>
        </div>

        <?php if ($success = flash('success')): ?>
          <div class="alert alert-success">✅ <?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
          <div class="alert alert-error">⚠️ <?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= url('/payment') ?>" enctype="multipart/form-data" novalidate>
          <?= csrf_field() ?>
          <div class="form-row">
            <div class="form-group">
              <label for="student_name">Student's Full Name *</label>
              <input type="text" id="student_name" name="student_name" value="<?= e($old['student_name'] ?? '') ?>" required>
              <?php if (isset($errors['student_name'])): ?><small class="field-error"><?= e($errors['student_name'][0]) ?></small><?php endif; ?>
            </div>
            <div class="form-group">
              <label for="class">Class *</label>
              <select id="class" name="class" required>
                <?php foreach (['' => 'Select class…', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG', 'Grade I' => 'Grade I', 'Grade II' => 'Grade II', 'Grade III' => 'Grade III', 'Grade IV' => 'Grade IV', 'Grade V' => 'Grade V', 'Grade VI' => 'Grade VI', 'Grade VII' => 'Grade VII', 'Grade VIII' => 'Grade VIII', 'Grade IX' => 'Grade IX', 'Grade X' => 'Grade X'] as $val => $label): ?>
                  <option value="<?= e($val) ?>" <?= ($old['class'] ?? '') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['class'])): ?><small class="field-error"><?= e($errors['class'][0]) ?></small><?php endif; ?>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="roll_no">Roll No *</label>
              <input type="text" id="roll_no" name="roll_no" value="<?= e($old['roll_no'] ?? '') ?>" required>
              <?php if (isset($errors['roll_no'])): ?><small class="field-error"><?= e($errors['roll_no'][0]) ?></small><?php endif; ?>
            </div>
            <div class="form-group">
              <label for="phone">Phone No *</label>
              <input type="text" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>" required>
              <?php if (isset($errors['phone'])): ?><small class="field-error"><?= e($errors['phone'][0]) ?></small><?php endif; ?>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="payment_for">Paying For</label>
              <select id="payment_for" name="payment_for">
                <?php foreach (['' => 'Select…', 'Monthly Fee' => 'Monthly Fee', 'Exam Fee' => 'Exam Fee', 'Admission Fee' => 'Admission Fee', 'Transport Fee' => 'Transport Fee', 'Annual Charges' => 'Annual Charges', 'Other' => 'Other'] as $val => $label): ?>
                  <option value="<?= e($val) ?>" <?= ($old['payment_for'] ?? '') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="amount">Amount Paid</label>
              <input type="text" id="amount" name="amount" inputmode="decimal" placeholder="e.g. 2500" value="<?= e($old['amount'] ?? '') ?>">
              <?php if (isset($errors['amount'])): ?><small class="field-error"><?= e($errors['amount'][0]) ?></small><?php endif; ?>
            </div>
          </div>

          <div class="form-group">
            <label for="transaction_ref">Transaction / Reference No (from your UPI app)</label>
            <input type="text" id="transaction_ref" name="transaction_ref" placeholder="e.g. 402512345678" value="<?= e($old['transaction_ref'] ?? '') ?>">
            <small class="help-text">Find this in your payment app's receipt after paying — it helps us match your payment.</small>
            <?php if (isset($errors['transaction_ref'])): ?><small class="field-error"><?= e($errors['transaction_ref'][0]) ?></small><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="voucher">Payment Voucher / Screenshot <small class="muted-label">(optional)</small></label>
            <div class="voucher-upload">
              <span class="voucher-upload-icon">🧾</span>
              <div class="voucher-upload-text">
                <strong>Attach your payment proof</strong>
                <small>Screenshot of the UPI/bank receipt, or a PDF statement. JPG, PNG, WEBP, GIF or PDF · up to 5&nbsp;MB.</small>
              </div>
              <input type="file" id="voucher" name="voucher" accept="image/*,application/pdf">
            </div>
            <?php if (isset($errors['voucher'])): ?><small class="field-error"><?= e($errors['voucher'][0]) ?></small><?php endif; ?>
          </div>

          <!-- honeypot -->
          <div class="hp-field" aria-hidden="true">
            <label>Website</label>
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>

          <button type="submit" class="btn btn-gold btn-lg">Submit Payment Details ➤</button>
          <small class="form-note">The school office verifies every payment before it is marked as received.</small>
        </form>
      </div>

      <!-- QR side -->
      <aside class="payment-qr-side reveal delay-1">
        <div class="qr-card">
          <h3>📱 Step 2 · Scan &amp; Pay</h3>
          <?php if (setting('payment_qr_image')): ?>
            <div class="qr-frame">
              <img src="<?= e(upload_url(setting('payment_qr_image'))) ?>" alt="<?= e(setting('payment_qr_label', 'School payment QR code')) ?>">
            </div>
            <p class="qr-label"><?= e(setting('payment_qr_label', 'School UPI QR Code')) ?></p>
          <?php else: ?>
            <div class="qr-frame qr-empty">
              <span>🔳</span>
              <p>QR code coming soon.<br>Please pay at the school office.</p>
            </div>
          <?php endif; ?>
          <?php if (setting('payment_instructions')): ?>
            <div class="qr-instructions">
              <h4>How to pay</h4>
              <p><?= e(setting('payment_instructions')) ?></p>
            </div>
          <?php endif; ?>
        </div>

        <div class="qr-help">
          <span>🧾</span>
          <p><strong>No internet receipt?</strong> Just submit the form — the office will confirm your payment by phone.</p>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta">
  <div class="container cta-inner">
    <div>
      <h2>Having trouble paying online?</h2>
      <p>Visit the school office — we accept payments at the counter during office hours.</p>
    </div>
    <a class="btn btn-gold btn-lg" href="<?= url('/contact') ?>">Contact the Office</a>
  </div>
</section>
