<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Activity
 *
 * @property int $aid 操作编号
 * @property int $auid 操作用户编号
 * @property string $atype 操作类型
 * @property string $atime 操作时间
 * @property string $ainfo 操作详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAuid($value)
 * @mixin \Eloquent
 */
	class Activity extends \Eloquent {}
}

namespace App\Models{
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
	class Contest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Notice
 *
 * @property int $nid 公告编号
 * @property int $nuid 公告管理员编号
 * @property string $ntype 公告类型
 * @property string $ntime 创建公告时间
 * @property string $nupdate 修改公告时间
 * @property string $ntitle 公告标题
 * @property string $ndes 公告描述
 * @property string $ninfo 公告详细信息
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNdes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNupdate($value)
 * @mixin \Eloquent
 */
	class Notice extends \Eloquent {}
}

namespace App\Models{
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
	class Problem extends \Eloquent {}
}

namespace App\Models{
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
	class Status extends \Eloquent {}
}

namespace App\Models{
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
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $uid 用户编号
 * @property int $uidno 身份证号
 * @property string $uemail 邮箱
 * @property string $utype 用户类型
 * @property string $upwd 密码
 * @property string $uname 姓名
 * @property string $uinfo 用户详细信息（JSON）
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUnickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUtype($value)
 * @mixin \Eloquent
 * @property string $uidtype 身份证件类型
 * @property string $utime 用户注册时间
 * @property int|null $con_id 所在州
 * @property int|null $coun_id 所在国家
 * @property int|null $state_id 所在州
 * @property int|null $city_id 所在城市
 * @property int|null $region_id 所在地区
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCounId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUidno($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUidtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUtime($value)
 */
	class User extends \Eloquent {}
}

