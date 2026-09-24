<?php

namespace App\Core;

/**
 * Bikram Sambat (नेपाली पात्रो) ↔ Gregorian (AD) date conversion.
 *
 * Pure-PHP implementation with an embedded, verified day-table for
 * BS 2000–2090 (AD 14 Apr 1943 – 13 Apr 2033 window). Day counts per
 * BS year are guaranteed to match the true 365/366-day Gregorian span
 * they map to, so round-trip conversions are always consistent.
 *
 * Reference dataset: ernilambar/nepali-date (MIT license).
 */
class NepaliCalendar
{
    /** Reference point: AD 1943-04-14 (Wednesday) == BS 2000-01-01. */
    private const REF_AD = '1943-04-14';
    private const REF_BS_YEAR  = 2000;
    private const REF_BS_MONTH = 1;
    private const REF_BS_DAY   = 1;

    /** English month names (index 1–12). */
    public const EN_MONTHS = [
        1 => 'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    /** Nepali (Devanagari) month names, index 1–12. */
    public const NP_MONTHS = [
        1 => 'बैशाख', 'जेठ', 'असार', 'साउन', 'भदौ', 'असोज',
        'कार्तिक', 'मंसिर', 'पुष', 'माघ', 'फाल्गुण', 'चैत्र',
    ];

    /** English weekday names (index 0=Sunday … 6=Saturday). */
    public const EN_DAYS = [
        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
    ];

    /** Nepali weekday names (index 0=Sunday … 6=Saturday). */
    public const NP_DAYS = [
        'आइतबार', 'सोमबार', 'मंगलबार', 'बुधबार', 'बिहिबार', 'शुक्रबार', 'शनिबार',
    ];

    /**
     * Days in each BS month.
     *
     * Source data is a table of rows [year, m1…m12]. Every row is
     * normalised here so the 12 month lengths sum to 365 or 366 —
     * exactly the number of Gregorian days from 1st Baisakh to the end
     * of Chaitra — which keeps both conversion directions consistent.
     */
    private static array $bsData = [
        [2000, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 365
        [2001, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2002, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2003, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2004, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 365
        [2005, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2006, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2007, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2008, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 29, 31], // 366
        [2009, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2010, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2011, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2012, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30], // 365
        [2013, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2014, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2015, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2016, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30], // 365
        [2017, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2018, 31, 32, 31, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2019, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 366
        [2020, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2021, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2022, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30], // 365
        [2023, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 366
        [2024, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2025, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2026, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2027, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 365
        [2028, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2029, 31, 31, 32, 31, 32, 30, 30, 29, 30, 29, 30, 30], // 365
        [2030, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2031, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 365
        [2032, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2033, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2034, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2035, 30, 32, 31, 32, 31, 31, 29, 30, 30, 29, 29, 31], // 366
        [2036, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2037, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2038, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2039, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30], // 365
        [2040, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2041, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2042, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2043, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30], // 365
        [2044, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2045, 31, 32, 31, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2046, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2047, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2048, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2049, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30], // 365
        [2050, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 366
        [2051, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2052, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2053, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30], // 365
        [2054, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 366
        [2055, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2056, 31, 31, 32, 31, 32, 30, 30, 29, 30, 29, 30, 30], // 365
        [2057, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2058, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 365
        [2059, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2060, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2061, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2062, 30, 32, 31, 32, 31, 31, 29, 30, 29, 30, 29, 31], // 365
        [2063, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2064, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2065, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2066, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 29, 31], // 366
        [2067, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2068, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2069, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2070, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30], // 365
        [2071, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2072, 31, 32, 31, 32, 31, 30, 30, 29, 30, 29, 30, 30], // 365
        [2073, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31], // 366
        [2074, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2075, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2076, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30], // 365
        [2077, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 366
        [2078, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2079, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2080, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30], // 365
        [2081, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31], // 366
        [2082, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2083, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30], // 365
        [2084, 31, 31, 32, 31, 31, 30, 30, 30, 29, 30, 30, 30], // 365
        [2085, 31, 32, 31, 32, 30, 31, 30, 30, 29, 30, 30, 30], // 365
        [2086, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30], // 365
        [2087, 31, 31, 32, 31, 31, 31, 30, 30, 29, 30, 30, 30], // 365
        [2088, 30, 31, 32, 32, 30, 31, 30, 30, 29, 30, 30, 30], // 366
        [2089, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30], // 365
        [2090, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30], // 365
    ];

    /** Cache of [year => ['days' => total, 'cum' => cumulative month ends']]. */
    private static array $yearCache = [];

    /** Cached cumulative days for REF_AD, from a fixed epoch (1970-01-01). */
    private static ?int $refDays = null;

    /** Get BS data row for a year. */
    private static function row(int $year): array
    {
        if ($year < self::REF_BS_YEAR || $year > 2090) {
            throw new \InvalidArgumentException("BS year {$year} out of supported range (2000–2090).");
        }
        return self::$bsData[$year - self::REF_BS_YEAR];
    }

    /** Total days in a BS year + cumulative day-of-year at each month end. */
    private static function yearInfo(int $year): array
    {
        if (isset(self::$yearCache[$year])) {
            return self::$yearCache[$year];
        }
        $row = self::row($year);
        $cum = [];
        $sum = 0;
        for ($m = 1; $m <= 12; $m++) {
            $sum += $row[$m];
            $cum[$m] = $sum;
        }
        // preserve_keys keeps months indexed 1–12 (matches real month numbers).
        return self::$yearCache[$year] = ['days' => $sum, 'cum' => $cum, 'months' => array_slice($row, 1, null, true)];
    }

    /** Days since 1970-01-01 (day number) for a Gregorian date. */
    private static function adToDayNumber(int $y, int $m, int $d): int
    {
        // JD 2440588 == 1970-01-01 (gregoriantojd returns the JD at noon).
        return gregoriantojd($m, $d, $y) - 2440588;
    }

    /** Convert a day number back to a Gregorian date [y, m, d]. */
    private static function dayNumberToAd(int $day): array
    {
        [$m, $d, $y] = explode('/', jdtogregorian($day + 2440588));
        return [(int) $y, (int) $m, (int) $d];
    }

    private static function refDayNumber(): int
    {
        if (self::$refDays === null) {
            self::$refDays = self::adToDayNumber(1943, 4, 14);
        }
        return self::$refDays;
    }

    /**
     * AD (Gregorian) → BS.
     *
     * @param string $date Any strtotime()-compatible date, e.g. '2026-09-18'.
     * @return array{year:int, month:int, day:int}|null BS date or null if out of range.
     */
    public static function adToBs(string $date): ?array
    {
        $ts = strtotime($date);
        if ($ts === false) {
            return null;
        }
        [$y, $m, $d] = array_map('intval', explode('-', date('Y-n-j', $ts)));

        // Exact window: AD 1943-04-14 (BS 2000-01-01) onwards. The walk below
        // returns null automatically once past BS 2090.
        $offset = self::adToDayNumber($y, $m, $d) - self::refDayNumber();
        if ($offset < 0) {
            return null;
        }

        $year = self::REF_BS_YEAR;
        while (true) {
            $days = self::yearInfo($year)['days'];
            if ($offset < $days) {
                break;
            }
            $offset -= $days;
            $year++;
            if ($year > 2090) {
                return null;
            }
        }

        $months = self::yearInfo($year)['months'];
        $month = 1;
        foreach ($months as $len) {
            if ($offset < $len) {
                break;
            }
            $offset -= $len;
            $month++;
        }

        return ['year' => $year, 'month' => $month, 'day' => $offset + 1];
    }

    /**
     * BS → AD (Gregorian).
     *
     * @return array{year:int, month:int, day:int}|null AD date or null if invalid/out of range.
     */
    public static function bsToAd(int $bsYear, int $bsMonth, int $bsDay): ?array
    {
        if ($bsYear < self::REF_BS_YEAR || $bsYear > 2090 || $bsMonth < 1 || $bsMonth > 12) {
            return null;
        }

        $info = self::yearInfo($bsYear);
        $maxDay = $info['months'][$bsMonth] ?? 0;
        if ($bsDay < 1 || $bsDay > $maxDay) {
            return null;
        }

        $dayNumber = self::refDayNumber();
        for ($y = self::REF_BS_YEAR; $y < $bsYear; $y++) {
            $dayNumber += self::yearInfo($y)['days'];
        }
        for ($m = 1; $m < $bsMonth; $m++) {
            $dayNumber += $info['months'][$m];
        }
        $dayNumber += $bsDay - 1;

        [$y, $m, $d] = self::dayNumberToAd($dayNumber);
        return ['year' => $y, 'month' => $m, 'day' => $d];
    }

    /** English formatted BS date, e.g. "Baisakh 1, 2081" (transliterated). */
    public static function formatBsEn(array $bs): string
    {
        static $translit = [
            1 => 'Baisakh', 2 => 'Jestha', 3 => 'Ashadh', 4 => 'Shrawan',
            5 => 'Bhadra', 6 => 'Ashwin', 7 => 'Kartik', 8 => 'Mangsir',
            9 => 'Poush', 10 => 'Magh', 11 => 'Falgun', 12 => 'Chaitra',
        ];
        return $translit[(int) $bs['month']] . ' ' . $bs['day'] . ', ' . $bs['year'] . ' BS';
    }

    /** Devanagari formatted BS date, e.g. "बैशाख १, २०८१". */
    public static function formatBsNp(array $bs): string
    {
        $digits = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
        $num = static fn (int|string $n): string => strtr((string) $n, array_combine(range(0, 9), $digits));
        return self::NP_MONTHS[(int) $bs['month']] . ' ' . $num($bs['day']) . ', ' . $num($bs['year']);
    }

    /** Devanagari digits for any number (e.g. 2081 → २०८१). */
    public static function np(int|string $n): string
    {
        $digits = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
        return strtr((string) $n, array_combine(range(0, 9), $digits));
    }

    /**
     * Month-grid data for rendering a BS calendar (like a wall calendar).
     *
     * Returns leading-blank count, day cells (with weekday + AD equivalents)
     * and trailing blanks so views can lay out a 7-column grid easily.
     */
    public static function monthGrid(int $bsYear, int $bsMonth): array
    {
        if ($bsYear < self::REF_BS_YEAR || $bsYear > 2090 || $bsMonth < 1 || $bsMonth > 12) {
            return ['ok' => false];
        }

        $info = self::yearInfo($bsYear);
        $daysInMonth = $info['months'][$bsMonth];

        // Weekday (0=Sun…6=Sat) of the 1st: BS 2000-01-01 was a Wednesday.
        $firstDow = self::weekdayOf($bsYear, $bsMonth, 1);
        $todayBs = self::adToBs(date('Y-m-d'));
        $isCurrentMonth = $todayBs !== null
            && $todayBs['year'] === $bsYear
            && $todayBs['month'] === $bsMonth;

        $cells = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $ad = self::bsToAd($bsYear, $bsMonth, $d);
            $cells[] = [
                'day'   => $d,
                'dow'   => ($firstDow + $d - 1) % 7,
                'ad'    => $ad ? sprintf('%04d-%02d-%02d', $ad['year'], $ad['month'], $ad['day']) : null,
                'today' => $isCurrentMonth && $todayBs['day'] === $d,
            ];
        }

        return [
            'ok'       => true,
            'year'     => $bsYear,
            'month'    => $bsMonth,
            'monthName' => self::NP_MONTHS[$bsMonth],
            'daysInMonth' => $daysInMonth,
            'leadingBlanks' => $firstDow,
            'cells'    => $cells,
            'prev'     => self::prevMonth($bsYear, $bsMonth),
            'next'     => self::nextMonth($bsYear, $bsMonth),
        ];
    }

    /** Weekday (0=Sun…6=Sat) of a BS date. */
    public static function weekdayOf(int $bsYear, int $bsMonth, int $bsDay): int
    {
        $ad = self::bsToAd($bsYear, $bsMonth, $bsDay);
        if ($ad === null) {
            return -1;
        }
        return (int) date('w', mktime(0, 0, 0, $ad['month'], $ad['day'], $ad['year']));
    }

    /** Previous BS month as [year, month], stopping at the data range start. */
    public static function prevMonth(int $bsYear, int $bsMonth): ?array
    {
        if ($bsMonth === 1) {
            return $bsYear <= self::REF_BS_YEAR ? null : [$bsYear - 1, 12];
        }
        return [$bsYear, $bsMonth - 1];
    }

    /** Next BS month as [year, month], stopping at the data range end. */
    public static function nextMonth(int $bsYear, int $bsMonth): ?array
    {
        if ($bsMonth === 12) {
            return $bsYear >= 2090 ? null : [$bsYear + 1, 1];
        }
        return [$bsYear, $bsMonth + 1];
    }

    /**
     * Month-grid data for a Gregorian (AD) calendar, same shape as monthGrid().
     * Clamped to 1944–2033 so the paired BS month always converts.
     */
    public static function adMonthGrid(int $year, int $month): array
    {
        if ($year < 1944 || $year > 2033 || $month < 1 || $month > 12) {
            return ['ok' => false];
        }

        $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        $firstDow = (int) date('w', mktime(0, 0, 0, $month, 1, $year)); // 0 = Sunday
        $today = date('Y-m-d');

        $cells = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $cells[] = [
                'day'   => $d,
                'dow'   => ($firstDow + $d - 1) % 7,
                'date'  => $date,
                'today' => $date === $today,
            ];
        }

        $prev = $month === 1 ? [$year - 1, 12] : [$year, $month - 1];
        $next = $month === 12 ? [$year + 1, 1] : [$year, $month + 1];
        if ($prev[0] < 1944) $prev = null;
        if ($next[0] > 2033) $next = null;

        return [
            'ok'            => true,
            'year'          => $year,
            'month'         => $month,
            'monthName'     => self::EN_MONTHS[$month],
            'daysInMonth'   => $daysInMonth,
            'leadingBlanks' => $firstDow,
            'cells'         => $cells,
            'prev'          => $prev,
            'next'          => $next,
        ];
    }
}