<?php
/**
 * English (Gregorian / AD) month calendar widget.
 *
 * Expects: $adCal = App\Core\NepaliCalendar::adMonthGrid($y, $m)
 * Optional: $adY, $adM (current selection for the selects)
 *
 * Mirrors the Nepali calendar partial: Mon-first week, adjacent-month
 * days grayed, today marked with a dot, month/year selects + arrows.
 * The month label is shown in both English and Devanagari script.
 */
use App\Core\NepaliCalendar;

$adCal = $adCal ?? ['ok' => false];
if (!$adCal['ok']):
    ?><p class="muted center-text">Calendar data is not available for this date.</p><?php
    return;
endif;

$adY = (int) ($adY ?? $adCal['year']);
$adM = (int) ($adM ?? $adCal['month']);

/** Link URL for a given AD year/month, preserving the BS selection if present. */
$adUrl = static function (int $y, int $m) use ($calY, $calM): string {
    $params = array_filter(['adcal' => $y . '-' . $m, 'cal' => ($calY ?? 0) ? $calY . '-' . $calM : null]);
    return url('/notices') . '?' . http_build_query($params) . '#calendar';
};

/** Nepali name + Devanagari year for the bilingual label. */
$npMonthName = NepaliCalendar::NP_MONTHS[(int) (NepaliCalendar::adToBs(sprintf('%04d-%02d-01', $adY, $adM))['month'] ?? 1)];
$npYear = NepaliCalendar::np(NepaliCalendar::adToBs(sprintf('%04d-%02d-15', $adY, $adM))['year'] ?? $adY);

// Monday-first week header (English short forms).
$weekHeader = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

// Leading cells: grayed tail of the previous month.
$leading = ($adCal['leadingBlanks'] + 6) % 7; // Sunday-first → Monday-first
$prevCells = [];
if ($adCal['prev'] && $leading > 0) {
    [$py, $pm] = $adCal['prev'];
    $daysInPrev = (int) date('t', mktime(0, 0, 0, $pm, 1, $py));
    for ($d = $daysInPrev - $leading + 1; $d <= $daysInPrev; $d++) {
        $prevCells[] = $d;
    }
}

// Trailing cells: grayed head of the next month.
$trailing = (7 - (($leading + $adCal['daysInMonth']) % 7)) % 7;
$nextCells = [];
if ($adCal['next'] && $trailing > 0) {
    for ($d = 1; $d <= $trailing; $d++) {
        $nextCells[] = $d;
    }
}

// Year navigation (clamped to the conversion-safe range).
$prevYear = $adY > 1944 ? $adY - 1 : null;
$nextYear = $adY < 2033 ? $adY + 1 : null;
?>

<div class="np-cal" role="group" aria-label="English calendar">

  <!-- Navigation: [<] month [>]   [<] year [>] -->
  <form class="np-cal-nav" method="get" action="<?= url('/notices') ?>#calendar">
    <input type="hidden" name="cal" value="<?= e($calY . '-' . $calM) ?>">
    <?php if ($adCal['prev']): ?>
      <a class="np-cal-arrow" href="<?= e($adUrl(...$adCal['prev'])) ?>" aria-label="Previous month">‹</a>
    <?php else: ?>
      <span class="np-cal-arrow is-disabled" aria-hidden="true">‹</span>
    <?php endif; ?>

    <select name="adcal_m" class="np-cal-select np-cal-select-month" aria-label="Month" onchange="this.form.submit()">
      <?php foreach (NepaliCalendar::EN_MONTHS as $mNum => $mName): ?>
        <option value="<?= $mNum ?>" <?= $adM === $mNum ? 'selected' : '' ?>><?= e($mName) ?></option>
      <?php endforeach; ?>
    </select>

    <?php if ($adCal['next']): ?>
      <a class="np-cal-arrow" href="<?= e($adUrl(...$adCal['next'])) ?>" aria-label="Next month">›</a>
    <?php else: ?>
      <span class="np-cal-arrow is-disabled" aria-hidden="true">›</span>
    <?php endif; ?>

    <?php if ($prevYear): ?>
      <a class="np-cal-arrow" href="<?= e($adUrl($prevYear, $adM)) ?>" aria-label="Previous year">‹</a>
    <?php else: ?>
      <span class="np-cal-arrow is-disabled" aria-hidden="true">‹</span>
    <?php endif; ?>

    <select name="adcal_y" class="np-cal-select np-cal-select-year" aria-label="Year" onchange="this.form.submit()">
      <?php for ($y = 1944; $y <= 2033; $y++): ?>
        <option value="<?= $y ?>" <?= $adY === $y ? 'selected' : '' ?>><?= e((string) $y) ?></option>
      <?php endfor; ?>
    </select>

    <?php if ($nextYear): ?>
      <a class="np-cal-arrow" href="<?= e($adUrl($nextYear, $adM)) ?>" aria-label="Next year">›</a>
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
      <span class="np-cal-cell is-adjacent"><?= e((string) $d) ?></span>
    <?php endforeach; ?>

    <?php foreach ($adCal['cells'] as $c): ?>
      <span class="np-cal-cell <?= $c['today'] ? 'is-today' : '' ?>"
            title="<?= e($adCal['monthName'] . ' ' . $c['day'] . ', ' . $adY) ?>">
        <?= e((string) $c['day']) ?>
      </span>
    <?php endforeach; ?>

    <?php foreach ($nextCells as $d): ?>
      <span class="np-cal-cell is-adjacent"><?= e((string) $d) ?></span>
    <?php endforeach; ?>
  </div>

  <p class="np-cal-foot">
    <?= e($adCal['monthName'] . ' ' . $adY) ?>
    <span class="np-cal-foot-sep">·</span>
    <span class="np"><?= e($npMonthName . ' ' . $npYear) ?></span>
  </p>
</div>
