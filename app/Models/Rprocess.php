<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Rprocess
 *
 * @property int $rpid 报备处理编号
 * @property int $rid 报备编号
 * @property int $uid 处理用户编号
 * @property mixed $rpinfo 处理详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Rprocess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rprocess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rprocess query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rprocess whereRid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rprocess whereRpid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rprocess whereRpinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rprocess whereUid($value)
 * @mixin \Eloquent
 */
class Rprocess extends Model
{
    //
    protected $table="rprocess";
    protected $primaryKey="rpid";
}
