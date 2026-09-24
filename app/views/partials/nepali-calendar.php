<?php
/**
 * Nepali (Bikram Sambat) month calendar widget.
 *
 * Expects: $cal = App\Core\NepaliCalendar::monthGrid($y, $m)
 * Optional: $calY, $calM (current selection for the selects)
 *
 * Matches the reference design: Mon-first week, adjacent-month days
 * grayed out, today marked with a dot, month/year selects + arrows.
 */
use App\Core\NepaliCalendar;

$cal = $cal ?? ['ok' => false];
if (!$cal['ok']):
    ?><p class="muted center-text">Calendar data is not available for this date.</p><?php
    return;
endif;

$calY = (int) ($calY ?? $cal['year']);
$calM = (int) ($calM ?? $cal['month']);

/** Link URL for a given BS year/month, preserving the AD selection if present. */
$calUrl = static function (int $y, int $m) use ($adY, $adM): string {
    $params = array_filter(['cal' => $y . '-' . $m, 'adcal' => ($adY ?? 0) ? $adY . '-' . $adM : null]);
    return url('/notices') . '?' . http_build_query($params) . '#calendar';
};

/** Devanagari numerals for labels. */
$npNum = static fn (int|string $n): string => NepaliCalendar::np($n);

// Monday-first week header (like the reference design).
$weekHeader = ['सोम', 'मंगल', 'बुध', 'बिही', 'शुक्र', 'शनि', 'आइत'];

// Leading cells: grayed tail of the previous month (blanks at range start).
$leading = ($cal['leadingBlanks'] + 6) % 7; // Sunday-first → Monday-first
$prevCells = [];
if ($cal['prev'] && $leading > 0) {
    [$py, $pm] = $cal['prev'];
    $daysInPrev = NepaliCalendar::monthGrid($py, $pm)['daysInMonth'];
    for ($d = $daysInPrev - $leading + 1; $d <= $daysInPrev; $d++) {
        $prevCells[] = $d;
    }
}

// Trailing cells: grayed head of the next month to complete the last row.
$trailing = (7 - (($leading + $cal['daysInMonth']) % 7)) % 7;
$nextCells = [];
if ($cal['next'] && $trailing > 0) {
    for ($d = 1; $d <= $trailing; $d++) {
        $nextCells[] = $d;
    }
}

// Year navigation (clamped to the supported BS range).
$prevYear = $calY > 2000 ? $calY - 1 : null;
$nextYear = $calY < 2090 ? $calY + 1 : null;
?>

<div class="np-cal" id="calendar-card" role="group" aria-label="नेपाली पात्रो (Nepali calendar)">

  <!-- Navigation: [<] month [>]   [<] year [>] -->
  <form class="np-cal-nav" method="get" action="<?= url('/notices') ?>#calendar">
    <?php if ($cal['prev']): ?>
      <a class="np-cal-arrow" href="<?= e($calUrl(...$cal['prev'])) ?>" aria-label="Previous month">‹</a>
    <?php else: ?>
      <span class="np-cal-arrow is-disabled" aria-hidden="true">‹</span>
    <?php endif; ?>

    <select name="cal_m" class="np-cal-select np-cal-select-month" aria-label="Month" onchange="this.form.submit()">
      <?php foreach (NepaliCalendar::NP_MONTHS as $mNum => $mName): ?>
        <option value="<?= $mNum ?>" <?= $calM === $mNum ? 'selected' : '' ?>><?= e($mName) ?></option>
      <?php endforeach; ?>
    </select>

    <?php if ($cal['next']): ?>
      <a class="np-cal-arrow" href="<?= e($calUrl(...$cal['next'])) ?>" aria-label="Next month">›</a>
    <?php else: ?>
      <span class="np-cal-arrow is-disabled" aria-hidden="true">›</span>
    <?php endif; ?>

    <?php if ($prevYear): ?>
      <a class="np-cal-arrow" href="<?= e($calUrl($prevYear, $calM)) ?>" aria-label="Previous year">‹</a>
    <?php else: ?>
      <span class="np-cal-arrow is-disabled" aria-hidden="true">‹</span>
    <?php endif; ?>

    <select name="cal_y" class="np-cal-select np-cal-select-year" aria-label="Year" onchange="this.form.submit()">
      <?php for ($y = 2000; $y <= 2090; $y++): ?>
        <option value="<?= $y ?>" <?= $calY === $y ? 'selected' : '' ?>><?= e($npNum($y)) ?></option>
      <?php endfor; ?>
    </select>

    <?php if ($nextYear): ?>
      <a class="np-cal-arrow" href="<?= e($calUrl($nextYear, $calM)) ?>" aria-label="Next year">›</a>
    <?php else: ?>
      <span class="np-cal-arrow is-disabled" aria-hidden="true">›</span>
    <?php endif; ?>

    <noscript><button type="submit" class="np-cal-go">Go</button></noscript>
  </form>

  <!-- Weekday header -->
  <div class="np-cal-week">
    <?php foreach ($weekHeader as $w): ?><span><?= e($w) ?></span><?php endforeach; ?>
  </div>

  <!-- Day grid -->
  <div class="np-cal-grid">
    <?php foreach ($prevCells as $d): ?>
      <span class="np-cal-cell is-adjacent"><?= e($npNum($d)) ?></span>
    <?php endforeach; ?>

    <?php foreach ($cal['cells'] as $c): ?>
      <span class="np-cal-cell <?= $c['today'] ? 'is-today' : '' ?>"
            title="<?= e($cal['monthName'] . ' ' . $npNum($c['day']) . ', ' . $npNum($calY) . ' · AD ' . $c['ad']) ?>">
        <?= e($npNum($c['day'])) ?>
      </span>
    <?php endforeach; ?>

    <?php foreach ($nextCells as $d): ?>
      <span class="np-cal-cell is-adjacent"><?= e($npNum($d)) ?></span>
    <?php endforeach; ?>
  </div>

  <p class="np-cal-foot">
    <?= e(NepaliCalendar::formatBsNp(['year' => $calY, 'month' => $calM, 'day' => 1])) ?>
    <span class="np-cal-foot-sep">·</span>
    <?= e(NepaliCalendar::formatBsEn(['year' => $calY, 'month' => $calM, 'day' => 1])) ?>
  </p>
</div>
