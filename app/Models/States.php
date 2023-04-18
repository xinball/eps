<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Library\Func;
/**
 * App\Models\States
 *
 * @property int $id
 * @property int $country_id 所属国家代码
 * @property string|null $code
 * @property string|null $name
 * @property string|null $cname
 * @property string|null $lower_name
 * @property string|null $code_full
 * @property int|null $area_id
 * @method static \Illuminate\Database\Eloquent\Builder|States newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|States newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|States query()
 * @method static \Illuminate\Database\Eloquent\Builder|States whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCodeFull($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereLowerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereName($value)
 * @mixin \Eloquent
 */
class States extends Model
{
    //
    protected $table="states";
    protected $primaryKey="id";
    static public function getInfo($id,$name,$cname,$code){
        $sql=States::join('countries','countries.id','=','states.country_id')->join('continents','continents.id','=','countries.continent_id')->select('continents.id AS zid','continents.name AS zname','continents.cname AS zcname','continents.lower_name AS zlname','countries.id AS gid','countries.code AS gcode','countries.name AS gname','countries.full_name AS gfname','countries.cname AS gcname','countries.full_cname AS gfcname','countries.lower_name AS glname','countries.remark AS gremark','countries.info AS ginfo','states.id AS sid','states.code AS scode','states.name AS sname','states.cname AS scname','states.lower_name AS slname')->where('states.id',$id);
        if(Func::Length($name)>0)
            $sql=$sql->orwhere('states.lower_name','like','%'.$name.'%');
        if(Func::Length($cname)>0)
            $sql=$sql->orwhere('states.cname','like','%'.$cname.'%');
        if(Func::Length($code)>0)
            $sql=$sql->orwhere('states.code','like','%'.$code.'%');
        return $sql->first();
    }
    static public function getStatesByGid($country_id){
        return States::where('country_id',$country_id)->get();
    }
    
}
