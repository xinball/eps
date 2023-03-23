<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Library\Func;
/**
 * App\Models\Continents
 *
 * @property int $id 自增id
 * @property string|null $name 英文名
 * @property string|null $cname 中文名
 * @property string|null $lower_name
 * @method static \Illuminate\Database\Eloquent\Builder|Continents newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Continents newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Continents query()
 * @method static \Illuminate\Database\Eloquent\Builder|Continents whereCname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Continents whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Continents whereLowerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Continents whereName($value)
 * @mixin \Eloquent
 */
class Continents extends Model
{
    //
    protected $table="continents";
    protected $primaryKey="id";
    
    static public function getInfo($id,$name,$cname){
        $sql=Continents::where('continents.id',$id);
        if(Func::Length($name)>0)
            $sql=$sql->orwhere('continents.name','like','%'.$name.'%');
        if(Func::Length($cname)>0)
            $sql=$sql->orwhere('continents.cname','like','%'.$cname.'%');
        return $sql->get();
    }

}
