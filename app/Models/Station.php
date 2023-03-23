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
    public $timestamps=false;
    //
    static public function getStationlist($where,$params){
        $sql = Station::distinct()->select("sid","sname","sstate","city_id","region_id","slng","slat","sinfo","stime")
        
        ->selectRaw('round(ST_Distance_Sphere(point(?,?),point(slng,slat))) as len',[$params['lng'],$params['lat']])
        
        ->selectRaw('(select Convert(json_extract(sinfo, "$.'.$params['service'].'num")-count(appoint.aid),unsigned) from appoint where appoint.sid=station.sid and appoint.atype = ? and appoint.astate = "s" and TO_DAYS(appoint.atime) = TO_DAYS(?)  ) as num',[$params['service'],$params['atime']])

        ->selectRaw('(select count(appoint.aid) from appoint where appoint.sid=station.sid and appoint.atype = ? and appoint.astate = "s" and TO_DAYS(appoint.atime) = TO_DAYS(?)  ) as anum',[$params['service'],$params['atime']])

        ->where($where);
        $orderPara = $params['order']??"";
        if($orderPara==="len"||$orderPara==="num"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('station.sid');
        }elseif($orderPara==="anum"){
            $sql=$sql->orderBy($orderPara)->orderByDesc('station.sid');
        }else{
            $sql=$sql->orderByDesc('station.sid');
        }
        return $sql;
    }
}
