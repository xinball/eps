<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Problem;
use App\Models\Contest;
use App\Models\Tag;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Library\Func;
use App\Library\MessageBox;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class AdminController extends Controller
{
    public function settingview(Request $request){
        if (!$this->checkauth("x")){
            return $this->defaultAdminView($request);
        }
        return view('admin.setting')->with('seactive',true);
    }
    public function noticeview(Request $request){
        if (!$this->checkauth("s")){
            return $this->defaultAdminView($request);
        }
        return view('admin.notice')->with('nactive',true);
    }
    public function userview(Request $request){
        if (!$this->checkauth("s")){
            return $this->defaultAdminView($request);
        }
        return view('admin.user')->with('uactive',true);
    }
    public function stationview(Request $request){
        if (!$this->checkauth()){
            return $this->defaultAdminView($request);
        }
        return view('admin.station')->with('sactive',true);
    }
    public function appointview(Request $request){
        if (!$this->checkauth()){
            return $this->defaultAdminView($request);
        }
        return view('admin.appoint')->with('aactive',true);
    }
    public function reportview(Request $request){
        if (!$this->checkauth()){
            return $this->defaultAdminView($request);
        }
        return view('admin.report')->with('ractive',true);
    }
    public function operationview(Request $request){
        if (!$this->checkauth("s")){
            return $this->defaultAdminView($request);
        }
        return view('admin.operation')->with('oactive',true);
    }
    public function areaview(Request $request){
        if (!$this->checkauth("s")){
            return $this->defaultAdminView($request);
        }
        return view('admin.area')->with('aractive',true);
    }
    public function login(Request $request) {
        //得到登录时的IP
        $ip=Func::getIp();
        if(in_array($ip,Redis::Lrange('ban',0,-1))){
            $this->errMsg="您所在IP已被封禁！";
            $this->getResult();
            return $this->result->toJson();
        }
        //存放输入的数据
        $uidno = mb_strtolower($request->post('uidno', null));
        $uname = $request->post('uname', null);
        $upwd = $request->post('upwd', '');
        $remember = $request->post('remember', '');
        
        //从数据库中得到这个管理员
        $admin = $this->getUserBy($uidno);
        if($admin == null) {
            $this->errMsg = '该管理员不存在！';
        }else{
            $left=Redis::exists("left_".$admin->uid)?Redis::get("left_".$admin->uid):6;
            if(5+$left<=0) {
                $this->errMsg="您所在IP已被封禁！";
                Redis::LPUSH('ban',$ip);
            }elseif($left<=1) {
                $this->errMsg="此用户已被锁定，输入错误".(5+$left)."次后将封禁所在IP，请更改密码或等待解锁后重新登录！";
                Redis::setex("left_".$admin->uid,3600,$left-1);
            }else {
                if($admin->uname!==$uname) {
                    $this->errMsg="身份证明与姓名不匹配！";
                }elseif($admin->utype=='a') {
                    $this->errMsg="用户尚未激活，请激活后登录！";
                }elseif($admin->utype=='d') {
                    $this->errMsg="用户已被删除！";
                }elseif($admin->utype=='r'&&!DB::table("admin_station")->where("uid",$admin->uid)->exists()) {
                    $this->errMsg="没有权限：无可管理站点！";
                }elseif($admin->utype!='s'&&$admin->utype!='x'&&$admin->utype!='r') {
                    $this->errMsg="没有权限：用户不是管理员！";
                }else{
                    //password存真正的密码
                    $password=json_decode($admin->upwd);
                    if(md5($password->auth . $upwd) != $password->pwd) {
                        Redis::setex("left_".$admin->uid,3600,$left-1);
                        $this->errMsg="密码错误，您还有".($left-1)."次机会！";
                    }else{
                        if($this->ladmin&&$admin->uid===$this->ladmin->uid){
                            $this->infoMsg="该用户已登录，无需再次登录！";
                        }else{
                            $this->getUser($admin);
                            if(isset($admin->uinfo->safe)&&$admin->uinfo->safe==='1'&&!$this->checkip($admin,$ip)){
                                $ipttl=$this->config_user['ipttl'];
                                $this->warnMsg="您所在IP尚未登录过本站，请进入您的<strong>邮箱 ".$admin->uemail."</strong> 绑定本机IP进行登录<br/>注意！链接将于".$ipttl."日后过期，请及时激活！";
                                $code=rand(100000,999999).$ip;
                                $ipExpire=3600*24*$ipttl;
                                Redis::setex("ip_".$admin->uid,$ipExpire,$code);
                                Func::sendUserMail($admin,[
                                    'subject'=>$this->config_basic['name']."-用户IP验证",
                                    'text'=>"IP验证",
                                    'link'=>config('var.ui')."?uid=$admin->uid&code=$code",
                                    'expire'=>date("Y-m-d H:i:s",$ipExpire+time())
                                ]);
                            }else{
                                $token=md5($admin->uid.rand());
                                //如果选择保存expire（到期）时间就会长
                                if($remember)
                                    $expire=time()+3600*24*30;
                                else{
                                    $expire=time()+$this->config_user['userloginttl'];
                                }
                                $key="atoken_".$admin->uid;
                                setcookie("auid",$admin->uid,$expire,"/");
                                setcookie($key,$token,$expire,"/");
                                Redis::setex($key,$expire-time(),$token);
                                Redis::del("left_".$admin->uid);
                                $this->successMsg="欢迎回到 ".$this->config_basic['name']." 后台管理！";
                                $this->url=url()->previous();
                            }
                            //返回刚才页面
                            $this->insertOperation($request,$admin->uid,"al");
                        }
                    }
                }
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function logout(Request $request){
        $uid=null;
        if($this->ladmin){//看看管理员是否处于登录状态
            setcookie("auid","",0,"/");
            setcookie($this->akey,"",0,"/");
            Redis::del($this->akey);
            $uid=$this->ladmin->uid;
            $this->ladmin=null;
            view()->share('ladmin',null);
            $this->successMsg="管理员退出登录成功！";
        }else{
            $this->warnMsg="退出登录失败：没有管理员登录中！";
        }
        //返回初始界面
        $this->url=url()->previous();
        if($this->successMsg){
            $this->insertOperation($request,$uid,"alo");
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function alter(Request $request,$config) {
        if (!$this->checkauth("x")){                 //只有系统管理员可以修改网站配置
            $this->errMsg="您没有权限修改网站配置信息，请重新登录！";
        }else{
            if($config==='bans'){
                $bans=json_decode($request->post('bans','[]'),true);
                Redis::del('bans');
                foreach($bans as $ban){
                    Redis::LPUSH('bans',$ban);
                }
            }else{
                $params=$request->all();
                foreach($params as $key=>$param){
                    if(Redis::hGet($config,$key)){
                        Redis::hSet($config,$key,$param);
                    }
                }
            }
            $this->successMsg="修改 ".$config." 配置信息成功！";
        }
        if($this->successMsg){
            $this->insertOperation($request,$this->ladmin->uid,"aal");
        }
        $this->getResult();
        return $this->result->toJson();
    }
}

