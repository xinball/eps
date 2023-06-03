<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Aprocess
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess query()
 * @mixin \Eloquent
 * @property int $apid 预约处理编号
 * @property int $aid 预约编号
 * @property int $uid 处理用户/管理员编号
 * @property mixed $apinfo 处理详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereAid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereApid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereApinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereUid($value)
 * @property string $aptime 预约处理时间
 * @method static \Illuminate\Database\Eloquent\Builder|Aprocess whereAptime($value)
 */
class Aprocess extends Model
{
    //
    protected $table="aprocess";
    protected $primaryKey="apid";
    public $timestamps=false;
    static public function getAprocessByAid($aid){
        return Aprocess::select("apid","user.uid as uid","uname","apinfo","aptime")->where('aid',$aid)->join('user','user.uid','aprocess.uid')->orderByDesc("apid")->get();
    }
    static public function counttoday($aid,$uid){
        return Aprocess::where('aid',$aid)->where('uid',$uid)->where('aptime','<',date("Y-m-d",strtotime("+1 day")))->where('aptime','>',date("Y-m-d",strtotime("today")))->count();
    }
    static public function countappoint($aid,$uid){
        return Aprocess::where('aid',$aid)->where('uid',$uid)->count();
    }
    static public function counttotal($uid){
        return Aprocess::where('uid',$uid)->where('aptime','<',date("Y-m-d",strtotime("+1 day")))->where('aptime','>',date("Y-m-d",strtotime("today")))->count();
    }
}
