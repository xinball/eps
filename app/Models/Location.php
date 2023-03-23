<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Location
 *
 * @property int $lid 报备点编号
 * @property int $city_id 市编号
 * @property int|null $region_id 县编号
 * @property int|null $llng 报备点经度
 * @property int|null $llat 报备点纬度
 * @property mixed $linfo 报备点详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLlat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLlng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereRegionId($value)
 * @mixin \Eloquent
 * @property string $lstate 报备点状态
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLstate($value)
 * @property string $lname 报备点名称
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLname($value)
 */
class Location extends Model
{
    //
    protected $table="location";
    protected $primaryKey="lid";
    public $timestamps=false;
}
