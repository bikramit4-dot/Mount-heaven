<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Message extends Model
{
    protected static function table(): string
    {
        return 'messages';
    }

    /** Unread first, then newest (admin inbox). */
    public static function inbox(): array
    {
        return Database::fetchAll('SELECT * FROM messages ORDER BY is_read ASC, created_at DESC');
    }

    /** Newest messages (admin dashboard). */
    public static function latest(int $limit): array
    {
        return Database::fetchAll(
            'SELECT * FROM messages ORDER BY created_at DESC LIMIT ' . (int) $limit
        );
    }

    /** Number of unread messages (admin sidebar badge). */
    public static function unreadCount(): int
    {
        return (int) (Database::fetch(
            'SELECT COUNT(*) AS c FROM messages WHERE is_read = 0'
        )['c'] ?? 0);
    }
}
