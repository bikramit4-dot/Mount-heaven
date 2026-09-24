<?php $title = 'Home'; ?>

<!-- Hero slider -->
<section class="hero">
  <div class="hero-slides" id="heroSlides">
    <?php foreach ($sliders as $i => $s): ?>
      <div class="hero-slide <?= $i === 0 ? 'is-active' : '' ?>" style="background-image:url('<?= e(upload_url($s['image'])) ?>')">
        <div class="hero-overlay"></div>
        <div class="container hero-caption">
          <p class="hero-kicker"><?= e(setting('site_name')) ?></p>
          <h1><?= e($s['title']) ?></h1>
          <p><?= e($s['subtitle']) ?></p>
          <div class="hero-buttons">
            <?php if ($s['button_text']): ?>
              <a class="btn btn-gold" href="<?= e($s['button_url'] ?: url('/')) ?>"><?= e($s['button_text']) ?></a>
            <?php endif; ?>
            <a class="btn btn-hero-ghost" href="<?= url('/about') ?>">Discover More</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($sliders) > 1): ?>
    <div class="hero-dots" id="heroDots">
      <?php foreach ($sliders as $i => $s): ?>
        <button class="<?= $i === 0 ? 'is-active' : '' ?>" data-slide="<?= $i ?>" aria-label="Slide <?= $i + 1 ?>"></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <a class="hero-scroll" href="#statsStrip" aria-label="Scroll down"><span>⌄</span></a>
</section>

<!-- School stats strip -->
<section class="strip" id="statsStrip">
  <div class="container strip-grid">
    <div class="strip-item"><span>🏔️</span><strong><?= e(setting('stat_established', 'Since 1959')) ?></strong><small><?= e(setting('stat_established_sub', 'Years of excellence')) ?></small></div>
    <div class="strip-item"><span>🎒</span><strong><span class="num" data-countup="<?= e(setting('stat_students', '800+')) ?>"><?= e(setting('stat_students', '800+')) ?></span></strong><small><?= e(setting('stat_students_sub', 'Happy students')) ?></small></div>
    <div class="strip-item"><span>👩‍🏫</span><strong><span class="num" data-countup="<?= e(setting('stat_teachers', '35+')) ?>"><?= e(setting('stat_teachers', '35+')) ?></span></strong><small><?= e(setting('stat_teachers_sub', 'Expert teachers')) ?></small></div>
    <div class="strip-item"><span>🏆</span><strong><span class="num" data-countup="<?= e(setting('stat_results', '100%')) ?>"><?= e(setting('stat_results', '100%')) ?></span></strong><small><?= e(setting('stat_results_sub', 'Board results')) ?></small></div>
  </div>
</section>

<!-- About preview -->
<section class="section">
  <div class="container split">
    <div class="reveal">
      <p class="eyebrow">About Our School</p>
      <h2 class="section-title left">Welcome to <?= e(setting('site_name')) ?></h2>
      <p class="lead"><?= e(setting('about_short')) ?></p>
      <p><?= e(str_limit(setting('about_history'), 260)) ?></p>
      <div class="about-actions mt">
        <a class="btn btn-primary" href="<?= url('/about') ?>">Learn More About Us</a>
        <a class="btn btn-outline" href="<?= url('/gallery') ?>">📸 View Gallery</a>
      </div>
    </div>
    <div class="split-cards reveal delay-1">
      <div class="mini-card"><h4>🎯 Our Mission</h4><p><?= e(str_limit(setting('about_mission'), 130)) ?></p></div>
      <div class="mini-card alt"><h4>🔭 Our Vision</h4><p><?= e(str_limit(setting('about_vision'), 130)) ?></p></div>
    </div>
  </div>
</section>

<!-- Programs -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">What We Offer</p>
      <h2 class="section-title center">Academic Programs</h2>
      <p class="section-sub">A clear path from the first day of school to board exams — guided by caring teachers at every step.</p>
    </div>
    <div class="grid grid-4">
      <?php foreach ($programs as $p): ?>
        <a class="program-tile reveal" href="<?= url('/programs/' . (int) $p['id']) ?>">
          <div class="program-tile-media">
            <span class="ph-fallback"><?= e($p['icon'] ?: '📚') ?></span>
            <?php if (!empty($p['photo'])): ?>
              <img src="<?= e(upload_url($p['photo'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy" onerror="this.remove()">
            <?php endif; ?>
            <span class="program-tile-icon"><?= e($p['icon'] ?: '📚') ?></span>
          </div>
          <div class="program-tile-body">
            <h3><?= e($p['title']) ?></h3>
            <p class="grades"><?= e($p['grades']) ?></p>
            <p><?= e(str_limit($p['description'], 100)) ?></p>
            <span class="tile-more">Explore program <i>→</i></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <p class="center mt"><a class="btn btn-outline" href="<?= url('/academics') ?>">View All Academics →</a></p>
  </div>
</section>

<!-- Facilities -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">World-Class Campus</p>
      <h2 class="section-title center">Our Facilities</h2>
      <p class="section-sub">Everything your child needs to learn, play and grow — all in one safe campus.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($facilities as $f): ?>
        <a class="facility-tile reveal" href="<?= url('/facilities/' . (int) $f['id']) ?>">
          <div class="facility-tile-media">
            <span class="ph-fallback"><?= e($f['icon'] ?: '🏫') ?></span>
            <?php if (!empty($f['photo'])): ?>
              <img src="<?= e(upload_url($f['photo'])) ?>" alt="<?= e($f['title']) ?>" loading="lazy" onerror="this.remove()">
            <?php endif; ?>
            <span class="facility-tile-icon"><?= e($f['icon'] ?: '🏫') ?></span>
          </div>
          <div class="facility-tile-body">
            <h4><?= e($f['title']) ?></h4>
            <p><?= e(str_limit($f['description'], 105)) ?></p>
            <span class="tile-more">View facility <i>→</i></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Notices & Events -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Stay Updated</p>
      <h2 class="section-title center">Notices &amp; Events</h2>
      <p class="section-sub">Important announcements and what's coming up on the school calendar.</p>
    </div>
    <div class="two-col">
      <div class="panel reveal">
        <div class="panel-head"><h3>📢 Latest Notices</h3><a class="see-all" href="<?= url('/notices') ?>">View all →</a></div>
        <?php foreach ($notices as $n): ?>
          <div class="notice-row">
            <div class="notice-date"><strong><?= e(date('d', strtotime($n['published_at']))) ?></strong><span><?= e(date('M', strtotime($n['published_at']))) ?></span></div>
            <div>
              <h4><a class="row-link" href="<?= url('/notices/' . (int) $n['id']) ?>"><?= e($n['title']) ?></a> <?= $n['is_pinned'] ? '<span class="pin">📌</span>' : '' ?></h4>
              <p><?= e(str_limit($n['body'], 100)) ?></p>
              <a class="see-all" href="<?= url('/notices/' . (int) $n['id']) ?>">View →</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="panel events-panel reveal delay-1">
        <div class="events-panel-head">
          <h3><span class="live-dot" aria-hidden="true"></span> Upcoming Events</h3>
          <?php if ($events): ?><span class="events-count"><?= count($events) ?> coming</span><?php endif; ?>
        </div>
        <?php if ($events): ?>
          <?php foreach ($events as $ev):
            $ts   = strtotime($ev['event_date']);
            $days = (int) floor(($ts - strtotime('today')) / 86400);
          ?>
            <a class="event-item<?= $days <= 3 ? ' soon' : '' ?>" href="<?= url('/events/' . (int) $ev['id']) ?>">
              <div class="event-item-date">
                <strong><?= e(date('d', $ts)) ?></strong>
                <span><?= e(date('M', $ts)) ?></span>
                <small><?= e(date('Y', $ts)) ?></small>
              </div>
              <div class="event-item-body">
                <h4><?= e($ev['title']) ?></h4>
                <p><?= e(str_limit($ev['description'], 90)) ?></p>
                <div class="event-item-meta">
                  <span class="event-chip<?= $days <= 1 ? ' hot' : '' ?>">
                    <?php if ($days <= 0): ?>🎉 Today<?php elseif ($days === 1): ?>⏳ Tomorrow<?php else: ?>⏱️ In <?= $days ?> days<?php endif; ?>
                  </span>
                  <span class="event-chip ghost"><?= e(date('l', $ts)) ?></span>
                </div>
              </div>
              <span class="event-item-arrow" aria-hidden="true">→</span>
            </a>
          <?php endforeach; ?>
          <a class="events-all-btn" href="<?= url('/notices') ?>">View full calendar →</a>
        <?php else: ?>
          <div class="events-empty"><span>🗓️</span><p>No upcoming events yet — check back soon!</p></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta">
  <div class="container cta-inner">
    <div>
      <h2>Ready to join the Mount Heaven family?</h2>
      <p><?= e(str_limit(setting('admission_info'), 150)) ?></p>
    </div>
    <a class="btn btn-gold btn-lg" href="<?= url('/admissions') ?>">Apply for Admission</a>
  </div>
</section>

<?php if ($popupBanner ?? null): ?>
<!-- Popup banner image: auto-shows on open, auto-hides after N seconds -->
<div class="notice-popup" id="noticePopup" role="dialog" aria-modal="false" aria-label="Announcement"
     data-notice-id="banner-<?= (int) $popupBanner['id'] ?>"
     data-duration="<?= max(2, min(30, (int) $popupBanner['duration'])) ?>"
     data-link="<?= e($popupBanner['link_url'] ?: '') ?>" hidden>
  <div class="notice-popup-box notice-popup-image">
    <button class="notice-popup-close" id="noticePopupClose" aria-label="Close announcement" type="button">✕</button>
    <?php if ($popupBanner['title']): ?><h3 class="sr-only"><?= e($popupBanner['title']) ?></h3><?php endif; ?>
    <img src="<?= e(upload_url($popupBanner['image'])) ?>" alt="<?= e($popupBanner['title'] ?: 'Announcement') ?>">
  </div>
</div>
<?php endif; ?>
