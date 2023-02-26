<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Library\Func;

/**
 * App\Models\Countries
 *
 * @property int $id
 * @property int|null $continent_id
 * @property string $code 地区代码
 * @property string|null $name 名称
 * @property string|null $full_name
 * @property string|null $cname
 * @property string|null $full_cname
 * @property string|null $lower_name
 * @property string|null $remark
 * @property string|null $info 防疫政策
 * @method static \Illuminate\Database\Eloquent\Builder|Countries newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries query()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereCname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereContinentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereFullCname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereLowerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereRemark($value)
 * @mixin \Eloquent
 */
class Countries extends Model
{
    //
    protected $table="countries";
    protected $primaryKey="id";
    static public function getCountriesByZid($continent_id){
        return (new Countries())->where('continent_id',$continent_id)->get();
    }
    static public function getInfo($id,$name,$cname,$fname,$fcname,$code){
        $sql=(new Countries())->join('continents','continents.id','=','countries.continent_id')->select('continents.id AS zid','continents.name AS zname','continents.cname AS zcname','continents.lower_name AS zlname','countries.id AS gid','countries.code AS gcode','countries.name AS gname','countries.full_name AS gfname','countries.cname AS gcname','countries.full_cname AS gfcname','countries.lower_name AS glname','countries.remark AS gremark','countries.info AS ginfo')->where('countries.id',$id);
        if(Func::Length($name)>0)
            $sql=$sql->orwhere('countries.name','like','%'.$name.'%');
        if(Func::Length($cname)>0)
            $sql=$sql->orwhere('countries.cname','like','%'.$cname.'%');
        if(Func::Length($fname)>0)
            $sql=$sql->orwhere('countries.lower_name','like','%'.$fname.'%');
        if(Func::Length($fcname)>0)
            $sql=$sql->orwhere('countries.full_cname','like','%'.$fcname.'%');
        if(Func::Length($code)>0)
            $sql=$sql->orwhere('countries.code','like','%'.$code.'%');
        return $sql->get();
    }

}

