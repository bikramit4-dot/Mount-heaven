<?php

namespace App\Models;

use App\Core\Model;

class Teacher extends Model
{
    protected static function table(): string
    {
        return 'teachers';
    }
}
