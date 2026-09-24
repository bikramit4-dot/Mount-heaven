<?php $title = 'Notices & Events'; $cal = $cal ?? ['ok' => false]; $calY = (int) ($calY ?? 0); $calM = (int) ($calM ?? 0); $adCal = $adCal ?? ['ok' => false]; $adY = (int) ($adY ?? 0); $adM = (int) ($adM ?? 0); ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb"><a href="<?= url('/') ?>">Home</a> <span>/</span> Notices &amp; Events</p>
    <h1>Notices &amp; Events</h1>
    <p>Stay updated with school announcements</p>
  </div>
</section>

<!-- Calendars: English (left) + Nepali (right) -->
<section class="section" id="calendar">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">पात्रो · Calendar</p>
      <h2 class="section-title center">🗓️ Calendars</h2>
      <p class="section-sub">English (A.D.) and Nepali (Bikram Sambat) calendars side by side.</p>
    </div>
    <div class="cal-grid reveal">
      <div class="cal-col">
        <?php include BASE_PATH . '/app/views/partials/english-calendar.php'; ?>
      </div>
      <div class="cal-col">
        <?php include BASE_PATH . '/app/views/partials/nepali-calendar.php'; ?>
      </div>
    </div>
  </div>
</section>

<!-- Notices -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Official Announcements</p>
      <h2 class="section-title center">📢 Notices</h2>
      <p class="section-sub">Important information for students and parents — pinned notices appear first.</p>
    </div>

    <div class="notice-list">
      <?php foreach ($notices as $n): ?>
        <?php
          $bs = \App\Core\NepaliCalendar::adToBs((string) $n['published_at']);
          $nts = strtotime($n['published_at']);
          $isNew = (time() - $nts) < 7 * 86400;
        ?>
        <article class="notice-card reveal <?= $n['is_pinned'] ? 'pinned' : '' ?>">
          <div class="notice-date-lg <?= $n['is_pinned'] ? 'gold' : '' ?>">
            <strong><?= e(date('d', $nts)) ?></strong>
            <span><?= e(date('M Y', $nts)) ?></span>
            <?php if ($bs): ?><small class="bs-date"><?= e(\App\Core\NepaliCalendar::formatBsNp($bs)) ?></small><?php endif; ?>
          </div>
          <div class="notice-content">
            <div class="notice-meta">
              <time><?= e(format_date($n['published_at'])) ?></time>
              <?php if ($bs): ?><time class="np"><?= e(\App\Core\NepaliCalendar::formatBsNp($bs)) ?></time><?php endif; ?>
              <?php if ($n['is_pinned']): ?><span class="pin-badge">📌 Pinned</span><?php endif; ?>
              <?php if ($isNew && !$n['is_pinned']): ?><span class="new-badge">NEW</span><?php endif; ?>
            </div>
            <h3><a class="notice-link" href="<?= url('/notices/' . (int) $n['id']) ?>"><?= e($n['title']) ?></a></h3>
            <p><?= e(str_limit($n['body'], 170)) ?></p>
            <a class="btn btn-outline btn-sm view-btn" href="<?= url('/notices/' . (int) $n['id']) ?>">👁 View Details</a>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (!$notices): ?><p class="muted center-text">No notices published yet.</p><?php endif; ?>
    </div>
  </div>
</section>

<!-- Events -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow center">Mark Your Calendar</p>
      <h2 class="section-title center">📅 Upcoming Events</h2>
      <p class="section-sub">Activities, celebrations and important dates on the school calendar.</p>
    </div>

    <div class="event-list">
      <?php foreach ($events as $ev): ?>
        <?php
          $isUpcoming = strtotime($ev['event_date']) >= strtotime('today');
          $evBs = \App\Core\NepaliCalendar::adToBs((string) $ev['event_date']);
          $evTs = strtotime($ev['event_date']);
          $evDays = (int) floor(($evTs - strtotime('today')) / 86400);
        ?>
        <article class="event-card reveal <?= $isUpcoming ? 'upcoming' : 'past' ?>">
          <div class="event-media">
            <span class="ph-fallback">📅</span>
            <?php if (!empty($ev['image'])): ?>
              <img class="event-photo" src="<?= e(upload_url($ev['image'])) ?>" alt="<?= e($ev['title']) ?>" loading="lazy" onerror="this.remove()">
            <?php endif; ?>
          </div>
          <div class="event-when"><?= $isUpcoming ? '<span class="event-flag">UPCOMING</span>' : '<span class="event-flag past">PAST</span>' ?>
            <strong><?= e(date('d', $evTs)) ?></strong>
            <span><?= e(date('M Y', $evTs)) ?></span>
            <?php if ($evBs): ?><small class="bs-date"><?= e(\App\Core\NepaliCalendar::formatBsNp($evBs)) ?></small><?php endif; ?>
          </div>
          <div class="event-card-body">
            <h3><?= e($ev['title']) ?></h3>
            <p><?= e(str_limit($ev['description'], 160)) ?></p>
            <div class="event-meta-row">
              <span class="event-chip ghost">📆 <?= e(format_date($ev['event_date'])) ?></span>
              <?php if ($evBs): ?><span class="event-chip ghost np"><?= e(\App\Core\NepaliCalendar::formatBsNp($evBs)) ?></span><?php endif; ?>
              <?php if ($isUpcoming): ?>
                <span class="event-chip<?= $evDays <= 1 ? ' hot' : '' ?>">
                  <?php if ($evDays <= 0): ?>🎉 Today<?php elseif ($evDays === 1): ?>⏳ Tomorrow<?php else: ?>⏱️ In <?= $evDays ?> days<?php endif; ?>
                </span>
              <?php endif; ?>
            </div>
            <a class="btn btn-outline btn-sm view-btn" href="<?= url('/events/' . (int) $ev['id']) ?>">👁 View Details</a>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (!$events): ?><p class="muted center-text">No events scheduled yet.</p><?php endif; ?>
    </div>
  </div>
</section>
