<?php $title = 'Event'; $isUpcoming = strtotime($event['event_date']) >= strtotime('today'); $evBs = \App\Core\NepaliCalendar::adToBs((string) $event['event_date']); ?>

<!-- Page hero -->
<section class="page-head">
  <div class="container">
    <p class="crumb">
      <a href="<?= url('/') ?>">Home</a> <span>/</span>
      <a href="<?= url('/notices') ?>">Notices &amp; Events</a> <span>/</span> Event
    </p>
    <h1>📅 Event</h1>
    <p><?= $isUpcoming ? 'Mark your calendar — we would love to see you there' : 'A look back at one of our school events' ?></p>
  </div>
</section>

<section class="section">
  <div class="container detail-grid">

    <!-- Event content -->
    <article class="detail-main">
      <div class="event-card reveal <?= $isUpcoming ? 'upcoming' : '' ?>">
        <div class="event-hero">
          <span class="ph-fallback">📅</span>
          <?php if (!empty($event['image'])): ?>
            <img src="<?= e(upload_url($event['image'])) ?>" alt="<?= e($event['title']) ?>" onerror="this.remove()">
          <?php endif; ?>
        </div>
        <div class="event-card-info">
          <div class="event-when">
            <strong><?= e(date('d', strtotime($event['event_date']))) ?></strong>
            <span><?= e(date('M Y', strtotime($event['event_date']))) ?></span>
            <?php if ($evBs): ?><small class="bs-date"><?= e(\App\Core\NepaliCalendar::formatBsNp($evBs)) ?></small><?php endif; ?>
          </div>
          <div class="event-card-text">
            <h1 class="detail-title"><?= e($event['title']) ?></h1>
            <p class="event-bs-line"><?= e(format_date($event['event_date'])) ?><?= $evBs ? ' · <span class="np">' . e(\App\Core\NepaliCalendar::formatBsNp($evBs)) . '</span>' : '' ?></p>
            <?php if ($isUpcoming): ?><span class="event-soon">✨ Coming up</span><?php endif; ?>
          </div>
        </div>
      </div>

      <div class="detail-body reveal">
        <p><?= nl2br(e($event['description'])) ?></p>
      </div>

      <div class="detail-actions mt">
        <a class="btn btn-outline" href="<?= url('/notices') ?>">← Back to Notices &amp; Events</a>
        <a class="btn btn-primary" href="<?= url('/contact') ?>">Any Question? Contact Us</a>
      </div>
    </article>

    <!-- Sidebar: upcoming events -->
    <aside class="detail-side">
      <div class="panel">
        <div class="panel-head"><h3>📅 More Events</h3><a class="see-all" href="<?= url('/notices') ?>">View all →</a></div>
        <?php if ($events): ?>
          <?php foreach ($events as $ev): ?>
            <?php $sBs = \App\Core\NepaliCalendar::adToBs((string) $ev['event_date']); ?>
            <div class="side-item">
              <div class="side-date"><?= e(date('d M', strtotime($ev['event_date']))) ?></div>
              <div>
                <a class="side-link" href="<?= url('/events/' . (int) $ev['id']) ?>"><?= e($ev['title']) ?></a>
                <?php if ($sBs): ?><time class="side-bs"><?= e(\App\Core\NepaliCalendar::formatBsNp($sBs)) ?></time><?php endif; ?>
                <p><?= e(str_limit($ev['description'], 80)) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="muted">No upcoming events scheduled.</p>
        <?php endif; ?>
      </div>
    </aside>

  </div>
</section>
