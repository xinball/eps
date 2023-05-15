<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Library\CropAvatar;
use App\Library\Func;
use App\Library\MessageBox;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;



class UserController extends Controller
{
    //
    public function activeview(){
        return view('user.active')->with('forget',false);
    }
    public function forgetview(Request $request){
        return view('user.forget')->with('forget',true);
    }
    public function indexview(Request $request,$uid){
        return view('user.index')->with('result',$this->get($uid));
    }
    public function settingview(Request $request){
        if (!$this->luser){//为了验证用户是否登陆的，没有登陆就传一个authUserView函数返回的未登录页面
            return $this->defaultUserView($request);
        }
        return view('user.setting')->with('seactive',true)->with('result',$this->get($this->luser->uid));
    }
    public function appointview(Request $request){
        if (!$this->luser){
            return $this->defaultUserView($request);
        }
        return view('user.appoint')->with('aactive',true);
    }
    public function operationview(Request $request){
        if (!$this->luser){
            return $this->defaultUserView($request);
        }
        return view('user.operation')->with('oactive',true);
    }
    public function reportview(Request $request){
        if (!$this->luser){
            return $this->defaultUserView($request);
        }
        return view('user.report')->with('ractive',true);
    }
    public function register(Request $request){
        $user=null;
        $uname = $request->post('uname', null);
        $uemail = mb_strtolower($request->post('uemail', null));
        $uidno = mb_strtolower($request->post('uidno', null));
        $uidtype = $request->post('uidtype', null);
        $upwd = $request->post('upwd', null);
        $upwd1 = $request->post('upwd1', null);
        //得到用户的IP地址
        $ip=Func::getIp();
        //检测是否符合规范
        // if($this->check($uname,$uemail,$uidno,$uidtype,$upwd,$upwd1)){

        // }
        if(intval($this->config_basic['status'])!==1||$this->config_basic['register']==='0'){
            $this->errMsg="网站处于维护状态，无法注册！";
        }elseif(!Func::isUname($uname,2,30)){
            $this->errMsg="姓名格式不合规范【2~30位汉字】！";
        }elseif(!Func::isUidtype($uidtype)){
            $this->errMsg="身份证明类型格式不合规范！";
        }elseif(!Func::isUidno($uidno,$uidtype)){
            $this->errMsg="身份证明格式不合规范！";
        }elseif(!Func::isUidnoUname($uidno,$uname)){
            $this->errMsg="身份证明与姓名不匹配！";
        }elseif(!Func::isEmail($uemail)||Func::Length($uemail)>50){
            $this->errMsg="邮箱格式不合规范！";
        }elseif(!Func::isPwd($upwd,8,20)){
            $this->errMsg="密码格式不合规范！【8~20位英文/数字/符号】";
        }elseif($upwd !== $upwd1) {
            $this->errMsg = '两次密码不相同！';
        }elseif(User::where('uidno',$uidno)->where('uidtype',$uidtype)->exists()){
            $this->errMsg="身份证明 ".$uidno." 已经存在！请确认输入是否正确或联系管理员！";
        }elseif(User::where('uemail',$uemail)->exists()){
            $this->errMsg="该邮箱已注册！";
        }elseif(User::where('uinfo->reg_ip',$ip)->count()>=$this->config_basic['iplimit']){
            $this->errMsg="您所在IP已达到用户注册上限！";
        }else{
            //符合规范，开始注册
            $user=new User();
            $auth=rand(1000,9999);
            $uinfo=[
                'addr'=>'',
                'lang'=>'cn',
                'private'=>'0',
                'safe'=>'1',
                'safemail'=>'1',
                'tel'=>'',
                'slogan'=>'',
                'sex'=>'2',
                'homepage'=>'',
                'homepagessl'=>'0',
                'qq'=>'',
                'wid'=>'',
                'reg_ip'=>Func::getIp()
            ];
            $password=[
                'auth'=>$auth,
                'pwd'=>md5($auth . $upwd)
            ];
            $user->uidno=$uidno;
            $user->uidtype=$uidtype;
            $user->uname=$uname;
            $user->utype='a';
            $user->uemail=$uemail;
            $user->upwd=json_encode($password,JSON_UNESCAPED_UNICODE);
            $user->uinfo=json_encode($uinfo,JSON_UNESCAPED_UNICODE);
            $user->allowip=json_encode([],JSON_UNESCAPED_UNICODE);
            if($user->save()){
                $saveduser=User::where('uname',$uname)->first();
                $activettl=$this->config_user['activettl'];
                $this->successMsg="恭喜您注册成功，请进入您的<strong>邮箱 ".$user->uemail." 激活账号</strong>吧！<br/>注意！链接将于".$activettl."日后过期，请及时激活！";
                $code=rand(100000,999999);
                $activeExpire=3600*24*$activettl;
                Redis::setex("active_".$saveduser->uid,$activeExpire,$code);
                Func::sendUserMail($user,[
                    'subject'=>$this->config_basic['name']."-用户激活",
                    'text'=>"激活",
                    'link'=>config('var.ua')."?uid=$user->uid&code=$code",
                    'expire'=>date("Y-m-d H:i:s",$activeExpire+time())
                ]);
            }else{
                $this->errMsg='注册用户失败！';
            }
        }
        if($user!==null){
            $this->insertOperation($request,$user->uid,"ur");
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function login(Request $request) {
        //得到用户的ip地址
        $ip=Func::getIp();
        if(in_array($ip,Redis::Lrange('ban',0,-1))){ //错误次数太多ip会封禁
            $this->errMsg="您所在IP已被封禁！";
            $this->getResult();
            return $this->result->toJson();
        }
        $uidno = mb_strtolower($request->post('uidno', null));
        $uname = $request->post('uname', null);
        $upwd = $request->post('upwd', null);
        $remember = $request->post('remember', null);//是否记住密码
        //目前这几个变量的值是登录时输入的
        
        $user = $this->getUserBy($uidno);//查询用户是否存在
        if($user === null) {
            $this->errMsg = '该用户不存在！';
        }else{
            //剩余错误次数
            $left=Redis::exists("left_".$user->uid)?Redis::get("left_".$user->uid):6;
            if(5+$left<=0) {
                $this->errMsg="您所在IP已被封禁！";//错误次数太多了
                Redis::LPUSH('ban',$ip);
            }elseif($left<=1) {
                $this->errMsg="此用户已被锁定，继续尝试".(5+$left)."次后将封禁所在IP，请更改密码或等待解锁后重新登录！";
                Redis::setex("left_".$user->uid,3600,$left-1);
            }else {
                if($user->uname!==$uname) {
                    $this->errMsg="身份证明与姓名不匹配！";
                }elseif($user->utype==='a') {
                    $this->errMsg="用户尚未激活，请激活后登录！";
                }elseif($user->utype==='d') {
                    $this->errMsg="用户已被删除！";
                }else{
                    $password=json_decode($user->upwd);
                    if(md5($password->auth . $upwd) !== $password->pwd) {
                        Redis::setex("left_".$user->uid,3600,$left-1);
                        $this->errMsg="密码错误，您还有".($left-1)."次机会！";  //错误次数
                    }else{
                        if($this->luser&&$user->uid===$this->luser->uid){
                            $this->infoMsg="该用户已登录，无需再次登录！";
                        }else{
                            if(!$this->checkOperation($user->uid)){
                                return $this->result->toJson();
                            }
                            $this->getUser($user);
                            if(isset($user->uinfo->safe)&&$user->uinfo->safe==='1'&&!$this->checkip($user,$ip)){
                                $ipttl=$this->config_user['ipttl'];
                                $this->warnMsg="您所在IP尚未登录过本站，请进入您的<strong>邮箱 ".$user->uemail." </strong>绑定本机IP进行登录<br/>注意！链接将于".$ipttl."日后过期，请及时激活！";
                                $code=rand(100000,999999).$ip;
                                $ipExpire=3600*24*$ipttl;
                                Redis::setex("ip_".$user->uid,$ipExpire,$code);
                                Func::sendUserMail($user,[
                                    'subject'=>$this->config_basic['name']."-用户IP验证",
                                    'text'=>"IP验证",
                                    'link'=>config('var.ui')."?uid=$user->uid&code=$code",
                                    'expire'=>date("Y-m-d H:i:s",$ipExpire+time())
                                ]);
                            }else{
                                $token=md5($user->uid.rand());
                                if($remember!==null)
                                    $expire=time()+3600*24*30;
                                else{
                                    $expire=time()+$this->config_user['userloginttl'];
                                }
                                $key="token_".$user->uid;
                                setcookie("uid",$user->uid,$expire,"/");
                                setcookie($key,$token,$expire,"/");
                                Redis::setex($key,$expire-time(),$token);
                                Redis::del("left_".$user->uid);
                                $this->successMsg="欢迎回到 ".$this->config_basic['name']."！";
                                $this->url=url()->previous();
                            }
                        }
                        $this->insertOperation($request,$user->uid,"ul");
                    }
                }
            }
        }
        //登陆成功返回原页面
        $this->getResult();
        return $this->result->toJson();
    }
    //激活
    public function active(Request $request){
        $code = $request->get('code', null);
        $uid = $request->get('uid', null);
        $uidno = mb_strtolower($request->post('uidno', ""));
        $uname= $request->get('uname',null);
        $user=null;
        //把输入的数据传进来
        $flag=true;
        //身份证号不为空
        if ($uidno!==""){
            view()->share('uidno', $uidno);
            view()->share('uname', $uname);
            //通过身份证号找到这个用户
            $user = $this->getUserBy($uidno);
            if($user){
                if(!$this->checkOperation($user->uid)){
                    return $this->result->toJson();
                }
                if($user->uname !== $uname) {
                    $this->errMsg = "身份证明/邮箱 和 姓名不匹配！";
                }elseif($user->utype==='d') {
                    $this->warnMsg="该用户已被删除！";
                }elseif($user->utype!=='a') {
                    $this->infoMsg=$user->uname . "，您已成为正式用户，无需激活！";
                }else{
                    $this->successMsg="请进入您的邮箱 <strong>".$user->uemail."</strong> 激活账号吧！<br/>注意！链接将于".$this->config_user['activettl']."日后过期，请及时激活！";
                    //生成一个随机6位数
                    $code=rand(100000,999999);
                    //到期时间
                    $activeExpire=3600*24*$this->config_user['activettl'];
                    //发送邮件
                    Func::sendUserMail($user,[
                        'subject'=>$this->config_basic['name']."-用户激活",
                        'text'=>"激活",
                        'link'=>config('var.ua')."?uid=$user->uid&code=$code",
                        'expire'=>date("Y-m-d H:i:s",$activeExpire+time())
                    ]);
                    Redis::setex("active_".$user->uid,$activeExpire,$code);
                }
            }else{
                $this->errMsg="该用户不存在！";
            }
            $this->getResult();
            return view('user.active')->with('result',$this->result->toJson());
            //code是和服务器做验证用的，用户不需要知道
            //只有收到邮件的用户才可以得到这个包含code的连接，其他连接都被认为非法的
            //激活邮件点进去的操作
        }elseif(Func::isUid($uid)){
            $user = User::where('uid',$uid)->first();
            if($user){
                if(!$this->checkOperation($user->uid)){
                    return $this->result->toJson();
                }
                if($user->utype==='d') {
                    $this->errMsg="该用户已被删除！";
                }elseif($user->utype!=='a') {
                    $this->infoMsg=$user->uname . "，您已成为正式用户，无需激活！";
                }elseif(Func::isNum($code,6,6)){
                    if(!Redis::exists("active_".$uid)){
                        $this->errMsg="抱歉！您的激活链接已过期<br/>请重新申请激活！";
                    }elseif($code===Redis::get("active_".$uid)){
                        if(User::where('uid',$uid)->update(['utype'=>'r'])){
                            Redis::del("active_".$uid);
                            $this->successMsg="<strong>恭喜您！</strong>您已成为".$this->config_basic['name']."正式用户！";
                        }else{
                            $this->errMsg="用户激活失败！";
                        }
                    }else{
                        $flag=false;
                    }
                }else{
                    $flag=false;
                }
            }else{
                $flag=false;
            }
        }else{
            $this->errMsg="您的数据传输有误！";
        }
        if(!$flag)
            $this->errMsg="抱歉！您的激活链接不正确<br/>请重新打开邮件内的链接进行激活！";

        if($this->successMsg){
            $this->url="/";//成功消息发出后，返回公告界面
            $this->insertOperation($request,$user->uid,"ua");
        }
        $this->getResult();
        return view('user.active')->with('result',$this->result->toJson());
    }
    //忘记密码
    public function forget(Request $request){
        $code= $request->get('code',null);
        $uid= $request->get('uid',null);
        $uidno = mb_strtolower($request->get('uidno', ""));
        $uname= $request->get('uname',null);
        $upwd= $request->get('upwd',null);
        $upwd1= $request->get('upwd1',null);
        $user=null;
        $flag=true;
        //身份证号不为空
        if ($uidno!==""){
            view()->share('title', '找回密码');
            view()->share('uidno', $uidno);
            view()->share('uname', $uname);
            //根据身份证号找到用户
            $user = $this->getUserBy($uidno);
            if($user) {
                if(!$this->checkOperation($user->uid)){
                    return $this->result->toJson();
                }
                if($user->uname !== $uname) {
                    $this->errMsg = "身份证明/邮箱 和 姓名不匹配！";
                }elseif($user->utype === 'b') {
                    $this->errMsg = $user->uname . "，您的账号已被封禁，无法激活！";
                }elseif($user->utype === 'd') {
                    $this->errMsg = "该用户已被删除！";
                }elseif($user->utype === 'a') {
                    $this->errMsg = "该用户尚未激活！";
                }else{
                    $this->successMsg = "请进入您的邮箱： <strong>" . $user->uemail . "</strong> 重置密码吧！<br/>注意！重置链接将于" . $this->config_user['forgetttl'] . "日后过期，请及时重置密码！";
                    $code = rand(100000, 999999);
                    $forgetExpire = time() + 3600 * 24 * $this->config_user['forgetttl'];
                    Redis::setex("forget_" . $user->uid, $forgetExpire - time(), $code);
                    Func::sendUserMail($user,[
                        'subject'=>$this->config_basic['name'] . "-找回密码",
                        'text'=>"重置密码",
                        'link'=>config('var.uf') . "?uid=$user->uid&code=$code",
                        'expire'=>date("Y-m-d H:i:s", $forgetExpire)
                    ]);
                }
            }else{
                $this->errMsg = "用户". $uname . "不存在，无法重置！";
            }
            $this->getResult();
            return view('user.forget')->with('result',$this->result->toJson());
        //重置邮件点进去的操作
        }elseif(Func::isUid($uid)){
            view()->share('title','密码重置');
            $user = User::where('uid',$uid)->first();
            $password=json_decode($user->upwd);
            if($user!==null){
                if(!$this->checkOperation($user->uid)){
                    return $this->result->toJson();
                }
                if(Func::isNum($code,6,6)){
                    if(!Redis::exists("forget_".$uid)){
                        $this->errMsg="抱歉！您的密码重置链接已过期<br/>请重新申请重置密码！";
                    }elseif($code===Redis::get("forget_".$uid)){
                        view()->share("fuser",$user);
                        view()->share("code",$code);
                        view()->share('upwd', $upwd);
                        view()->share('upwd1', $upwd1);
                        if($upwd){
                            if($upwd !== $upwd1){
                                $this->warnMsg="两次输入的密码不相同！";
                            }elseif(!Func::isPwd($upwd,8,20)){
                                $this->errMsg="新密码格式不合规范！";
                            }elseif(md5($password->auth . $upwd) === $password->pwd){
                                $this->infoMsg="重置密码与原密码相同！";
                            }else{
                                $auth=rand(1000,9999);
                                $newpassword=[
                                    'auth'=>$auth,
                                    'pwd'=>md5($auth . $upwd)
                                ];
                                if(User::where('uid',$uid)->update(['upwd'=>json_encode($newpassword,JSON_UNESCAPED_UNICODE)])){
                                    Redis::del("forget_".$uid);
                                    Redis::del("token_".$uid);
                                    Redis::del("atoken_".$uid);
                                    Redis::del("left_".$uid);
                                    $this->successMsg="<strong>恭喜！</strong>您的密码已重置，请重新登录！";
                                }else{
                                    $this->errMsg="重置密码失败！";
                                }
                            }
                        }
                    }else{
                        $flag=false;
                    }
                }else{
                    $flag=false;
                }
            }else{
                $flag=false;
            }
        }else{
            $this->errMsg="您的数据传输有误！";
        }
        if(!$flag)
            $this->errMsg="抱歉！您的密码重置链接不正确<br/>请重新打开邮件内的链接！";
        if($this->successMsg){
            $this->url="/";
            $this->insertOperation($request,$user->uid,"uf");
        }
        $this->getResult();
        return view('user.forget')->with('result',$this->result->toJson());
    }
    public function ipverify(Request $request){
        $code = $request->get('code', null);
        $uid = $request->get('uid', null);
        $user = User::where('uid',$uid)->first();
        $flag = false;
        $this->url=null;
        if($user){
            if(!$this->checkOperation($user->uid)){
                return view('notice.list')->with('result',$this->result->toJson());
            }
            if($user->utype==='d') {
                $this->errMsg="该用户已被删除！";
            }elseif($user->utype==='a') {
                $this->warnMsg=$user->uname . "，您尚未激活，不能绑定IP！";
            }else{
                $tmpuser=clone $user;
                $this->getUser($user);
                if(!Redis::exists("ip_".$uid)||$code!==Redis::get("ip_".$uid)){
                    $flag=false;
                }elseif(isset($user->uinfo->safemail)&&$user->uinfo->safemail==='1'&&substr($code,6)!==Func::getIp()){
                    $flag=false;
                }else{
                    $flag=true;
                    $allowip=$user->allowip;
                    if($allowip===null)
                        $allowip = [];
                    array_push($allowip,substr($code,6));
                    $allowip=array_unique($allowip);
                    $tmpuser->allowip=json_encode($allowip,JSON_UNESCAPED_UNICODE);
                    $tmpuser->update();
                    if(substr($code,6)!==Func::getIp()){
                        $this->successMsg="IP已验证，请使用原设备所在IP登录！ ";
                    }else{
                        $this->url="/";
                        $flag=true;
                        Redis::del("ip_".$user->uid);
                        $token=md5($user->uid.rand());
                        $expire=time()+$this->config_user['userloginttl'];
                        $key="token_".$user->uid;
                        setcookie("uid",$user->uid,$expire,"/");
                        setcookie($key,$token,$expire,"/");
                        Redis::setex($key,$expire-time(),$token);
                        $this->successMsg="您的IP已验证，欢迎回到 ".$this->config_basic['name']."！";
                    }
                    $this->insertOperation($request,$user->uid,"ui");
                }
            }
        }else{
            $flag=false;
        }
        if(!$flag)
            $this->errMsg="抱歉！您的绑定IP链接有误或IP不匹配<br/>请重新申请！";
        $this->getResult();
        return view('notice.list')->with('result',$this->result->toJson());
    }
    //退出
    public function logout(Request $request){
        $uid=null;
        if($this->luser){//检测用户是否正在登陆中
            if(!$this->checkOperation($this->luser->uid)){
                return $this->result->toJson();
            }
            setcookie("uid","",0,"/");
            setcookie($this->key,"",0,"/");
            Redis::del($this->key);
            $uid=$this->luser->uid;
            $this->luser=null;
            view()->share('luser',null);
            $this->successMsg="用户退出登录成功！";
        }else{
            $this->warnMsg="退出登录失败：没有用户登录中！";
        }
        $this->url=url()->previous();//返回到先前的页面
        if($this->successMsg){
            $this->insertOperation($request,$uid,"ulo");
        }
        $this->getResult();
        return $this->result->toJson();
    }
    //修改个人信息
    public function alter(Request $request) {
        if (!$this->luser){//检测是否登陆
            $this->errMsg="您没有权限修改信息，请重新登录！";
        }else{
            if(!$this->checkOperation($this->luser->uid)){
                return $this->result->toJson();
            }
            $upwd = $request->post('upwd', null);              //输入的密码信息，输入正确的密码才能进行修改操作
            $password = json_decode($this->luser->upwd);       //真正的密码
            if($upwd===null){
                $this->errMsg="请填写密码以修改信息！";
            }elseif(md5($password->auth.$upwd)!==$password->pwd){         //密码对不上
                $this->errMsg="修改信息失败，密码错误！";
            }else{
                $upwd1 = $request->post('upwd1', null);
                $upwd2 = $request->post('upwd2', null);
                $lang = $request->post('lang', 'cn');             //语言
                $private = $request->post('private', '1');
                $safe = $request->post('safe', '1');
                $safemail = $request->post('safemail', '1');
                $sex = $request->post('sex', '2');
                $tel = $request->post('tel', '');
                $slogan = $request->post('slogan', '');
                $homepage = $request->post('homepage', '');
                $homepagessl = $request->post('homepagessl', '1');
                $con_id = $request->post('con_id', 0);
                $coun_id = $request->post('coun_id', 0);
                $state_id = $request->post('state_id', 0);
                $city_id = $request->post('city_id', 0);
                $region_id = $request->post('region_id', 0);
                $addr = $request->post('addr', '');
                $qq = $request->post('qq', '');
                $wid = $request->post('wid', '');
                $allowip = $request->post('allowip', '[]');
                if($lang!=="cn"&&$lang!=="en"){                   //检错
                    $this->errMsg="语言选择有误！";
                }elseif($private!=="0"&&$private!=="1"){
                    $this->errMsg="安全信息选择有误！";
                }elseif($sex!=='0'&&$sex!=='1'&&$sex!=='2'){
                    $this->errMsg="性别选择错误！";
                }elseif($homepagessl!=='0'&&$homepagessl!=='1'){
                    $this->errMsg="个人主页协议选择错误！";
                }elseif($safe!=='0'&&$safe!=='1'){
                    $this->errMsg="异地登录保护选择错误！";
                }elseif($safemail!=='0'&&$safemail!=='1'){
                    $this->errMsg="邮箱验证保护选择错误！";
                }elseif(mb_strlen($tel)>0&&!Func::isTel($tel)){
                    $this->errMsg="联系方式格式错误！";
                }elseif(mb_strlen($slogan)>100){
                    $this->errMsg="个性签名格式错误【长度不得大于100】！";
                }elseif(mb_strlen($homepage)>50){
                    $this->errMsg="个人主页格式错误【长度不得大于50】！";
                }elseif(mb_strlen($qq)>13){
                    $this->errMsg="QQ格式错误！";
                }elseif($allowip!=="[]"&&!json_decode($allowip,true)){
                    $this->errMsg="允许登录的IP格式不合规范！";
                }elseif(!$this->checkaddr($con_id,$coun_id,$state_id,$city_id,$region_id)){
                    $this->errMsg="所在地格式不合规范！";
                }else{
                    if($upwd1!==null){
                        if(!Func::isPwd($upwd1,8,20)){
                            $this->errMsg="新密码格式不合规范！【8~20位英文/数字/符号】";
                        }elseif($upwd1 !== $upwd2) {
                            $this->errMsg = '两次新密码不相同！';
                        }elseif($upwd === $upwd1) {
                            $this->errMsg = '修改失败：新密码与旧密码相同！';
                        }
                    }
                    //没有出现错误
                    if(!$this->errMsg){
                        //新密码保存
                        $password->auth=rand(1000,9999);
                        $password->pwd=md5($password->auth.$upwd1);
                        if($upwd1!==null)
                            $this->luser->upwd=json_encode($password,JSON_UNESCAPED_UNICODE);

                        //修改头像和横幅
                        unset($this->luser->avatar);
                        unset($this->luser->banner);
                        if($homepage===''){
                            $homepage=config("app.host").'/user/'.$this->luser->uid;
                            $homepagessl=config("app.http");
                        }
                        //更新新修改的数据
                        $allowip=json_decode($allowip);
                        $allowip=array_filter(array_unique($allowip));
                        $this->luser->allowip=json_encode($allowip,JSON_UNESCAPED_UNICODE);
                        $this->luser->con_id=$con_id;
                        $this->luser->coun_id=$coun_id;
                        $this->luser->state_id=$state_id;
                        $this->luser->city_id=$city_id;
                        $this->luser->region_id=$region_id;
                        $this->luser->uinfo=json_encode([
                            'reg_ip'=>$this->luser->uinfo->reg_ip??Func::getIp(),
                            'addr'=>$addr,
                            'lang'=>$lang,
                            'private'=>$private,
                            'safe'=>$safe,
                            'safemail'=>$safemail,
                            'private'=>$private,
                            'sex'=>$sex,
                            'tel'=>$tel,
                            'slogan'=>$slogan,
                            'homepage'=>$homepage,
                            'homepagessl'=>$homepagessl,
                            'qq'=>$qq,
                            'wid'=>$wid
                        ],JSON_UNESCAPED_UNICODE);
                        if($this->luser->update()>0){
                            $this->successMsg="修改个人信息成功！";
                        }else{
                            $this->errMsg="修改个人信息失败！";
                        }
                        //User::where('uid',$this->luser->uid)->update()
                    }
                }
            }
        }
        if($this->successMsg){
            $this->insertOperation($request,$this->luser->uid,"ual");
        }
        $this->getResult();
        return $this->result->toJson();
    }
    //修改个性签名
    public function alterslogan(Request $request) {
        if (!$this->luser){                    //看用户是否登录
            $this->errMsg="您没有权限修改信息，请重新登录！";
        }else{
            if(!$this->checkOperation($this->luser->uid)){
                return $this->result->toJson();
            }
            $slogan = $request->post('slogan', null);
            $prePage= $request->post('prePage',null);
            //存放用户输入的个签
            if(Func::Length($slogan)>100){
                $this->errMsg="个性签名格式错误【长度不得大于100】！";
            }else {
                if (!$this->errMsg) {
                    unset($this->luser->avatar);
                    unset($this->luser->banner);
                    $arr=$this->luser->uinfo;
                    $arr['slogan']=$slogan;
                    $this->luser->uinfo = json_encode($arr, JSON_UNESCAPED_UNICODE);
                    if ($this->luser->update() > 0) {
                        $this->successMsg = "修改个性签名成功！";
                    } else {
                        $this->errMsg = "修改个性签名失败！";
                    }
                }
            }
        }
        if($this->successMsg){
            $this->insertOperation($request,$this->luser->uid,"uals");
        }
        $this->getResult();
        return $this->result->toJson();
    }
    //上传头像
    public function uploadavatar(Request $request){
        if (!$this->luser){
            $response = array(
                'state'  => 200,
                'status' => 4,
                'imgurl' => $this->config_basic['defaultavatar'],
                'message' => "您没有权限修改头像，请重新登录！"
            );
        }else{
            if(!$this->checkOperation($this->luser->uid)){
                return $this->result->toJson();
            }
            $dstwidth=$this->config_basic["avatarwidth"];
            $crop = new CropAvatar($request->post('avatar_src'), $request->post('avatar_data'), $_FILES['avatar_file'], $this->config_basic["useravatar"].$this->luser->uid,$dstwidth,$dstwidth);
            $response = array(
                'state'  => 200,
                'status' => $crop -> getResult()!==null?1:4,
                'imgurl' => $crop -> getResult()."?".filectime(public_path($crop -> getResult())),
                'message' => ($crop -> getMsg()!==null?$crop -> getMsg():"上传头像成功！")
            );
            $this->insertOperation($request,$this->luser->uid,"uua",json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        echo json_encode($response);
    }
    //上传横幅
    public function uploadbanner(Request $request){
        if (!$this->luser){                          //用户没登录
            $response = array(
                'state'  => 200,
                'status' => 4,
                'imgurl' => $this->config_basic['defaultabanner'],
                'message' => "您没有权限修改横幅，请重新登录！"
            );
        }else{
            if(!$this->checkOperation($this->luser->uid)){
                return $this->result->toJson();
            }
            $dstwidth=$this->config_basic["bannerwidth"];
            $crop = new CropAvatar($request->post('avatar_src'), $request->post('avatar_data'), $_FILES['avatar_file'], $this->config_basic["userbanner"].$this->luser->uid,$dstwidth,$dstwidth/2);
            $response = array(
                'state'  => 200,
                'status' => $crop -> getResult()!==null?1:4,
                'imgurl' => $crop -> getResult()."?".filectime(public_path($crop -> getResult())),
                'message' => ($crop -> getMsg()!==null?$crop -> getMsg():"上传横幅成功！")
            );
            $this->insertOperation($request,$this->luser->uid,"uub",json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        echo json_encode($response);
    }

    //查看用户信息
    public function get($uid){
        $this->url="/";
        $user=$this->getUserBy($uid);
        if($user){
            if($user->utype==='d'){
                $this->errMsg="该用户已被删除，无法查看用户信息！";
            }elseif($user->utype==='a'){
                $this->errMsg="该用户未激活，无法查看用户信息！";
            }elseif($user->utype==='b'){
                $this->errMsg="该用户被封禁，无法查看用户信息！";
            }else{
                $this->successMsg="获取用户 ".$user->uname." 的信息成功";
                $this->url=null;
                $this->getUser($user);
                $this->setUserPrivate($user);
                unset($user->upwd);
                $this->result->data = [
                    'user' => $user
                ];
            }
        }else{
            $this->errMsg="该用户不存在！";
        }

        $this->getResult();
        return $this->result->toJson();
    }

    //管理员查看用户详细信息
    public function aget($uid){
        if(!$this->ladmin){
            $this->errMsg="您不是管理员，没有权限查看用户详细信息！";
        }else{
            $user=$this->getUserBy($uid);
            if($user){
                if($user->utype==='d'){
                    $this->errMsg="该用户已被删除，无法查看用户信息！";
                }else{
                    $this->successMsg="获取用户 ".$user->uname." 的信息成功";
                    $this->url=null;
                    $this->getUser($user);//getUser就是得到头像和横幅还有信息
                    unset($user->upwd);
                    $this->result->data = [
                        'user' => $user
                    ];
                }
            }else{
                $this->errMsg="该用户不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

    //管理员查看用户列表
    public function agetlist(Request $request){
        if($this->ladmin===null){
            $this->errMsg="您不是管理员，没有权限查看用户列表！";
        }else{
            $params=$request->all();
            //$orwhere=array();
            $sql=User::select("uid","uidno","utype","uname","uemail","uinfo","utime")->distinct();
            $where=[];
            foreach ($params as $key=>$v){
                if($v!==""&&$v!==null){
                    if($key==="uname"||$key==="uemail"||$key==="uid"||$key==="uidno"){
                        $where[]=[$key,'like','%'.$v.'%'];
                    }elseif($key==="type"&&in_array($v,$this->config_user['typekey']['all'])){
                        $where[]=['utype','=',$v];
                    }elseif($key==="uidtype"&&isset($this->config_user['idnotype']{$v})){
                        $where[]=['uidtype','=',$v];
                    }elseif($key==="tel"||$key==="qq"||$key==="wid"){
                        $where[]=['uinfo->'.$key,'like','%'.$v.'%'];
                    }elseif($key==="sex"){
                        $where[]=['uinfo->'.$key,'=',$v];
                    }elseif($key==="ustart"&&strtotime($v)){
                        $where[]=['utime','>=',$v];
                    }elseif($key==="uend"&&strtotime($v)){
                        $where[]=['utime','<=',$v];
                    }
                }
            }
            $sql=$sql->where($where);
    

            //根据侧边栏的选择看如何排列
            $orderPara = $params['order']??"";
            $desc = $params['desc']??"0";
            if($orderPara==="uname"){
                if($desc==='1'){
                    $sql=$sql->orderByRaw("CONVERT(uname USING gbk) desc")->orderBy('uid');
                }else{
                    $sql=$sql->orderByRaw("CONVERT(uname USING gbk)")->orderBy('uid');
                }
            }elseif($orderPara==="uemail"||$orderPara==="uidno"){
                if($desc==='1'){
                    $sql=$sql->orderByDesc($orderPara)->orderBy('uid');
                }else{
                    $sql=$sql->orderBy($orderPara)->orderBy('uid');
                }
            }else{
                if($desc==='1'){
                    $sql=$sql->orderByDesc('uid');
                }else{
                    $sql=$sql->orderBy('uid');
                }
            }
            //echo $sql->toSql();
    
            $users=$sql->paginate($this->config_user['listnum'])->withQueryString();
            //getUser就是得到头像和横幅还有信息
            foreach($users as $user){
                $this->getUser($user);
            }
            $this->listMsg($users);
            $this->result->data=[
                'users'=>$users,
                'num'=>$this->getTypeNum(),
            ];
        }

        $this->getResult();
        return $this->result->toJson();
    }
    //每种类用户有多少个
    public function getTypeNum(){
        $sql = User::select('utype',DB::raw("count('uid') as num"))->groupBy('utype');
        $nums = $sql->get();
        $typenum=[
            'sum'=>0,
        ];
        foreach($nums as $num){
            $typenum[$num->utype]=$num->num;
        }
        foreach($this->config_user['typekey']['all'] as $type){
            if(!isset($typenum[$type])){
                $typenum[$type]=0;
            }
            $typenum['sum']+=$typenum[$type];
        }
        return $typenum;
    }

}

