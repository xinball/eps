<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Tag;
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
        if ($auth=$this->authUserView($request)){     //为了验证用户是否登陆的，没有登陆就传一个authUserView函数返回的未登录页面
            return $auth;
        }
        return view('user.setting')->with('seactive',true)->with('result',$this->get($this->luser->uid));
    }
    public function appointview(Request $request){
        if ($auth=$this->authUserView($request)){
            return $auth;
        }
        return view('user.appoint')->with('aactive',true);
    }
    public function reportview(Request $request){
        if ($auth=$this->authUserView($request)){
            return $auth;
        }
        return view('user.report')->with('rcactive',true);
    }
    public function tagview(Request $request){
        if ($auth=$this->authUserView($request)){
            return $auth;
        }
        $tags=Tag::all();
        $tids=Tag::getTidsByUid($this->luser->uid);
        foreach($tags as $tag){
            if(in_array($tag->tid,$tids)){
                $tag->like=true;
            }else{
                $tag->like=false;
            }
        }
        $this->result->tags=$tags;
        return view('user.tag')->with('tactive',true)->with('result',$this->result->toJson());
    }

    
    public function register(Request $request)
    {
        $uname = $request->post('uname', null);
        $uemail = mb_strtolower($request->post('uemail', null));
        $uidno = mb_strtolower($request->post('uidno', null));
        $uidtype = $request->post('uidtype', null);
        $upwd = $request->post('upwd', null);
        $upwd1 = $request->post('upwd1', null);
        $ip=Func::getIp();
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
            $user=new User();
            $auth=rand(1000,9999);
            $uinfo=[
                'lang'=>'cn',
                'private'=>'0',
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
            if($user->save()){
                $saveduser=User::where('uname',$uname)->first();
                $activettl=$this->config_user['activettl'];
                $this->successMsg="恭喜您注册成功，请进入您的<strong>邮箱激活账号</strong>吧！<br/>注意！链接将于".$activettl."日后过期，请及时激活！";
                $code=rand(100000,999999);
                $activeExpire=3600*24*$activettl;
                Redis::setex("active_".$saveduser->uid,$activeExpire,$code);
                Func::sendMail($user->uemail,$this->config_basic['name']."-用户激活",
                    "激活链接：<a href='".config('var.ua')."?uid=$user->uid&code=$code'>激活</a>"
                    ."<br/>过期时间：".date("Y-m-d H:i:s",$activeExpire+time()));

                // $email = new Email;
                // $email->to = $uemail;
                // $email->cc = 'zhouqing@xinball.top';
                // $email->subject = 'XBOJ邮箱验证';
                // $email->content = '请于24小时点击该链接完成验证. http://book.magina.com/service/validate_email'
                //     . '?member_id=' . $member->id
                //     . '&code=' . $uuid;

                // $tempEmail = new TempEmail;
                // $tempEmail->member_id = $member->id;
                // $tempEmail->code = $uuid;
                // $tempEmail->deadline = date('Y-m-d H-i-s', time() + 24*60*60);
                // $tempEmail->save();

                // Mail::send('email_register', ['m3_email' => $m3_email], function ($m) use ($m3_email) {
                //     // $m->from('hello@app.com', 'Your Application');
                //     $m->to($m3_email->to, '尊敬的用户')
                //         ->cc($m3_email->cc)
                //         ->subject($m3_email->subject);
                // });

            }else{
                $this->errMsg='注册用户失败！';
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

    public function login(Request $request) {
        $ip=Func::getIp();
        if(in_array($ip,Redis::Lrange('ban',0,-1))){ //错误次数太多会封禁
            $this->errMsg="您所在IP已被封禁！";
            $this->getResult();
            return $this->result->toJson();
        }
        $uidno = mb_strtolower($request->post('uidno', null));
        $uname = $request->post('uname', null);
        $upwd = $request->post('upwd', null);
        $remember = $request->post('remember', null);//是否记住密码
        
        $user = $this->getUser($uidno);//查询用户是否存在
        if($user === null) {
            $this->errMsg = '该用户不存在！';
        }else{
            $left=Redis::exists("left_".$user->uid)?Redis::get("left_".$user->uid):6;
            if(5+$left<=0) {
                $this->errMsg="您所在IP已被封禁！";//错误次数太多了
                Redis::LPUSH('ban',$ip);
            }elseif($left<=1) {
                $this->errMsg="此用户已被锁定，输入错误".(5+$left)."次后将封禁所在IP，请更改密码或等待解锁后重新登录！";
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
                    }
                }
            }
        }
        if($this->successMsg)
            $this->url=url()->previous();
        $this->getResult();
        return $this->result->toJson();
    }
    //激活
    public function active(Request $request){
        $code = $request->get('code', null);
        $uid = $request->get('uid', null);
        $uname = mb_strtolower($request->get('uname', null));
        $flag=true;
        if ($uname!==""){
            $user = $this->getUser($uname);
            if($user){
                view()->share('uname',$user->uidno);
                if($user->utype==='d') {
                    $this->warnMsg="该用户已被删除！";
                }elseif($user->utype!=='a') {
                    $this->infoMsg=$user->uname . "，您已成为正式用户，无需激活！";
                }else{
                    $this->successMsg="请进入您的邮箱 <strong>".$user->uemail."</strong> 激活账号吧！<br/>注意！链接将于".$this->config_user['activettl']."日后过期，请及时激活！";
                    $code=rand(100000,999999);
                    $activeExpire=3600*24*$this->config_user['activettl'];
                    Func::sendMail($user->uemail,$this->config_basic['name']."-用户激活",
                        "激活链接：<a href='".config('var.ua')."?uid=$user->uid&code=$code'>激活</a>"
                        ."<br/>过期时间：".date("Y-m-d H:i:s",$activeExpire+time()));
                    Redis::setex("active_".$user->uid,$activeExpire,$code);
                }
            }else{
                $this->errMsg="该用户不存在！";
            }
            $this->getResult();
            return view('user.active')->with('result',$this->result->toJson());
        }elseif(Func::isUid($uid)){
            $user = User::where('uid',$uid)->first();
            if($user){
                if($user->utype==='d') {
                    $this->warnMsg="该用户已被删除！";
                }elseif($user->utype!=='a') {
                    $this->infoMsg=$user->uname . "，您已成为正式用户，无需激活！";
                }elseif(Func::isNum($code,6,6)){
                    if(!Redis::exists("active_".$uid)){
                        $this->errMsg="抱歉！您的激活链接已过期<br/>请重新申请激活！";
                    }else if($code===Redis::get("active_".$uid)){
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

        if($this->successMsg)
            $this->url="/notice";
        $this->getResult();
        return view('user.active')->with('result',$this->result->toJson());
    }

    public function forget(Request $request){           //忘记密码
        $code= $request->get('code',null);
        $uid= $request->get('uid',null);
        $uname= $request->get('uname',null);
        $upwd= $request->get('upwd',null);
        $upwd1= $request->get('upwd1',null);
        $flag=true;
        if ($uname!==null){
            view()->share('title', '找回密码');
            view()->share('uname', $uname);
            $user = User::where('uname', $uname)->first();
            if($user) {
                if ($user->utype === 'b') {
                    $this->errMsg = $user->uname . "，您的账号已被封禁，无法激活！";
                } elseif ($user->utype === 'd') {
                    $this->warnMsg = "该用户已被删除！";
                } elseif ($user->utype === 'a') {
                    $this->warnMsg = "该用户尚未激活！";
                } else{
                    $this->successMsg = "请进入您的邮箱： <strong>" . $user->uemail . "</strong> 重置密码吧！<br/>注意！重置链接将于" . $this->config_user['forgetttl'] . "日后过期，请及时重置密码！";
                    $code = rand(100000, 999999);
                    $forgetExpire = time() + 3600 * 24 * $this->config_user['forgetttl'];
                    Redis::setex("forget_" . $user->uid, $forgetExpire - time(), $code);
                    Func::sendMail($user->uemail, $this->config_basic['name'] . "-找回密码",
                        "密码重置链接：<a href='" . config('var.uf') . "?uid=$user->uid&code=$code'>重置密码</a>"
                        . "<br/>过期时间：" . date("Y-m-d H:i:s", $forgetExpire));
                }
            }else{
                $this->errMsg = "用户". $uname . "不存在，无法激活！";
            }
            $this->getResult();
            return view('user.forget')->with('result',$this->result->toJson());
        }elseif($uid!==null){
            view()->share('title','密码重置');
            $user = User::where('uid',$uid)->first();
            $password=json_decode($user->upwd);
            if($user){
                if(Func::isNum($code,6,6)){
                    if(!Redis::exists("forget_".$uid)){
                        $this->errMsg="抱歉！您的密码重置链接已过期<br/>请重新申请重置密码！";
                    }else if($code===Redis::get("forget_".$uid)){
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
                                $this->infoMsg="重置密码与原秘密相同！";
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
        if($this->successMsg)
            $this->url="/notice";
        $this->getResult();
        return view('user.forget')->with('result',$this->result->toJson());
    }

    //退出
    public function logout(Request $request){
        if($this->luser){
            setcookie("uid","",0,"/");
            setcookie($this->key,"",0,"/");
            Redis::del($this->key);
            $this->luser=null;
            view()->share('luser',null);
            $this->successMsg="用户退出登录成功！";
        }else{
            $this->warnMsg="退出登录失败：没有用户登录中！";
        }
        $this->url=url()->previous();                //返回
        $this->getResult();
        return $this->result->toJson();
    }

    //修改个人信息
    public function alter(Request $request) {
        if (!$this->luser){
            $this->errMsg="您没有权限修改信息，请重新登录！";
        }else{
            $upwd = $request->post('upwd', null);
            $password = json_decode($this->luser->upwd);
            if($upwd===null){
                $this->errMsg="请填写密码以修改信息！";
            }elseif(md5($password->auth.$upwd)!==$password->pwd){
                $this->errMsg="修改信息失败，密码错误！";
            }else{
                $upwd1 = $request->post('upwd1', null);
                $upwd2 = $request->post('upwd2', null);
                $lang = $request->post('lang', null);
                $private = $request->post('private', null);
                $sex = $request->post('sex', null);
                $tel = $request->post('tel', null);
                $slogan = $request->post('slogan', null);
                $homepage = $request->post('homepage', null);
                $homepagessl = $request->post('homepagessl', null);
                $qq = $request->post('qq', null);
                $wid = $request->post('wid', null);
                if($lang!=="cn"&&$lang!=="en"){
                    $this->errMsg="语言选择有误！";
                }elseif($private!=="0"&&$private!=="1"){
                    $this->errMsg="安全信息选择有误！";
                }elseif($sex!=='0'&&$sex!=='1'&&$sex!=='2'){
                    $this->errMsg="性别选择错误！";
                }elseif($homepagessl!=='0'&&$homepagessl!=='1'){
                    $this->errMsg="个人主页协议选择错误！";
                }elseif(mb_strlen($tel)>0&&!Func::isTel($tel)){
                    $this->errMsg="联系方式格式错误！";
                }elseif(mb_strlen($slogan)>100){
                    $this->errMsg="个性签名格式错误【长度不得大于100】！";
                }elseif(mb_strlen($homepage)>50){
                    $this->errMsg="个人主页格式错误【长度不得大于50】！";
                }elseif(mb_strlen($qq)>13){
                    $this->errMsg="QQ格式错误！";
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
                    if(!$this->errMsg){
                        $password->auth=rand(1000,9999);
                        $password->pwd=md5($password->auth.$upwd1);
                        if($upwd1!==null)
                            $this->luser->upwd=json_encode($password,JSON_UNESCAPED_UNICODE);
                        unset($this->luser->avatar);
                        unset($this->luser->banner);
                        $this->luser->uinfo=json_encode([
                            'reg_ip'=>$this->luser->uinfo['reg_ip'],
                            'lang'=>$lang,
                            'private'=>$private,
                            'sex'=>$sex,
                            'tel'=>$tel,
                            'slogan'=>$slogan,
                            'homepage'=>$homepage,
                            'homepagesl'=>$homepagessl,
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
        $this->getResult();
        return $this->result->toJson();
    }


 
    //修改个性签名
    public function alterslogan(Request $request) {
        if (!$this->luser){
            $this->errMsg="您没有权限修改信息，请重新登录！";
        }else{
                $slogan = $request->post('slogan', null);
                $prePage= $request->post('prePage',null);
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
        $this->getResult();
        return $this->result->toJson();
    }

    //加载头像
    public function uploadavatar(Request $request){
        if (!$this->luser){
            $response = array(
                'state'  => 200,
                'status' => 4,
                'imgurl' => $this->config_basic['defaultavatar'],
                'message' => "您没有权限修改头像，请重新登录！"
            );
        }else{
            $dstwidth=$this->config_basic["avatarwidth"];
            $crop = new CropAvatar($request->post('avatar_src'), $request->post('avatar_data'), $_FILES['avatar_file'], $this->config_basic["useravatar"].$this->luser->uid,$dstwidth,$dstwidth);
            $response = array(
                'state'  => 200,
                'status' => $crop -> getResult()!==null?1:4,
                'imgurl' => $crop -> getResult()."?".filectime(public_path($crop -> getResult())),
                'message' => ($crop -> getMsg()!==null?$crop -> getMsg():"上传头像成功！")
            );
            //'result' => $crop -> getMsg()
        }
        echo json_encode($response);
    }

    //加载横幅
    public function uploadbanner(Request $request){
        if (!$this->luser){
            $response = array(
                'state'  => 200,
                'status' => 4,
                'imgurl' => $this->config_basic['defaultabanner'],
                'message' => "您没有权限修改横幅，请重新登录！"
            );
        }else{
            $dstwidth=$this->config_basic["bannerwidth"];
            $crop = new CropAvatar($request->post('avatar_src'), $request->post('avatar_data'), $_FILES['avatar_file'], $this->config_basic["userbanner"].$this->luser->uid,$dstwidth,$dstwidth/2);
            $response = array(
                'state'  => 200,
                'status' => $crop -> getResult()!==null?1:4,
                'imgurl' => $crop -> getResult()."?".filectime(public_path($crop -> getResult())),
                'message' => ($crop -> getMsg()!==null?$crop -> getMsg():"上传横幅成功！")
            );
            //'result' => $crop -> getMsg()
        }
        echo json_encode($response);
    }

    //查看用户信息
    public function get($uid){
        $this->url="/";
        $user=$this->getUser($uid);
        if($user){
            if($user->utype==='d'){
                $this->errMsg="该用户已被删除，无法查看用户信息！";
            }elseif($user->utype==='a'){
                $this->errMsg="该用户未激活，无法查看用户信息！";
            }elseif($user->utype==='b'){
                $this->errMsg="该用户被封禁，无法查看用户信息！";
            }else{
                // $this->successMsg="欢迎进入用户 ".$user->uname." 的个人主页";
                $this->successMsg="";
                $this->url=null;
                Func::getUser($user);
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
        $user=$this->getUser($uid);
        if(!$this->ladmin){
            $this->errMsg="您不是管理员，没有权限查看用户详细信息！";
        }else{
            if($user){
                $this->successMsg="欢迎进入用户 ".$user->uname." 的个人主页";
                Func::getUser($user);
                unset($user->upwd);
                $this->result->data = [
                    'user' => $user
                ];
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
            $sql=User::select("uid","utype","uname","uemail","uinfo","utime")->distinct();
            $where=[];
            foreach ($params as $key=>$v){
                if($v!==""&&$v!==null){
                    if($key==="uname"||$key==="uemail"||$key==="uid"){
                        $where[]=[$key,'like','%'.$v.'%'];
                    }elseif($key==="type"&&in_array($v,$this->config_user['typekey']['all'])){
                        $where[]=['utype','=',$v];
                    }elseif($key==="nickname"||$key==="sn"||$key==="major"||$key==="tel"||$key==="qq"||$key==="wid"){
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
    
            $orderPara = $params['order']??"";
            if($orderPara==="uname"||$orderPara==="uemail"){
                $sql=$sql->orderBy($orderPara)->orderByDesc('uid');
            }elseif($orderPara==="uid"){
                $sql=$sql->orderBy('uid');
            }else{
                $sql=$sql->orderByDesc('uid');
            }
            //echo $sql->toSql();
    
            $users=$sql->paginate($this->config_user['listnum'])->withQueryString();
            foreach($users as $user){
                Func::getUser($user);
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

    //上传东西
    public function upload(Request $request){
        if(!$this->luser){                    //看登录没有
            $this->errMsg="您没有权限上传图片！";
        }else{
            $smfile=$request->file('smfile');
            $userpath='public/img/user/'.$this->luser->uid;
            $rootpath=storage_path('app/'.$userpath);
            $name=time().'.'.$smfile->extension();
            if(!is_dir($rootpath)){
                mkdir($rootpath);
            }
            // TODO: 文件大小类型限制
            
            // if($smfile->storeAs($userpath,$name)){
            //     $file=fopen($rootpath.'/'.$name,'r');
            //     $response = Http::withHeaders([
            //         'Content-Type'=>'multipart/form-data',
            //         'Authorization'=>'yZOeBgtM8XbrsizaLXqbYDYy36ald16s'
            //     ])
            //     ->attach('smfile',$file)
            //     ->post('https://sm.ms/api/v2/upload');
            //     return $response;
            // }
        }
        $this->getResult();
        return $this->result->toJson();

    }
}

