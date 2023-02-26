<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Activity
 *
 * @property int $aid 操作编号
 * @property int $auid 操作用户编号
 * @property string $atype 操作类型
 * @property string $atime 操作时间
 * @property string $ainfo 操作详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAuid($value)
 * @mixin \Eloquent
 */
class Activity extends Model
{

    public $timestamps=false;
    protected $table="activity";
    protected $primaryKey="aid";

    //

}
