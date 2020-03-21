<?php

/**
 * 模型基类
 *
 * @author JiangJian <silverd@sohu.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
    protected $magicVars = [];

    /**
     * all attributes mass assignable
     *
     * @var array
     */
    protected $guarded = [];

    public function __get($var)
    {
        if (in_array($var, $this->magicVars)) {
            return $this->{$var} = $this->$var();
        }

        return parent::__get($var);
    }

    // 保存时移除掉模型的魔术属性
    // 因为魔术属性并不是真正的数据库字段
    public function save(array $options = [])
    {
        if ($this->magicVars) {
            foreach ($this->magicVars as $var) {
                unset($this->{$var});
            }
        }

        return parent::save($options);
    }

    public function modify(array $attributes = [], array $options = [])
    {
        return $this->update($attributes, $options);
    }

    // 批量自增多个字段
    public function increments(array $attributes)
    {
        $setArrs = [];

        foreach ($attributes as $field => $amount) {
            $setArrs[$field] = \DB::raw("`{$field}` + {$amount}");
        }

        return $this->update($setArrs);
    }

    public static function incrsOnDuplicateKey(array $conditions, array $attributes)
    {
        // 存在相同键名的情况下，插入数组的值覆盖更新的值
        $inserts = $conditions + $attributes;

        array_walk($attributes, function (&$value, $field) {
            $value = \DB::raw("`$field` + $value");
        });

        $inserts['created_at'] = $attributes['updated_at'] = now();

        return static::insertOnDuplicateKey($inserts, $attributes);
    }

    // 同时更新字段 & 递增字段
    public function incrWithUpdate(array $incrs, array $updates)
    {
        if ($incrs) {
            array_walk($incrs, function (&$value, $field) {
                $value = \DB::raw("`$field` + $value");
            });
        }

        return $this->fill($incrs + $updates)->save();
    }

    // 同时更新字段 & 递减字段
    public function decrWithUpdate(array $decrs, array $updates)
    {
        if ($decrs) {
            array_walk($decrs, function (&$value, $field) {
                $value = \DB::raw("`$field` - $value");
            });
        }

        return $this->fill($decrs + $updates)->save();
    }
}
