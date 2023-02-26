<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Contest
 *
 * @property int $cid 比赛编号
 * @property string $ctype 比赛类型，c公开、h隐藏、p私有
 * @property string $cstart 比赛开始后时间
 * @property string $cend 比赛结束时间
 * @property string $ctitle 比赛题目
 * @property string $cdes 比赛描述
 * @property string $coption 比赛选项
 * @property string $cinfo 比赛详细信息
 * @property int $cnum 比赛人数
 * @method static \Illuminate\Database\Eloquent\Builder|Contest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contest query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contest whereCdes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contest whereCend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contest whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contest whereCinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contest whereCstart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contest whereCtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contest whereCtype($value)
 * @mixin \Eloquent
 */
class Contest extends Model
{
    protected $table="contest";
    protected $primaryKey="cid";
    public $timestamps=false;
    //
    static public function getContestsByPid($pid){
        return DB::table('contest_problem')->join('contest','contest_problem.cid','=','contest.cid')->select("cid","ctitle","cdes","ctype","cstart","cend","cnum",'cinfo','coption')->where('pid',$pid)->get();
    }
    static public function getCidsByPid($pid){
        return DB::table('contest_problem')->where('pid',$pid)->pluck('cid')->toArray();
    }
    static public function getCidsByUid($uid,$rtrank=null){
        $sql= DB::table('contest_user')->where('uid',$uid);
        if($rtrank!==null)
            $sql=$sql->join('contest','contest.cid','contest_user.cid')->where('contest.coption->rtrank','=',$rtrank);
        return $sql->pluck('contest.cid')->toArray();
    }
    static public function getCidsByAuid($auid){
        return DB::table('admin_contest')->where('uid',$auid)->pluck('cid')->toArray();
    }
}
