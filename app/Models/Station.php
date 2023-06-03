<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
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
 * @property int $state_id 省编号
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereStateId($value)
 */
class Station extends Model
{
    protected $table = "station";
    protected $primaryKey = "sid";
    public $timestamps=false;
    //
    
    static public function getAdminBy($station){
        $sadmin = Station::getSAdminBy($station);
        $aadmin = Station::getAAdminBy($station);
        $uemails = [];
        foreach($sadmin as $item){
            $uemails[] = $item->uemail;
        }
        foreach($aadmin as $item){
            $uemails[] = $item->uemail;
        }
        return $uemails;
    }
    static public function getSAdminBy($station){
        return DB::table("admin_station")->distinct()->select("user.uid","uemail","uname","pri")->where("sid",$station->sid)->orderByDesc("pri")->join("user","user.uid","admin_station.uid")->get();
    }
    static public function getAAdminBy($station){
        return DB::table("admin_area")->distinct()->selectRaw("user.uid,uname,uemail,JSON_ARRAYAGG(type) as types")
        ->where(function ($query) use ($station){
            $query->orWhere(function ($query) use ($station){
                $query->where("type","r")->where("admin_area.region_id",$station->region_id);
            })->orWhere(function ($query) use ($station){
                $query->where("type","c")->where("admin_area.city_id",$station->city_id);
            })->orWhere(function ($query) use ($station){
                $query->where("type","s")->where("admin_area.state_id",$station->state_id);
            });
        })->groupBy("uid","uname","uemail")
        ->join("user","user.uid","admin_area.uid")->get();
    }
    static public function getSid($uid){
        $ssids = Station::getSSid($uid);
        $asids = Station::getASid($uid);
        return array_unique(array_merge($ssids,$asids));
    }
    static public function getSSid($uid){
        return DB::table("admin_station")->distinct()->where("uid",$uid)->pluck('sid')->toArray();
    }
    static public function getASid($uid){
        $areas = DB::table("admin_area")->distinct()->where('uid',$uid)->get();
        $state_ids=[];
        $city_ids=[];
        $region_ids=[];
        foreach($areas as $area){
            if($area->type==='s'){
                $state_ids[]=$area->state_id;
            }elseif($area->type==='c'){
                $city_ids[]=$area->city_id;
            }elseif($area->type==='r'){
                $region_ids[]=$area->region_id;
            }
        }
        $orwhere=[];
        if(count($state_ids)>0){
            $orwhere['state_id']=$state_ids;
        }
        if(count($city_ids)>0){
            $orwhere['city_id']=$city_ids;
        }
        if(count($region_ids)>0){
            $orwhere['region_id']=$region_ids;
        }
        if(count($orwhere)>0){
            return Station::distinct()->where(function($query) use ($orwhere){
                foreach($orwhere as $index=>$item){
                    $query->orWhereIn($index,$item);
                }
            })->pluck('sid')->toArray();
        }else{
            return [];
        }
    }
    static public function getStationlist($where,$params){
        $sql = Station::distinct()->select("sid","sname","sstate","sinfo","slng","slat")
        
        ->selectRaw('round(ST_Distance_Sphere(point(?,?),point(slng,slat))) as len',[$params['lng'],$params['lat']])
        
        ->selectRaw('(select Convert(json_extract(sinfo, "$.'.$params['service'].'num")-count(appoint.aid),unsigned) from appoint where appoint.sid=station.sid and appoint.atype = ? and appoint.astate = "s" and TO_DAYS(appoint.atime) = TO_DAYS(?)  ) as num',[$params['service'],$params['atime']])

        ->selectRaw('(select count(appoint.aid) from appoint where appoint.sid=station.sid and appoint.atype = ? and appoint.astate = "s" and TO_DAYS(appoint.atime) = TO_DAYS(?)  ) as anum',[$params['service'],$params['atime']])

        ->where($where);
        $orderPara = $params['order']??"";
        $decs = $params['desc']??"0";
        if($orderPara==="len"||$orderPara==="num"||$orderPara==="anum"){
            if($decs==='1'){
                $sql=$sql->orderByDesc($orderPara)->orderBy('station.sid');
            }else{
                $sql=$sql->orderBy($orderPara)->orderBy('station.sid');
            }
        }else{
            if($decs==='1'){
                $sql=$sql->orderByDesc('station.sid');
            }else{
                $sql=$sql->orderBy('station.sid');
            }
        }
        return $sql;
    }
}
