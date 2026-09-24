<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\NepaliCalendar;

/**
 * Small JSON endpoint powering the paired BS ⇄ AD date inputs on the
 * admission form. GET only, no side effects.
 *
 *   /calendar/convert?from=bs&date=2082-05-12
 *   /calendar/convert?from=ad&date=2015-05-10
 */
class CalendarController extends Controller
{
    public function convert(): string
    {
        header('Content-Type: application/json; charset=utf-8');

        $from = strtolower(trim((string) ($_GET['from'] ?? 'bs')));
        $raw  = trim((string) ($_GET['date'] ?? ''));

        // Users may type Devanagari digits (२०८२) — normalise to ASCII.
        $ascii = strtr($raw, [
            '०' => '0', '१' => '1', '२' => '2', '३' => '3', '४' => '4',
            '५' => '5', '६' => '6', '७' => '7', '८' => '8', '९' => '9',
        ]);

        if (!preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $ascii, $m)) {
            return json_encode(['ok' => false, 'error' => 'Date format must be YYYY-MM-DD.']);
        }
        $y = (int) $m[1];
        $mo = (int) $m[2];
        $d = (int) $m[3];

        if ($from === 'bs') {
            $ad = NepaliCalendar::bsToAd($y, $mo, $d);
            if ($ad === null) {
                return json_encode([
                    'ok' => false,
                    'error' => 'Not a valid BS date. Supported range: 2000 – 2090 BS.',
                ]);
            }
            $adStr = sprintf('%04d-%02d-%02d', $ad['year'], $ad['month'], $ad['day']);
            return json_encode([
                'ok'        => true,
                'ad'        => $adStr,
                'np_label'  => NepaliCalendar::formatBsNp(['year' => $y, 'month' => $mo, 'day' => $d]),
                'en_label'  => NepaliCalendar::formatBsEn(['year' => $y, 'month' => $mo, 'day' => $d]),
            ]);
        }

        if ($from === 'ad') {
            $bs = NepaliCalendar::adToBs(sprintf('%04d-%02d-%02d', $y, $mo, $d));
            if ($bs === null) {
                return json_encode([
                    'ok' => false,
                    'error' => 'Date out of supported range (1944 – 2033 AD).',
                ]);
            }
            $bsStr = sprintf('%04d-%02d-%02d', $bs['year'], $bs['month'], $bs['day']);
            return json_encode([
                'ok'       => true,
                'bs'       => $bsStr,
                'np_label' => NepaliCalendar::formatBsNp($bs),
                'en_label' => NepaliCalendar::formatBsEn($bs),
            ]);
        }

        return json_encode(['ok' => false, 'error' => 'Unknown calendar type. Use from=bs or from=ad.']);
    }

    /**
     * Month-grid JSON for the BS picker popup.
     *   /calendar/month?y=2082&m=5
     */
    public function month(): string
    {
        header('Content-Type: application/json; charset=utf-8');

        $y = (int) ($_GET['y'] ?? 0);
        $m = (int) ($_GET['m'] ?? 0);

        $grid = NepaliCalendar::monthGrid($y, $m);
        if (!$grid['ok']) {
            return json_encode(['ok' => false, 'error' => 'Supported BS range: 2000 – 2090.']);
        }

        // Compact cells for the popup: day number + ISO AD date.
        $cells = array_map(static fn (array $c): array => [
            'd' => $c['day'],
            'today' => $c['today'],
            'ad' => $c['ad'],
        ], $grid['cells']);

        return json_encode([
            'ok'        => true,
            'year'      => $grid['year'],
            'month'     => $grid['month'],
            'monthName' => $grid['monthName'],
            'yearNp'    => NepaliCalendar::np($grid['year']),
            'leading'   => $grid['leadingBlanks'],
            'days'      => $cells,
            'prev'      => $grid['prev'],
            'next'      => $grid['next'],
        ]);
    }
}
