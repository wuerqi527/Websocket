<?php

namespace App\Models;

class Area extends AbstractModel
{
    protected $table = 'areas';

    protected $primaryKey = 'area_code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $visible = [
        'area_name',
        'area_code',
    ];

    // area_level等级
    const
        LEVEL_COUNTRY  = 0, // 国
        LEVEL_PROVINCE = 1, // 省
        LEVEL_CITY     = 2, // 市
        LEVEL_DISTRICT = 3; // 区

    // 父级区域
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_code', 'area_code');
    }

    // 子级区域
    public function children()
    {
        return $this->hasMany(self::class, 'parent_code', 'area_code');
    }
}
