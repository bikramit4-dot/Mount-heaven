<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class AdmissionEnquiry extends Model
{
    protected static function table(): string
    {
        return 'admissions_enquiries';
    }

    /** Newest first, optionally filtered by workflow status. */
    public static function filtered(?string $status): array
    {
        if ($status !== null && $status !== '') {
            return Database::fetchAll(
                'SELECT * FROM admissions_enquiries WHERE status = ? ORDER BY created_at DESC',
                [$status]
            );
        }
        return Database::fetchAll('SELECT * FROM admissions_enquiries ORDER BY created_at DESC');
    }

    /** Number of enquiries still waiting to be contacted. */
    public static function newCount(): int
    {
        return (int) (Database::fetch(
            'SELECT COUNT(*) AS c FROM admissions_enquiries WHERE status = ?',
            ['new']
        )['c'] ?? 0);
    }
}
