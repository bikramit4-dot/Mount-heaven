<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Notice extends Model
{
    protected static function table(): string
    {
        return 'notices';
    }

    /** Pinned first, then newest — the public listing order. */
    public static function allOrdered(): array
    {
        return Database::fetchAll('SELECT * FROM notices ORDER BY is_pinned DESC, published_at DESC');
    }

    /** Pinned first, then newest, limited (home page). */
    public static function latest(int $limit): array
    {
        return Database::fetchAll(
            'SELECT * FROM notices ORDER BY is_pinned DESC, published_at DESC LIMIT ' . (int) $limit
        );
    }

    /** Newest by publish date only (admin dashboard). */
    public static function latestPublished(int $limit): array
    {
        return Database::fetchAll(
            'SELECT * FROM notices ORDER BY published_at DESC LIMIT ' . (int) $limit
        );
    }

    /** Other notices for the detail-page sidebar. */
    public static function related(int $excludeId, int $limit): array
    {
        return Database::fetchAll(
            'SELECT * FROM notices WHERE id <> ? ORDER BY is_pinned DESC, published_at DESC LIMIT ' . (int) $limit,
            [$excludeId]
        );
    }
}
