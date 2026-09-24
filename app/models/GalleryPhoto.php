<?php

namespace App\Models;

use App\Core\Model;

class GalleryPhoto extends Model
{
    protected static function table(): string
    {
        return 'gallery_photos';
    }
}
