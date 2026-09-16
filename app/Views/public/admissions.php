<?php $title = 'Admissions'; $errors = $errors ?? []; $old = $old ?? []; ?>

<section class="page-head">
  <div class="container">
    <h1>Admissions</h1>
    <p>Join the Mount Heaven family — <?= e(date('Y')) ?>-<?= e(date('y') + 1) ?> session</p>
  </div>
</section>

<section class="section">
  <div class="container narrow center-text">
    <div class="status-pill <?= setting('admission_open') ? 'open' : 'closed' ?>">
      <?= setting('admission_open') ? '● Admissions Currently OPEN' : '● Admissions Currently Closed' ?>
    </div>
    <p class="lead mt"><?= e(setting('admission_info')) ?></p>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <h2 class="section-title center">How to Apply</h2>
    <div class="grid grid-4 steps">
      <div class="step"><span class="step-num">1</span><h4>Enquire</h4><p>Call us, visit the campus or send a message through the contact page.</p></div>
      <div class="step"><span class="step-num">2</span><h4>Registration Form</h4><p>Collect and submit the registration form at the school office.</p></div>
      <div class="step"><span class="step-num">3</span><h4>Interaction</h4><p>Students attend a friendly interaction/assessment with our faculty.</p></div>
      <div class="step"><span class="step-num">4</span><h4>Enrollment</h4><p>Complete the documentation and welcome to Mount Heaven!</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container narrow">
    <h2 class="section-title">Documents Required</h2>
    <ul class="checklist">
      <li>Birth certificate (photocopy)</li>
      <li>Transfer certificate from the previous school (if applicable)</li>
      <li>Report card of the previous class</li>
      <li>4 recent passport-size photographs of the student</li>
      <li>Aadhaar card photocopy of the student and parents</li>
      <li>Residence proof</li>
    </ul>
  </div>
</section>

<!-- Admission enquiry form -->
<section class="section section-alt" id="apply">
  <div class="container narrow">
    <p class="eyebrow center">Apply Online</p>
    <h2 class="section-title center">Admission Enquiry Form</h2>
    <p class="center-text muted">Fill this form and our admissions team will contact you within 2 working days.</p>

    <div class="panel form-panel">
      <?php if ($success = flash('success')): ?>
        <div class="alert alert-success">✅ <?= e($success) ?></div>
      <?php endif; ?>
      <?php if ($error = flash('error')): ?>
        <div class="alert alert-error">⚠️ <?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= url('/admissions/apply') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
          <div class="form-group">
            <label for="student_name">Student's Full Name *</label>
            <input type="text" id="student_name" name="student_name" value="<?= e($old['student_name'] ?? '') ?>" required>
            <?php if (isset($errors['student_name'])): ?><small class="field-error"><?= e($errors['student_name'][0]) ?></small><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" value="<?= e($old['dob'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender">
              <?php foreach (['' => 'Select…', 'male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label): ?>
                <option value="<?= e($val) ?>" <?= ($old['gender'] ?? '') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="grade_applying">Grade Applying For *</label>
            <select id="grade_applying" name="grade_applying" required>
              <?php foreach (['' => 'Select grade…', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG', 'Grade I' => 'Grade I', 'Grade II' => 'Grade II', 'Grade III' => 'Grade III', 'Grade IV' => 'Grade IV', 'Grade V' => 'Grade V', 'Grade VI' => 'Grade VI', 'Grade VII' => 'Grade VII', 'Grade VIII' => 'Grade VIII', 'Grade IX' => 'Grade IX', 'Grade X' => 'Grade X'] as $val => $label): ?>
                <option value="<?= e($val) ?>" <?= ($old['grade_applying'] ?? '') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['grade_applying'])): ?><small class="field-error"><?= e($errors['grade_applying'][0]) ?></small><?php endif; ?>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="parent_name">Parent/Guardian Name *</label>
            <input type="text" id="parent_name" name="parent_name" value="<?= e($old['parent_name'] ?? '') ?>" required>
            <?php if (isset($errors['parent_name'])): ?><small class="field-error"><?= e($errors['parent_name'][0]) ?></small><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="phone">Phone *</label>
            <input type="text" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>" required>
            <?php if (isset($errors['phone'])): ?><small class="field-error"><?= e($errors['phone'][0]) ?></small><?php endif; ?>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
            <?php if (isset($errors['email'])): ?><small class="field-error"><?= e($errors['email'][0]) ?></small><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="previous_school">Previous School (if any)</label>
            <input type="text" id="previous_school" name="previous_school" value="<?= e($old['previous_school'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="address">Address</label>
          <input type="text" id="address" name="address" value="<?= e($old['address'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="message">Message / Questions</label>
          <textarea id="message" name="message" rows="4"><?= e($old['message'] ?? '') ?></textarea>
        </div>

        <!-- honeypot -->
        <div class="hp-field" aria-hidden="true">
          <label>Website</label>
          <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        <button type="submit" class="btn btn-gold btn-lg">Submit Enquiry ➤</button>
      </form>
    </div>
  </div>
</section>

<section class="cta">
  <div class="container cta-inner">
    <div>
      <h2>Seats are limited — act early!</h2>
      <p>Reach out to our admissions desk and we will guide you through every step.</p>
    </div>
    <a class="btn btn-gold btn-lg" href="#apply">Apply Online Now</a>
  </div>
</section>
