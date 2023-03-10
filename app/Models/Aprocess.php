<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Aprocess
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess query()
 * @mixin \Eloquent
 * @property int $apid 预约处理编号
 * @property int $aid 预约编号
 * @property int $uid 处理用户/管理员编号
 * @property mixed $apinfo 处理详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereAid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereApid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereApinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereUid($value)
 */
class Aprocess extends Model
{
    //
    protected $table="aprocess";
    protected $primaryKey="apid";
}
