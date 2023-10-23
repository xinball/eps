<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Operation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Operation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Operation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Operation query()
 * @mixin \Eloquent
 * @property int $oid 操作编号
 * @property int $uid 操作用户
 * @property string $otime 操作时间
 * @property mixed $oinfo 详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Operation whereOid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Operation whereOinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Operation whereOtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Operation whereUid($value)
 * @property string $otype 操作类型
 * @property mixed $orequest 请求
 * @property mixed $oresult 操作结果
 * @method static \Illuminate\Database\Eloquent\Builder|Operation whereOrequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Operation whereOresult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Operation whereOtype($value)
 */
class Operation extends Model
{
    protected $table="operation";
    protected $primaryKey="oid";
    public $timestamps=false;
    //
}
