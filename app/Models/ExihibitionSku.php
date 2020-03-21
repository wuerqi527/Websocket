<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExihibitionSku extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'base_exihibitions_skus';

    // 无限库存
    const INFINITE_STOCK = 999999999;

    // 所属展会
    public function exihibition()
    {
        return $this->belongsTo(Exihibition::class, 'exihibition_id');
    }

    // 添加库存
    public function addStock(int $count = 1)
    {
        if ($this->stock == self::INFINITE_STOCK) {
            return true;
        }

        $this->increment('stock', $count);
    }

    // 减少库存
    public function subStock(int $count = 1)
    {
        if ($this->stock == self::INFINITE_STOCK) {
            return true;
        }

        $this->decrement('stock', $count);
    }
}
