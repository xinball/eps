<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\User;
use App\Models\Tag;
use App\Library\Func;
use App\Models\Result;
use App\View\Components\Modal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $config_basic;
    public $config_user;
    public $config_notice;
    public $config_status;
    public $config_problem;
    public $config_contest;

    public $successMsg=null;
    public $infoMsg=null;
    public $warnMsg=null;
    public $errMsg=null;
    public $url=null;
    public $result=null;

    public $luser=null;
    public $ladmin=null;
    public $uid="";
    public $auid="";
    public $key="";
    public $akey="";

    public function __construct(Request $request)
    {
        $this->result=new Result();
        if(isset($_COOKIE['result'])){
            $result = json_decode($_COOKIE['result']);
            $this->result->status=$result->status;
            $this->result->message=$result->message;
            setcookie("result",json_encode([
                'status'=>null,
                'message'=>null
            ],JSON_UNESCAPED_UNICODE),time()-1000,'/');
        }
        if(isset($request->result)){
            $this->result->status=$request->result->status;
            $this->result->message=$request->result->message;
        }
        //配置
        if(count(Redis::hGetAll('basic'))>0){
            $this->config_basic=Redis::hGetAll('basic');
        }else{
            $this->config_basic=[
            'defaultavatar'=>'/bootstrap/icon/person-circle.svg',
            'defaultbanner'=>'/img/banner/redchina.png',
            'useravatar'=>'/img/avatar/',
            'userbanner'=>'/img/banner/',
            'stationavatar'=>'/img/station',
            'locationavatar'=>'/img/location',
            'avatarwidth'=>64,
            'stationwidth'=>64,
            'locationwidth'=>64,
            'bannerwidth'=>400,
            'iplimit'=>3,
            'name'=>'EPS',
            'schollName'=>'吉林大学',
            'register'=>1,
            'status'=>1,
            'copyright'=>'	
            <div class="container" style="z-index:1000;position:relative;">
              <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
                <div class="col-md-4 d-flex align-items-center">
                  <a href="/" title="XinBall" class="text-decoration-none" style="padding-right:5px;"><img src="/img/icon.png" width="24"></a>
                  <span class="text-muted">&copy; 2023 全卿阁, Inc</span>
                </div>
            
                <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                  <li class="ms-3"><a class="text-muted" target="_blank" href="https://github.com/xinball/oj"><i class="bi bi-github"></i></a></li>
                  <li class="ms-3"><a class="text-muted" href="#"></a></li>
                  <li class="ms-3"><a class="text-muted" href="#"></a></li>
                </ul>
              </footer>
            </div>'
            ];
            foreach($this->config_basic as $key=>$value){
                Redis::hSet('basic',$key,$value);
            }
        }
        $this->config_user=count(Redis::hGetAll('user'))>0?Redis::hGetAll('user'):[
            'activettl'=>2,
            'forgetttl'=>2,
            'adminloginttl'=>12000,
            'adminttl'=>6000,
            'userloginttl'=>600,
            'userttl'=>300,
            'listnum'=>20,
            'pagenum'=>3,
            'type'=>'
            {
            "a":"未激活用户",
            "r":"普通用户",
            "s":"管理员",
            "b":"封禁用户",
            "d":"已注销用户"
            }',
            'typekey'=>'{"all":["a","r","s","b","d"]}',
            'adis'=>'
            {
            "sum":{"label":"全部","btn":"dark","num":"badge bg-dark"},
            "s":{"label":"管理","btn":"primary","num":"badge bg-primary"},
            "r":{"label":"普通","btn":"success","num":"badge bg-success"},
            "a":{"label":"未激活","btn":"warning","num":"badge bg-warning"},
            "b":{"label":"封禁","btn":"secondary","num":"badge bg-secondary"},
            "d":{"label":"注销","btn":"danger","num":"badge bg-danger"}
            }',
        ];

        $this->config_notice=count(Redis::hGetAll('notice'))>0?Redis::hGetAll('notice'):[
            'listnum'=>20,
            'pagenum'=>3,
            'adis'=>'
            {
            "sum":{"label":"全部","btn":"dark","num":"badge bg-dark"},
            "s":{"label":"系统","btn":"primary","num":"badge bg-primary"},
            "u":{"label":"更新","btn":"success","num":"badge bg-success"},
            "k":{"label":"知识","btn":"info","num":"badge bg-info"},
            "h":{"label":"隐藏","btn":"secondary","num":"badge bg-secondary"},
            "d":{"label":"已删除","btn":"danger","num":"badge bg-danger"}
            }',
            'type'=>'	
            {
            "s":{"label":"系统","color":"primary"},
            "u":{"label":"更新","color":"success"},
            "k":{"label":"知识","color":"info"},
            "h":{"label":"隐藏","color":"secondary"},
            "d":{"label":"删除","color":"danger"}
            }',
            'typekey'=>'
            {
            "total":["u","s","k","h","d"],
            "all":["u","s","k","h"],
            "d":["d"]
            }',
        ];

        $this->config_station=count(Redis::hGetAll('station'))>0?Redis::hGetAll('station'):[
            'listnum'=>20,
            'pagenum'=>3,
            'state'=>'{
                "o":"开放",
                "c":"关闭",
                "d":"删除"
            }',
            'type'=>'{
                "p":"核酸",
                "r":"抗原",
                "v":"疫苗"
            }',
            'typekey'=>'{"total":["p","r","v"]}',
        ];
        $this->config_appoint=count(Redis::hGetAll('appoint'))>0?Redis::hGetAll('appoint'):[
            'listnum'=>20,
            'pagenum'=>3,
            'state'=>'{
                "n":"创建",
                "s":"提交",
                "r":"拒绝",
                "f":"完成",
                "d":"删除"
            }',
            'statekey'=>'{"total":["n","r","s","f","d"],"all":["n","r","s","f","d"]}',
        ];

        $this->config_location=count(Redis::hGetAll('location'))>0?Redis::hGetAll('location'):[
            'listnum'=>20,
            'pagenum'=>3,
            'type'=>'{
                "o":"开放",
                "c":"关闭",
                "d":"删除"
            }',
        ];

        view()->share('config_basic',$this->config_basic);

        view()->share('config_userpre',$this->config_user);
        $this->config_user['typekey']=json_decode($this->config_user['typekey'],true);
        view()->share('config_user',$this->config_user);

        view()->share('config_noticepre',$this->config_notice);
        $this->config_notice['typekey']=json_decode($this->config_notice['typekey'],true);
        $this->config_notice['type']=json_decode($this->config_notice['type'],true);
        $this->config_notice['adis']=json_decode($this->config_notice['adis'],true);
        view()->share('config_notice',$this->config_notice);

        view()->share('config_stationpre',$this->config_station);
        $this->config_station['typekey']=json_decode($this->config_station['typekey'],true);
        $this->config_station['type']=json_decode($this->config_station['type'],true);
        $this->config_station['state']=json_decode($this->config_station['state'],true);
        view()->share('config_station',$this->config_station);

        view()->share('config_appointpre',$this->config_appoint);
        $this->config_appoint['statekey']=json_decode($this->config_appoint['statekey'],true);
        $this->config_appoint['state']=json_decode($this->config_appoint['state'],true);
        view()->share('config_appoint',$this->config_appoint);

        view()->share('config_locationpre',$this->config_location);
        view()->share('config_location',$this->config_location);

        view()->share('statement',Func::getStatement());
        view()->share('ip',Func::getIp());
        

        // if($this->config_basic['status']!=='1'){
        //     $this->errMsg="网站处于维护状态，无法操作！";
        //     $this->getResult();
        //     return $this->result->toJson();
        // }

        //登录认证
        //如果长时间不操作ttl会减少，初始为600，低于300时操作的话会再加600，减为0则退出，无法操作
        if($this->authAdmin()){
            $this->ladmin=User::where('uid',$this->auid)->first();
            Func::getUser($this->ladmin);
            view()->share("ladmin",$this->ladmin);

            if(Redis::ttl($this->akey)<$this->config_user['adminttl']){
                $expire=time()+Redis::ttl($this->akey)+$this->config_user['adminloginttl'];
                $token=md5($this->auid.rand());
                setcookie("auid",$this->auid,$expire,"/");
                setcookie($this->akey,$token,$expire,"/");
                Redis::setex($this->akey,$expire-time(),$token);
            }
        }
        if($this->authUser()){
            $this->luser=User::where('uid',$this->uid)->first();
            Func::getUser($this->luser);
            view()->share("luser",$this->luser);

            if(Redis::ttl($this->key)<$this->config_user['userttl']){
                $expire=time()+Redis::ttl($this->key)+$this->config_user['userloginttl'];
                $token=md5($this->uid.rand());
                setcookie("uid",$this->uid,$expire,"/");
                setcookie($this->key,$token,$expire,"/");
                Redis::setex($this->key,$expire-time(),$token);
            }
        }

        //组件
        Blade::component('modal',Modal::class);


    }

    //用户权限
    public function authUser(): bool
    {
        $this->uid= $_COOKIE['uid'] ?? '';
        $this->key='token_'.$this->uid;
        $token=$_COOKIE[$this->key]??null;
        return $token&&$token===Redis::get($this->key);
    }

    //管理员权限
    public function authAdmin(): bool
    {
        $this->auid= $_COOKIE['auid'] ?? '';
        $this->akey='atoken_'.$this->auid;
        $token=$_COOKIE[$this->akey]??null;
        return $token&&$token===Redis::get($this->akey);
    }


    public function getSnums($nums){
        $typenum=[
            'sum'=>0,
        ];
        foreach($nums as $num){
            $typenum[$num->sresult]=$num->num;
        }
        foreach($this->config_status['resultkey']['total'] as $type){
            if(!isset($typenum[$type])){
                $typenum[$type]=0;
            }
            $typenum['sum']+=$typenum[$type];
        }
        return $typenum;
    }

    //设置用户为私密
    public function setUserPrivate(User $user){
        if(!$this->luser||$this->luser->uid!==$user->uid){
            if (isset($user->uinfo['private'])&&$user->uinfo['private']==="1"){
                $user->uinfo=[
                    'slogan'=>$user->uinfo['slogan'],
                    'homepage'=>$user->uinfo['homepage'],
                ];
            }
        }
    }

    //用户界面权限
    public function authUserView(Request $request){
        if(!$this->luser){
            $this->errMsg="您没有权限查看用户管理界面！";
            $this->getResult();
            $this->setReqResult($request);
            return app()->call('App\Http\Controllers\NoticeController@listview',[$request]);
        }
        return null;
    }

    //管理员界面权限
    public function authAdminView(Request $request){
        if(!$this->ladmin){
            $this->errMsg="您没有权限查看管理界面！";
            $this->getResult();
            $this->setReqResult($request);
            return app()->call('App\Http\Controllers\NoticeController@listview',[$request]);
        }
        return null;
    }

    //得到指定用户
    public function getUser($uid){
        $user=null;
        if(Func::isEmail($uid)) {
            $user = User::where('uemail', $uid)->first();
        }elseif(Func::isUid($uid)) {
            $user = User::where('uid', $uid)->first();
        }else{
            $user = User::where('uidno', $uid)->first();
        }
        return $user;
    }

    //根据消息等级进行响应
    public function getResult(){
        if($this->errMsg!==null){
            $this->result->status=4;
            $this->result->message=$this->errMsg;
        }elseif($this->warnMsg!==null){
            $this->result->status=3;
            $this->result->message=$this->warnMsg;
        }elseif($this->infoMsg!==null){
            $this->result->status=2;
            $this->result->message=$this->infoMsg;
        }elseif($this->successMsg!==null){
            $this->result->status=1;
            $this->result->message=$this->successMsg;
        }
        if($this->url!==null){
            $this->result->url=$this->url;
        }
    }
    public function listMsg($data){
        if($data->total()>0){
            $this->successMsg=" 我们为您找到了 ".$data->total()." 条符合条件的记录";
            if($data->hasPages()){
                $this->successMsg.="  页面太多？底部页码处输入页数快速到达！";
            }
        }
    }
    public function setReqResult($request){
        $request->result=$this->result;
    }
    //得到指定站点
    public function getStation($station){
        if($station===null){
            return null;
        }
        $station->img=Func::getSAvatar($station->sid);
        $station->stime=json_decode($station->stime,true);
        $station->sinfo=json_decode($station->sinfo);
        if(!isset($station->sinfo->p)){
            $station->sinfo->p=false;
        }
        if(!isset($station->sinfo->r)){
            $station->sinfo->r=false;
        }
        if(!isset($station->sinfo->v)){
            $station->sinfo->v=false;
        }
        if(!isset($station->sinfo->pnum)){
            $station->sinfo->pnum=0;
        }
        if(!isset($station->sinfo->rnum)){
            $station->sinfo->rnum=0;
        }
        if(!isset($station->sinfo->vnum)){
            $station->sinfo->vnum=0;
        }
        return $station;
    }
    //处理预约对象
    public function getAppoint($appoint){
        $appoint->ainfo=json_decode($appoint->ainfo);
        if(isset($appoint->aprocesses)){
            foreach($appoint->aprocesses as $aprocess){
                getAprocess($aprocess);
            }
        }
        return $appoint;
    }
    public function getAprocess($aprocess){
        $aprocess->apinfo=json_decode($aprocess->apinfo);
        return $aprocess;
    }
}
