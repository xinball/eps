<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Library\Func;
/**
 * App\Models\Regions
 *
 * @property int $id
 * @property int $city_id 所属城市代码
 * @property string|null $code
 * @property string|null $name
 * @property string|null $cname 名称
 * @property string|null $lower_name
 * @property string $code_full 地区代码
 * @property string|null $info 防疫政策
 * @method static \Illuminate\Database\Eloquent\Builder|Regions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Regions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Regions query()
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereCname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereCodeFull($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereLowerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereName($value)
 * @mixin \Eloquent
 */
class Regions extends Model
{
    //
    protected $table="regions";
    protected $primaryKey="id";
    static public function getInfo($id,$name,$cname,$code){
        $sql=Regions::join('cities','cities.id','=','regions.city_id')->join('states','states.id','=','cities.state_id')->join('countries','countries.id','=','states.country_id')->join('continents','continents.id','=','countries.continent_id')->select('continents.id AS zid','continents.name AS zname','continents.cname AS zcname','continents.lower_name AS zlname','countries.id AS gid','countries.code AS gcode','countries.name AS gname','countries.full_name AS gfname','countries.cname AS gcname','countries.full_cname AS gfcname','countries.lower_name AS glname','countries.remark AS gremark','countries.info AS ginfo','states.id AS sid','states.code AS scode','states.name AS sname','states.cname AS scname','states.lower_name AS slname','cities.id AS cid','cities.code AS ccode','cities.name AS cname','cities.cname AS ccname','cities.lower_name AS clname','cities.code_full AS ccodef','cities.info AS cinfo','regions.id AS rid','regions.code AS rcode','regions.name AS rname','regions.cname AS rcname','regions.lower_name AS rlname','regions.code_full AS rcodef','regions.info AS rinfo')->where('regions.id',$id);
        if(Func::Length($name)>0)
            $sql=$sql->orwhere('regions.lower_name','like','%'.$name.'%');
        if(Func::Length($cname)>0)
            $sql=$sql->orwhere('regions.cname','like','%'.$cname.'%');
        if(Func::Length($code)>0)
            $sql=$sql->orwhere('regions.code','like','%'.$code.'%');
        return $sql->first();
    }
    static public function getRegionsByCid($city_id){
        return Regions::where('city_id',$city_id)->get();
    }
}