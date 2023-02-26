<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notice
 *
 * @property int $nid 公告编号
 * @property int $nuid 公告管理员编号
 * @property string $ntype 公告类型
 * @property string $ntime 创建公告时间
 * @property string $nupdate 修改公告时间
 * @property string $ntitle 公告标题
 * @property string $ndes 公告描述
 * @property string $ninfo 公告详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNdes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNupdate($value)
 * @mixin \Eloquent
 */
class Notice extends Model
{
    protected $table="notice";
    protected $primaryKey="nid";
    public $timestamps=false;
    //
}
