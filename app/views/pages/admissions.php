<?php $title = 'Admissions'; $errors = $errors ?? []; $old = $old ?? []; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb"><a href="<?= url('/') ?>">Home</a> <span>/</span> Admissions</p>
    <h1>Admissions</h1>
    <p>Join the Mount Heaven family — <?= e(date('Y')) ?>-<?= e(date('y') + 1) ?> session</p>
  </div>
</section>

<!-- Admissions status banner -->
<section class="section">
  <div class="container narrow">
    <div class="status-banner <?= setting('admission_open') ? 'open' : 'closed' ?> reveal">
      <span class="status-banner-icon"><?= setting('admission_open') ? '🎉' : '🔒' ?></span>
      <div>
        <h2><?= setting('admission_open') ? 'Admissions are OPEN!' : 'Admissions Currently Closed' ?></h2>
        <p><?= e(setting('admission_info')) ?></p>
        <?php if (setting('admission_open')): ?>
          <a class="btn btn-primary btn-sm" href="#apply">Start Your Application ↓</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- How to apply -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Simple &amp; Transparent</p>
      <h2 class="section-title center">How to Apply</h2>
      <p class="section-sub">Four easy steps from your first enquiry to your child's first day at Mount Heaven.</p>
    </div>
    <div class="grid grid-4 steps-flow">
      <div class="step reveal">
        <span class="step-num">1</span><h4>Enquire</h4>
        <p>Call us, visit the campus or send a message through the contact page.</p>
      </div>
      <div class="step reveal">
        <span class="step-num">2</span><h4>Registration Form</h4>
        <p>Collect and submit the registration form at the school office.</p>
      </div>
      <div class="step reveal">
        <span class="step-num">3</span><h4>Interaction</h4>
        <p>Students attend a friendly interaction/assessment with our faculty.</p>
      </div>
      <div class="step reveal">
        <span class="step-num">4</span><h4>Enrollment</h4>
        <p>Complete the documentation and welcome to Mount Heaven!</p>
      </div>
    </div>
  </div>
</section>

<!-- Documents required -->
<section class="section">
  <div class="container narrow">
    <div class="panel docs-panel reveal">
      <div class="panel-head"><h3>📋 Documents Required</h3></div>
      <ul class="checklist docs-grid">
        <li>Birth certificate (photocopy)</li>
        <li>Transfer certificate from the previous school (if applicable)</li>
        <li>Report card of the previous class</li>
        <li>4 recent passport-size photographs of the student</li>
        <li>Aadhaar card photocopy of the student and parents</li>
        <li>Residence proof</li>
      </ul>
      <div class="docs-note">💡 Originals may be verified at the school office during the interaction step.</div>
    </div>
  </div>
</section>

<!-- Admission enquiry form -->
<section class="section section-alt" id="apply">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Apply Online</p>
      <h2 class="section-title center">Admission Enquiry Form</h2>
      <p class="section-sub">Fill this form and our admissions team will contact you within 2 working days.</p>
    </div>

    <div class="apply-grid">
      <!-- Info column -->
      <aside class="apply-info reveal">
        <h3>Talk to us directly</h3>
        <div class="contact-card">
          <span>📞</span>
          <div><h4>Phone</h4><p><?= e(setting('phone')) ?></p></div>
        </div>
        <div class="contact-card">
          <span>✉️</span>
          <div><h4>Email</h4><p><?= e(setting('email')) ?></p></div>
        </div>
        <div class="contact-card">
          <span>🕒</span>
          <div><h4>Office Hours</h4><p><?= e(setting('office_hours')) ?></p></div>
        </div>

        <h3 class="apply-info-sub">Why families choose us</h3>
        <ul class="apply-points">
          <li>Experienced, caring teachers</li>
          <li>Small classes with personal attention</li>
          <li>Safe campus with modern facilities</li>
          <li>Strong board exam results</li>
        </ul>
      </aside>

      <!-- Form -->
      <div class="panel form-panel reveal delay-1">
        <?php if ($success = flash('success')): ?>
          <div class="alert alert-success">✅ <?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
          <div class="alert alert-error">⚠️ <?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= url('/admissions/apply') ?>" novalidate>
          <?= csrf_field() ?>
          <div class="form-row">
            <div class="form-group">
              <label for="student_name">Student's Full Name *</label>
              <input type="text" id="student_name" name="student_name" value="<?= e($old['student_name'] ?? '') ?>" required>
              <?php if (isset($errors['student_name'])): ?><small class="field-error"><?= e($errors['student_name'][0]) ?></small><?php endif; ?>
            </div>
            <div class="form-group dob-pair">
              <label>Date of Birth</label>
              <div class="dob-fields">
                <div class="dob-field dob-bs">
                  <label class="dob-sub" for="dob_bs">Birth Date (B.S.)</label>
                  <input type="text" id="dob_bs" name="dob_bs" inputmode="numeric"
                         placeholder="YYYY-MM-DD" autocomplete="off"
                         value="<?= e($old['dob_bs'] ?? '') ?>">
                </div>
                <div class="dob-field dob-ad">
                  <label class="dob-sub" for="dob">(A.D.)</label>
                  <input type="date" id="dob" name="dob" value="<?= e($old['dob'] ?? '') ?>">
                </div>
              </div>
              <small class="dob-hint" id="dobHint">Type the Nepali (B.S.) date — the English (A.D.) date fills automatically, or pick the A.D. date to get B.S.</small>
              <input type="hidden" name="dob_np_label" id="dobNpLabel" value="<?= e($old['dob_np_label'] ?? '') ?>">
              <?php $todayBs = \App\Core\NepaliCalendar::adToBs(date('Y-m-d')); ?>
              <input type="hidden" id="bsToday" value="<?= $todayBs ? e($todayBs['year'] . '-' . $todayBs['month'] . '-' . $todayBs['day']) : '' ?>">
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

          <div class="form-section-title">Previous School Details <small>(leave blank if first school)</small></div>
          <div class="form-row form-row-3">
            <div class="form-group">
              <label for="previous_class">Previous Class</label>
              <input type="text" id="previous_class" name="previous_class" placeholder="e.g. Grade V" maxlength="60"
                     value="<?= e($old['previous_class'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="emis_no">EMIS Number</label>
              <input type="text" id="emis_no" name="emis_no" placeholder="e.g. 123-45-678" maxlength="30"
                     inputmode="numeric" value="<?= e($old['emis_no'] ?? '') ?>">
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
        <script>window.APP_BASE_URL = <?= json_encode(app_base()) ?>;</script>
        <script src="<?= asset('js/dob-pair.js') ?>"></script>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta">
  <div class="container cta-inner">
    <div>
      <h2>Seats are limited — act early!</h2>
      <p>Reach out to our admissions desk and we will guide you through every step.</p>
    </div>
    <a class="btn btn-gold btn-lg" href="#apply">Apply Online Now</a>
  </div>
</section>
