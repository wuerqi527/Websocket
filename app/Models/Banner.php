<?php

namespace App\Models;

class Banner extends AbstractModel
{
    protected $table = 'base_banners';

    const
        STATUS_DISABLED = 0,
        STATUS_ENABLED  = 1;

    // url装饰
    public function getImgUrlAttribute($url)
    {
        return getImgThumbUrl($url, 750, 360);
    }
}
