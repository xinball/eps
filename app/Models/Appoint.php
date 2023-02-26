<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Appoint
 *
 * @property int $aid 预约编号
 * @property int $uid 预约用户编号
 * @property int $sid 预约站点
 * @property string $atime 预约时间
 * @property string $atype 预约类型
 * @property string $astate 预约状态
 * @property mixed $ainfo 预约详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint query()
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint whereAid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint whereAinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint whereAstate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint whereAtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint whereAtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appoint whereUid($value)
 * @mixin \Eloquent
 */
class Appoint extends Model
{
    //
    protected $table="appoint";
    protected $primaryKey="aid";
}
