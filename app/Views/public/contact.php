<?php $title = 'Contact Us'; $errors = $errors ?? []; $old = $old ?? []; ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb"><a href="<?= url('/') ?>">Home</a> <span>/</span> Contact Us</p>
    <h1>Contact Us</h1>
    <p>We would love to hear from you</p>
  </div>
</section>

<!-- Contact info + form -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Get In Touch</p>
      <h2 class="section-title center">Send Us a Message</h2>
      <p class="section-sub">Have a question about admissions, fees or school life? Reach us any way you like — or use the form.</p>
    </div>

    <div class="contact-grid">
      <!-- Info column -->
      <aside class="contact-info reveal">
        <h3>Contact Information</h3>

        <a class="contact-tile" href="tel:<?= e(preg_replace('/[^0-9+]/', '', setting('phone'))) ?>">
          <span class="contact-tile-icon">📞</span>
          <div><h4>Call Us</h4><p><?= e(setting('phone')) ?></p></div>
        </a>
        <a class="contact-tile" href="mailto:<?= e(setting('email')) ?>">
          <span class="contact-tile-icon">✉️</span>
          <div><h4>Email Us</h4><p><?= e(setting('email')) ?></p></div>
        </a>
        <div class="contact-tile">
          <span class="contact-tile-icon">📍</span>
          <div><h4>Visit Us</h4><p><?= e(setting('address')) ?></p></div>
        </div>
        <div class="contact-tile">
          <span class="contact-tile-icon">🕘</span>
          <div><h4>Office Hours</h4><p><?= e(setting('office_hours')) ?></p></div>
        </div>

        <?php if (setting('facebook_url') || setting('instagram_url') || setting('youtube_url')): ?>
          <h3 class="contact-info-sub">Follow Us</h3>
          <div class="contact-socials">
            <?php if (setting('facebook_url')): ?><a href="<?= e(setting('facebook_url')) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
            <?php if (setting('instagram_url')): ?><a href="<?= e(setting('instagram_url')) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
            <?php if (setting('youtube_url')): ?><a href="<?= e(setting('youtube_url')) ?>" target="_blank" rel="noopener">YouTube</a><?php endif; ?>
          </div>
        <?php endif; ?>
      </aside>

      <!-- Form -->
      <div class="panel form-panel reveal delay-1">
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
            <textarea id="message" name="message" rows="6" required><?= e($old['message'] ?? '') ?></textarea>
            <?php if (isset($errors['message'])): ?><small class="field-error"><?= e($errors['message'][0]) ?></small><?php endif; ?>
          </div>
          <!-- honeypot -->
          <div class="hp-field" aria-hidden="true">
            <label>Website</label>
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>
          <button type="submit" class="btn btn-primary btn-lg">Send Message ➤</button>
          <small class="form-note">We usually reply within 2 working days.</small>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Location map -->
<?php
    $mapEmbed   = trim((string) setting('map_embed', ''));
    $mapAddress = trim((string) setting('map_location', '')) ?: trim((string) setting('address'));
?>
<?php if ($mapEmbed !== '' || $mapAddress !== ''): ?>
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Find Us</p>
      <h2 class="section-title center">Our Location</h2>
      <p class="section-sub">We are easy to reach — here is exactly where to find us.</p>
    </div>

    <div class="map-wrap reveal">
      <?php if ($mapEmbed !== ''): ?>
        <iframe
          class="map-embed"
          src="<?= e($mapEmbed) ?>"
          title="Map — <?= e(setting('site_name')) ?> location"
          loading="lazy"
          allowfullscreen
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      <?php else: ?>
        <iframe
          class="map-embed"
          src="https://www.google.com/maps?q=<?= e(rawurlencode($mapAddress)) ?>&output=embed"
          title="Map — <?= e(setting('site_name')) ?> location"
          loading="lazy"
          allowfullscreen
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      <?php endif; ?>
      <div class="map-foot">
        <span>📍 <?= e($mapAddress) ?></span>
        <a class="btn btn-outline btn-sm" href="https://www.google.com/maps/search/?api=1&query=<?= e(rawurlencode($mapAddress)) ?>" target="_blank" rel="noopener">Open in Google Maps ↗</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
