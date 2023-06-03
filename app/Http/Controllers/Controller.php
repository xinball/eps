<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Operation;
use App\Models\Status;
use App\Models\Aprocess;
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
use Jenssegers\Agent\Agent;

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

    public function __construct(Request $request=null)
    {
        $this->initConfig();
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
        //登录认证
        //如果长时间不操作ttl会减少，初始为600，低于300时操作的话会再加600，减为0则退出，无法操作
        if($this->authAdmin()){
            $this->ladmin=User::where('uid',$this->auid)->first();
            $this->getUser($this->ladmin);
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
            $this->getUser($this->luser);
            view()->share("luser",$this->luser);

            if(Redis::ttl($this->key)<$this->config_user['userttl']){
                $expire=time()+Redis::ttl($this->key)+$this->config_user['userloginttl'];
                $token=md5($this->uid.rand());
                setcookie("uid",$this->uid,$expire,"/");
                setcookie($this->key,$token,$expire,"/");
                Redis::setex($this->key,$expire-time(),$token);
            }
        }
        if($this->config_basic['status']==='0'){
            if($this->ladmin===null&&$this->getCurrentControllerName()!=='App\Http\Controllers\AdminController'&&$this->getCurrentControllerName()!=='App\Http\Controllers\NoticeController'){
                abort(403);
            }
        }
    }
    //检测权利够不够
    public function checkauth($utype=null){
        if($this->ladmin!==null){
            if($utype==="s"){
                if(($this->ladmin->utype==='s'||$this->ladmin->utype==='x'))
                    return true;
                else
                    return false;
            }elseif($utype==="x"){
                if($this->ladmin->utype==='x')
                    return true;
                else
                    return false;
            }else{
                return true;
            }
        }
        return false;
    }
    public function checkip($user,$ip){
        $ips = Operation::distinct()->where('uid',$user->uid)->where('oresult->status',1)
        ->where(function ($query){
            $query->orWhere('otype','ul')->orWhere('otype','al');
        })->pluck("oip")->toArray();
        foreach($ips as $item){
            if($ip===$item){
                return true;
            }
        }
        $ips=$user->allowip;
        foreach($ips as $item){
            if($ip===$item){
                return true;
            }
        }
        return false;
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
            if (isset($user->uinfo->private)&&$user->uinfo->private==="1"){
                $user->uinfo=[
                    'slogan'=>$user->uinfo->slogan,
                    'homepage'=>$user->uinfo->homepage,
                ];
            }
            unset($user->allowip);
        }
    }

    //用户界面权限
    public function defaultUserView(Request $request){
        $request->errMsg="您没有权限查看用户管理界面！";
        return app()->call('App\Http\Controllers\NoticeController@listview',[$request]);
    }

    //管理员界面权限
    public function defaultAdminView(Request $request){
        $request->errMsg="您没有权限查看该管理界面，请重新登陆！";
        return app()->call('App\Http\Controllers\NoticeController@listview',[$request]);
    }

    //得到指定用户
    public function getUserBy($uid){
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
                $this->successMsg.="<br>页面太多？底部页码处输入页数快速到达！";
            }
        }
    }
    // public function setReqResult($request){
    //     $request->result=$this->result;
    // }
    //补充获取用户信息，头像和背景，并对json格式字符串解码
    public function getUser(User $user){
        $user->avatar=$this->getAvatar($user->uid);
        $user->banner=$this->getBanner($user->uid);
        $user->allowip=json_decode($user->allowip)??[];
        $user->uinfo=json_decode($user->uinfo);
        $user->uinfo->addr=$user->uinfo->addr??"";
        $user->uinfo->lang=$user->uinfo->lang??"cn";
        $user->uinfo->private=$user->uinfo->private??"1";
        $user->uinfo->safe=$user->uinfo->safe??"0";
        $user->uinfo->safemail=$user->uinfo->safemail??"0";
        $user->uinfo->tel=$user->uinfo->tel??"";
        $user->uinfo->slogan=$user->uinfo->slogan??"";
        $user->uinfo->sex=$user->uinfo->sex??"2";
        $user->uinfo->homepage=$user->uinfo->homepage??"";
        $user->uinfo->homepagessl=$user->uinfo->homepagessl??"1";
        $user->uinfo->qq=$user->uinfo->qq??"";
        $user->uinfo->addr=$user->uinfo->addr??"";
        $user->uinfo->wid=$user->uinfo->wid??"";
        $user->uinfo->reg_ip=$user->uinfo->reg_ip??"";
    }
    //登录页面，输入完身份证或者邮箱后，点击别处会刷新，如果该用户有自己头像就显示自己头像，没有就显示默认头像
    public function getAvatar($uid=null){
        $basicConfig=Redis::hGetAll("basic");
        $default=($basicConfig['defaultavatar'] ?? "/img/favicon.png");
        if($uid){
            $href=($basicConfig['useravatar'] ?? "/img/avatar/").$uid.".png";
            if(is_file(public_path($href))){
                return $href."?".filectime(public_path($href));
            }
        }
        return $default."?".filectime(public_path($default));
    }
    //得到站点的头像，没有就用默认头像
    public function getSAvatar($sid=null){
        if($sid){
            $href=($basicConfig['stationavatar'] ?? "/img/station/").$sid.".png";
            if(is_file(public_path($href))){
                return url('/').$href."?".filectime(public_path($href));
            }
        }
        return url('/bootstrap/icon/building.svg');
    }
    //有则用自己，没有用默认
    public function getBanner($uid=null){
        $basicConfig=Redis::hGetAll("basic");
        $default=($basicConfig['defaultbanner'] ?? "/img/banner/redchina.png");
        if($uid){
            $href=($basicConfig['userbanner'] ?? "/img/banner/").$uid.".png";
            if(is_file(public_path($href))){
                return $href."?".filectime(public_path($href));
            }
        }
        return $default."?".filectime(public_path($default));
    }
    //得到指定站点
    public function getStation($station){
        if($station===null){
            return null;
        }
        $station->img=$this->getSAvatar($station->sid);
        $station->stime=json_decode($station->stime,true);
        $station->sinfo=json_decode($station->sinfo);
        if(!isset($station->sinfo->a)){
            $station->sinfo->p="0";
        }
        if(!isset($station->sinfo->approvetime)){
            $station->sinfo->approvetime="0";
        }
        if(!isset($station->sinfo->a)){
            $station->sinfo->a="0";
        }
        if(!isset($station->sinfo->r)){
            $station->sinfo->r="0";
        }
        if(!isset($station->sinfo->v)){
            $station->sinfo->v="0";
        }
        if(!isset($station->sinfo->anum)){
            $station->sinfo->anum=0;
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
                $this->getAprocess($aprocess);
            }
        }
        return $appoint;
    }
    public function getAprocess($aprocess){
        $aprocess->apinfo=json_decode($aprocess->apinfo);
        return $aprocess;
    }
    public function checkstationauth($station,$uid){//站点权限
        return $this->checkstationinfoauth($station->region_id,$station->city_id,$station->state_id,$uid,$station->sid);
    }
    public function checkstationaddr($region_id,$city_id,$state_id){//地址有效性
        if($region_id!==null){
            return DB::table("regions")->where("id",$region_id)->where("city_id",$city_id)->exists()&&DB::table("cities")->where("id",$city_id)->where("state_id",$state_id)->exists();
        }elseif($city_id!==null){
            return DB::table("cities")->where("id",$city_id)->where("state_id",$state_id)->exists();
        }
        return false;
    }
    public function checkaddr($con_id,$coun_id=null,$state_id=null,$city_id=null,$region_id=null){//地址有效性
        $conflag=DB::table("continents")->where("id",$con_id)->exists();
        $counflag=DB::table("countries")->where("id",$coun_id)->where("continent_id",$con_id)->exists();
        $stateflag=DB::table("states")->where("id",$state_id)->where("country_id",$coun_id)->exists();
        $cityflag=DB::table("cities")->where("id",$city_id)->where("state_id",$state_id)->exists();
        $regionflag=DB::table("regions")->where("id",$region_id)->where("city_id",$city_id)->exists();
        if($coun_id===null){
            return $conflag;
        }elseif($state_id===null){
            return $conflag&&$counflag;
        }elseif($city_id===null){
            return $conflag&&$counflag&&$stateflag;
        }elseif($region_id===null){
            return $conflag&&$counflag&&$stateflag&&$cityflag;
        }else{
            return $conflag&&$counflag&&$stateflag&&$cityflag&&$regionflag;
        }
    }
    public function checkstationinfoauth($region_id,$city_id,$state_id,$uid,$sid=null){//站点权限
        if($region_id!==null&&DB::table("admin_area")->where("type","r")->where("region_id",$region_id)->where("uid",$uid)->exists())
            return 1;
        elseif($city_id!==null&&DB::table("admin_area")->where("type","c")->where("city_id",$city_id)->where("uid",$uid)->exists())
            return 2;
        elseif($state_id!==null&&DB::table("admin_area")->where("type","s")->where("state_id",$state_id)->where("uid",$uid)->exists())
            return 3;
        elseif($sid!==null&&DB::table('admin_station')->where('sid','=',$sid)->where('uid','=',$uid)->exists())
            return 4;
        elseif($this->checkauth('x'))
            return 5;
        return false;
    }
    private function initConfig(){
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
        $this->config_operation=count(Redis::hGetAll('operation'))>0?Redis::hGetAll('operation'):[
            'listnum'=>20,
            'pagenum'=>3,
            'otype'=>'{
                "ul":"创建",
                "s":"提交",
                "r":"拒绝",
                "f":"完成",
                "d":"删除"
            }',
        ];

        $this->config_basic['bans']=json_decode($this->config_basic['bans'],true);
        view()->share('config_basic',$this->config_basic);

        view()->share('config_userpre',$this->config_user);
        $this->config_user['typekey']=json_decode($this->config_user['typekey'],true);
        $this->config_user['idnotype']=json_decode($this->config_user['idnotype'],true);
        $this->config_user['adis']=json_decode($this->config_user['adis'],true);
        view()->share('config_user',$this->config_user);

        view()->share('config_noticepre',$this->config_notice);
        $this->config_notice['typekey']=json_decode($this->config_notice['typekey'],true);
        $this->config_notice['type']=json_decode($this->config_notice['type'],true);
        $this->config_notice['adis']=json_decode($this->config_notice['adis'],true);
        view()->share('config_notice',$this->config_notice);

        view()->share('config_stationpre',$this->config_station);
        $this->config_station['typekey']=json_decode($this->config_station['typekey'],true);
        $this->config_station['type']=json_decode($this->config_station['type'],true);
        $this->config_station['typer']=json_decode($this->config_station['typer'],true);
        $this->config_station['typep']=json_decode($this->config_station['typep'],true);
        $this->config_station['state']=json_decode($this->config_station['state'],true);
        $this->config_station['stimeconfigs']=json_decode($this->config_station['stimeconfigs'],true);
        view()->share('config_station',$this->config_station);

        view()->share('config_appointpre',$this->config_appoint);
        $this->config_appoint['statekey']=json_decode($this->config_appoint['statekey'],true);
        $this->config_appoint['state']=json_decode($this->config_appoint['state'],true);
        $this->config_appoint['processtype']=json_decode($this->config_appoint['processtype'],true);
        $this->config_appoint['dis']=json_decode($this->config_appoint['dis'],true);
        view()->share('config_appoint',$this->config_appoint);

        view()->share('config_operationpre',$this->config_operation);
        $this->config_operation['type']=json_decode($this->config_operation['type'],true);
        $this->config_operation['status']=json_decode($this->config_operation['status'],true);
        view()->share('config_operation',$this->config_operation);

        view()->share('statement',Func::getStatement());
        view()->share('ip',Func::getIp());
        
    }
    public function insertOperation($request,$uid,$type,$result=null){
        $this->getResult();
        $operation = new Operation();
        $operation->uid=$uid;
        $operation->otype=$type;
        if($result===null){
            $result=$this->result->toJson();
        }
        $operation->oip=Func::getIp();
        if($request!==null){
            $operation->orequest=json_encode($request->all(),JSON_UNESCAPED_UNICODE);
        }else{
            $operation->orequest=json_encode([],JSON_UNESCAPED_UNICODE);
        }
        $operation->oresult=$result;
        $agent = new Agent();
        $operation->oinfo=json_encode([
            'browser' => $agent->browser(),
            'browserv' => $agent->version($agent->browser()),
            'platform' => $agent->platform(),
            'platformv' => $agent->version($agent->platform()),
            'device' => $agent->device(),
            'isDesktop' => $agent->isDesktop(),
            'isPhone' => $agent->isPhone(),
            'isTablet' => $agent->isTablet(),
        ],JSON_UNESCAPED_UNICODE);
        $operation->save();
    }
    public function checkOperation($uid){
        $operation=Operation::where('uid',$uid)->orderByDesc("oid")->first();
        if($operation===null||time()-strtotime($operation->otime)>$this->config_basic['opttl']){
            return true;
        }
        $this->infoMsg="两次操作间隔时间直接不能少于".$this->config_basic['opttl']."s！";
        $this->getResult();
        return false;
    }
    public function checkAprocess($uid){
        $aprocess=Aprocess::where('uid',$uid)->orderByDesc("apid")->first();
        if($aprocess===null||time()-strtotime($aprocess->aptime)>$this->config_basic['apttl']){
            return true;
        }
        $this->infoMsg="两次预约处理操作间隔时间直接不能少于".$this->config_basic['apttl']."s！";
        $this->getResult();
        return false;
    }
    /**
     * 获取当前控制器名
     *
     * @return string
     */
    public function getCurrentControllerName()
    {
        return $this->getCurrentAction()['controller'];
    }

    /**
     * 获取当前方法名
     *
     * @return string
     */
    public function getCurrentMethodName()
    {
        return $this->getCurrentAction()['method'];
    }

    /**
     * 获取当前控制器与方法
     *
     * @return array
     */
    public function getCurrentAction()
    {
        $action = \Route::current()->getActionName();
        list($class, $method) = explode('@', $action);

        return ['controller' => $class, 'method' => $method];
    }
}
