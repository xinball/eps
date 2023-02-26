<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @mixin \Eloquent
 * @property int $uid 用户编号
 * @property int $uidno 身份证号
 * @property string $uemail 邮箱
 * @property string $utype 用户类型
 * @property string $upwd 密码
 * @property string $uname 姓名
 * @property string $uinfo 用户详细信息（JSON）
 * @property string $uidtype 身份证件类型
 * @property string $utime 用户注册时间
 * @property int|null $con_id 所在州
 * @property int|null $coun_id 所在国家
 * @property int|null $state_id 所在州
 * @property int|null $city_id 所在城市
 * @property int|null $region_id 所在地区
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCounId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUidno($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUidtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUtime($value)
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table="user";    //表名字

    protected $primaryKey="uid";    //主键

    public $timestamps=false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [      //防止注入攻击
        'upwd', 'uinfo','utype'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [             //隐藏
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [                               //类型转换
        'email_verified_at' => 'datetime',
    ];


    //将数据库语言转化为php函数的写法，避免出错
    //pluck剩单列，toArray转化为数组
    static public function getAuidsByCid($cid){
        return DB::table('admin_contest')->where('cid',$cid)->orderBy('uid')->pluck('uid')->toArray();
    }
    static public function getAunameByCid($cid){
        return DB::table('admin_contest')->select('uname','uemail')->join('user','user.uid','=','admin_contest.uid')->where('cid',$cid)->orderBy('admin_contest.uid')->get();
    }
    static public function getUidsByCid($cid){
        return DB::table('contest_user')->where('cid',$cid)->pluck('uid')->toArray();
    }

}
