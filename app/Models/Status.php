<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Status
 *
 * @property int $sid 提交编号
 * @property int $spid 提交题目
 * @property int $suid 提交用户
 * @property string $stype 提交结果类型
 * @property string $stime 提交时间
 * @property string $sinfo 提交详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereSinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereSpid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereStime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereStype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereSuid($value)
 * @mixin \Eloquent
 */
class Status extends Model
{
    protected $table="status";
    protected $primaryKey="sid";
    public $timestamps=false;
    //

    static public function getNum($stype='u'){
        return Status::check($stype)->groupBy('sresult')->get();
    }
    static public function getNumByUid($uid,$stype='u'){
        return Status::check($stype)->where('suid',$uid)->groupBy('sresult')->get();
    }
    static public function getNumByUidAndPid($uid,$pid,$stype='u'){
        return Status::check($stype)->where('suid','=',$uid)->where('spid','=',$pid)->groupBy('sresult')->get();
    }
    static public function getNumByUidAndPidAndCid($uid,$pid,$cid,$stype='u'){
        return Status::check($stype)->where('suid','=',$uid)->where('scid','=',$cid)->where('spid','=',$pid)->groupBy('sresult')->get();
    }
    public function getNumByPid($pid,$stype='u'){
        return Status::check($stype)->where('scid','is not',null)->where('spid','=',$pid)->groupBy('sresult')->get();
    }
    static public function getNumByCid($cid,$stype='u'){
        return Status::check($stype)->where('scid','=',$cid)->groupBy('sresult')->get();
    }
    static public function getNumByCidAndPid($cid,$pid,$stype='u'){
        return Status::check($stype)->where('scid','=',$cid)->where('spid','=',$pid)->groupBy('sresult')->get();
    }
    static function check($stype){
        if($stype!==null){
            $sql = Status::select('sresult',DB::raw("count('sid') as num"))->where('stype',$stype);
        }else{
            $sql = Status::select('sresult',DB::raw("count('sid') as num"));
        }
        return $sql;
    }
    static function getSids($pid=null,$uid=null,$cid=null,$stype=null){
        if($pid!==null){
            $sql =  Status::where('spid',$pid);
        }
        if($uid!==null){
            $sql =  Status::where('suid',$uid);
        }
        if($cid!==null){
            $sql =  Status::where('scid',$cid);
        }
        if($stype!==null){
            $sql = $sql=$sql->where('stype',$stype);
        }
        return $sql->pluck('sid')->toArray();
    }
    static public function getSolve($uid,$pid,$cid=null,$stype="u"){
        if($cid!==null){
            $sql=Status::where('scid','=',$cid);
        }else{
            $sql=Status::where('scid','is not',null);
        }
        return $sql->where('stype','=',$stype)->where('sresult','=','a')->where('suid','=',$uid)->where('spid','=',$pid)->orderBy('stime')->first();
    }
}
