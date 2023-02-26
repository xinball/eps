<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Station
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Station newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Station newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Station query()
 * @mixin \Eloquent
 * @property int $sid 站点编号
 * @property string $sname 站点名称
 * @property string $sstate 站点状态
 * @property int $city_id 市编号
 * @property int|null $region_id 县编号
 * @property int|null $slng 站点经度
 * @property int|null $slat 站点纬度
 * @property mixed $sinfo 站点详细信息
 * @property mixed $stime 检测时间
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereSinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereSlat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereSlng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereStime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereSstate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereSname($value)
 */
class Station extends Model
{
    protected $table = "station";
    protected $primaryKey = "sid";
    //
}
