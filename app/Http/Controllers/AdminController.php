<?php

namespace App\Http\Controllers;

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
        if ($auth=$this->authAdminView($request)){
            return $auth;
        }
        return view('admin.setting')->with('seactive',true);
    }
    public function stationview(Request $request){
        if ($auth=$this->authAdminView($request)){
            return $auth;
        }
        return view('admin.station')->with('sactive',true);
    }
    public function locationview(Request $request){
        if ($auth=$this->authAdminView($request)){
            return $auth;
        }
        return view('admin.location')->with('lactive',true);
    }
    public function appointview(Request $request){
        if ($auth=$this->authAdminView($request)){
            return $auth;
        }
        return view('admin.appoint')->with('aactive',true);
    }
    public function reportview(Request $request){
        if ($auth=$this->authAdminView($request)){
            return $auth;
        }
        return view('admin.report')->with('ractive',true);
    }
    public function noticeview(Request $request){
        if ($auth=$this->authAdminView($request)){
            return $auth;
        }
        return view('admin.notice')->with('nactive',true);
    }


    public function tagview(Request $request){
        if ($auth=$this->authAdminView($request)){
            return $auth;
        }
        $this->result->tags=Tag::all();
        return view('admin.tag')->with('tactive',true)->with('result',$this->result->toJson());
    }
    public function userview(Request $request){
        if ($auth=$this->authAdminView($request)){
            return $auth;
        }
        return view('admin.user')->with('uactive',true)->with('ban',Redis::lrange('ban',0,-1));
    }

    public function login(Request $request) {
        $ip=Func::getIp();
        if(in_array($ip,Redis::Lrange('ban',0,-1))){
            $this->errMsg="?????????IP???????????????";
            $this->getResult();
            return $this->result->toJson();
        }
        $uidno = mb_strtolower($request->post('uidno', null));
        $uname = $request->post('uname', null);
        $upwd = $request->post('upwd', '');
        $remember = $request->post('remember', '');
        
        $admin = $this->getUser($uidno);
        if($admin == null) {
            $this->errMsg = '????????????????????????';
        }else{
            $left=Redis::exists("left_".$admin->uid)?Redis::get("left_".$admin->uid):6;
            if(5+$left<=0) {
                $this->errMsg="?????????IP???????????????";
                Redis::LPUSH('ban',$ip);
            }elseif($left<=1) {
                $this->errMsg="????????????????????????????????????".(5+$left)."?????????????????????IP???????????????????????????????????????????????????";
                Redis::setex("left_".$admin->uid,3600,$left-1);
            }else {
                if($admin->uname!==$uname) {
                    $this->errMsg="?????????????????????????????????";
                }elseif($admin->utype=='a') {
                    $this->errMsg="??????????????????????????????????????????";
                }elseif($admin->utype=='d') {
                    $this->errMsg="?????????????????????";
                }elseif($admin->utype!='s'&&$admin->utype!='x') {
                    $this->errMsg="???????????????????????????????????????";
                }else{
                    $password=json_decode($admin->upwd);
                    if(md5($password->auth . $upwd) != $password->pwd) {
                        $left=Redis::exists("left_".$admin->uid)?Redis::get("left_".$admin->uid):6;
                        Redis::setex("left_".$admin->uid,3600,$left-1);
                        $this->errMsg="????????????????????????".($left-1)."????????????";
                    }else{
                        $token=md5($admin->uid.rand());
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
                        $this->successMsg="???????????? ".$this->config_basic['name']." ???????????????";
                    }
                }
            }
        }
        if($this->successMsg)
            $this->url=url()->previous();
        $this->getResult();
        return $this->result->toJson();
    }


    public function logout(Request $request){
        if($this->ladmin){
            setcookie("auid","",0,"/");
            setcookie($this->akey,"",0,"/");
            Redis::del($this->akey);
            $this->ladmin=null;
            view()->share('ladmin',null);
            $this->successMsg="??????????????????????????????";
        }else{
            $this->warnMsg="????????????????????????????????????????????????";
        }
        $this->url=url()->previous();
        $this->getResult();
        return $this->result->toJson();
    }

    public function alter(Request $request,$config) {
        if (!$this->ladmin){
            $this->errMsg="????????????????????????????????????????????????????????????";
        }else{
            if($config==='ban'){
                $bans=json_decode($request->post('ban','[]'),true);
                Redis::del('ban');
                foreach($bans as $ban){
                    Redis::LPUSH('ban',$ban);
                }
            }else{
                $params=$request->all();
                foreach($params as $key=>$param){
                    if(Redis::hGet($config,$key)){
                        Redis::hSet($config,$key,$param);
                    }
                }
            }
            $this->successMsg="?????? ".$config." ?????????????????????";
        }
        $this->getResult();
        return $this->result->toJson();
    }
}

