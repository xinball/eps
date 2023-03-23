<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Library\Func;
/**
 * App\Models\Cities
 *
 * @property int $id
 * @property int $state_id 所属州省代码
 * @property string|null $code
 * @property string|null $name
 * @property string|null $cname
 * @property string|null $lower_name
 * @property string $code_full 地区代码
 * @property string|null $info 防疫政策
 * @method static \Illuminate\Database\Eloquent\Builder|Cities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cities query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereCname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereCodeFull($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereLowerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereStateId($value)
 * @mixin \Eloquent
 */
class Cities extends Model
{
    //
    protected $table="cities";
    protected $primaryKey="id";
    static public function getInfo($id,$name,$cname,$code){
        $sql=Cities::join('states','states.id','=','cities.state_id')->join('countries','countries.id','=','states.country_id')->join('continents','continents.id','=','countries.continent_id')->select('continents.id AS zid','continents.name AS zname','continents.cname AS zcname','continents.lower_name AS zlname','countries.id AS gid','countries.code AS gcode','countries.name AS gname','countries.full_name AS gfname','countries.cname AS gcname','countries.full_cname AS gfcname','countries.lower_name AS glname','countries.remark AS gremark','countries.info AS ginfo','states.id AS sid','states.code AS scode','states.name AS sname','states.cname AS scname','states.lower_name AS slname','cities.id AS cid','cities.code AS ccode','cities.name AS cname','cities.cname AS ccname','cities.lower_name AS clname','cities.code_full AS ccodef','cities.info AS cinfo')->where('cities.id',$id);
        if(Func::Length($name)>0)
            $sql=$sql->orwhere('cities.lower_name','like','%'.$name.'%');
        if(Func::Length($cname)>0)
            $sql=$sql->orwhere('cities.cname','like','%'.$cname.'%');
        if(Func::Length($code)>0)
            $sql=$sql->orwhere('cities.code_full','like','%'.$code.'%');
        return $sql->get();
    }

    static public function getCitiesBySid($state_id){
        return Cities::where('state_id',$state_id)->get();
    }
}
