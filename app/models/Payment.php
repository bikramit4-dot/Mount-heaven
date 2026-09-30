<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected static function table(): string
    {
        return 'payments';
    }

    /** Newest first — the admin listing order. */
    public static function latest(int $limit = 200): array
    {
        return \App\Core\Database::fetchAll(
            'SELECT * FROM payments ORDER BY created_at DESC, id DESC LIMIT ' . (int) $limit
        );
    }

    /** Count of payments not yet verified by the office. */
    public static function pendingCount(): int
    {
        return (int) (\App\Core\Database::fetch(
            "SELECT COUNT(*) AS c FROM payments WHERE status = 'pending'"
        )['c'] ?? 0);
    }
}
