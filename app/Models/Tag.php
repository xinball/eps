<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * App\Models\Tag
 *
 * @property int $tid 标签编号
 * @property int $tnum 标签题目数量
 * @property string $tname 标签名称
 * @property string $tdes 标签详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTaccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTsubmit($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    protected $table="tag";
    protected $primaryKey="tid";
    public $timestamps=false;
    //
    //将数据库语言转化为php函数的写法，避免出错
    //pluck剩单列，toArray转化为数组
    static public function getTagsByPid($pid){
        return DB::table('problem_tag')->join('tag','tag.tid','problem_tag.tid')->select('problem_tag.tid','tname')->where('problem_tag.pid',$pid)->get();
    }
    static public function getTidsByPid($pid){
        return DB::table('problem_tag')->where('pid',$pid)->pluck('tid')->toArray();
    }
    static public function getTidsByUid($uid){
        return DB::table('user_tag')->where('uid',$uid)->pluck('tid')->toArray();
    }
}
