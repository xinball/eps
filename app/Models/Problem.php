<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * App\Models\Problem
 *
 * @property int $pid 题目编号
 * @property int $puid
 * @property string $ptype 题目状态
 * @property int $psubmit 提交数量
 * @property int $pacrate AC率
 * @property int $pac AC数量
 * @property int $pce CE数量
 * @property int $pwa WA数量
 * @property int $pre RE数量
 * @property int $ptl TL数量
 * @property int $pml ML数量
 * @property int $pse SE数量
 * @property string $ptitle 题目标题
 * @property string $pdes 描述
 * @property string $poption 题目选项（JSON）
 * @property string $pinfo 题目信息（JSON）
 * @property string $pcases 样例（JSON）
 * @method static \Illuminate\Database\Eloquent\Builder|Problem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Problem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Problem query()
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePaccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePcases($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePdes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePsubmit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Problem wherePuid($value)
 * @mixin \Eloquent
 */
class Problem extends Model
{
    protected $table="problem";
    protected $primaryKey="pid";
    public $timestamps=false;

    static public function getPidsByCid($cid){
        return DB::table('contest_problem')->where('cid',$cid)->orderBy('ordernum')->pluck('pid')->toArray();
    }
    static public function getPtitleByCid($cid){
        return DB::table('contest_problem')->select('ptitle','pdes')->join('problem','problem.pid','=','contest_problem.pid')->where('cid',$cid)->orderBy('contest_problem.ordernum')->get();
    }
    static public function getPidsByUid($uid){
        return Problem::where('puid',$uid)->orderBy('pid')->pluck('pid')->toArray();
    }
}
