<?php $title = 'Contact Us'; $errors = $errors ?? []; $old = $old ?? []; ?>

<section class="page-head">
  <div class="container">
    <h1>Contact Us</h1>
    <p>We would love to hear from you</p>
  </div>
</section>

<section class="section">
  <div class="container two-col">
    <div>
      <div class="contact-card">
        <span>📍</span>
        <div><h4>Visit Us</h4><p><?= e(setting('address')) ?></p></div>
      </div>
      <div class="contact-card">
        <span>📞</span>
        <div><h4>Call Us</h4><p><?= e(setting('phone')) ?></p></div>
      </div>
      <div class="contact-card">
        <span>✉️</span>
        <div><h4>Email Us</h4><p><?= e(setting('email')) ?></p></div>
      </div>
      <div class="contact-card">
        <span>🕘</span>
        <div><h4>Office Hours</h4><p><?= e(setting('office_hours')) ?></p></div>
      </div>
    </div>

    <div class="panel form-panel">
      <h3>Send us a Message</h3>

      <?php if ($success = flash('success')): ?>
        <div class="alert alert-success">✅ <?= e($success) ?></div>
      <?php endif; ?>
      <?php if ($error = flash('error')): ?>
        <div class="alert alert-error">⚠️ <?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= url('/contact') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
          <div class="form-group">
            <label for="name">Your Name *</label>
            <input type="text" id="name" name="name" value="<?= e($old['name'] ?? '') ?>" required>
            <?php if (isset($errors['name'])): ?><small class="field-error"><?= e($errors['name'][0]) ?></small><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
            <?php if (isset($errors['email'])): ?><small class="field-error"><?= e($errors['email'][0]) ?></small><?php endif; ?>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" value="<?= e($old['subject'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="message">Message *</label>
          <textarea id="message" name="message" rows="5" required><?= e($old['message'] ?? '') ?></textarea>
          <?php if (isset($errors['message'])): ?><small class="field-error"><?= e($errors['message'][0]) ?></small><?php endif; ?>
        </div>
        <!-- honeypot -->
        <div class="hp-field" aria-hidden="true">
          <label>Website</label>
          <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary btn-lg">Send Message ➤</button>
      </form>
    </div>
  </div>
</section>
