<?php

namespace App\Models;

use App\Core\Model;

class Slider extends Model
{
    protected static function table(): string
    {
        return 'sliders';
    }
}
