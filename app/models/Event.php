<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Event extends Model
{
    protected static function table(): string
    {
        return 'events';
    }

    /** All events, newest date first (public list + admin index). */
    public static function allOrdered(): array
    {
        return Database::fetchAll('SELECT * FROM events ORDER BY event_date DESC');
    }

    /** Upcoming events, soonest first (home page). */
    public static function upcoming(int $limit): array
    {
        return Database::fetchAll(
            'SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT ' . (int) $limit
        );
    }

    /** Other upcoming events for the detail-page sidebar. */
    public static function related(int $excludeId, int $limit): array
    {
        return Database::fetchAll(
            'SELECT * FROM events WHERE id <> ? AND event_date >= CURDATE() ORDER BY event_date LIMIT ' . (int) $limit,
            [$excludeId]
        );
    }
}
