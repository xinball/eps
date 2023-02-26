<?php


namespace App\Library;


use App\Models\User;
use Illuminate\Support\Facades\Redis;

//Func主要用来发送邮件和检查格式是否正确
class Func
{
    //发送邮件
    static public function sendMail($test="xinball@qq.com",$subject="测试",$body="<b>测试</b>",$file="")
    {
        $mail = new Smtp();

        $mail->setServer("smtp.exmail.qq.com", "zhouqing@xinball.top", "Zq000528,", 465, true); //参数1（qq邮箱使用smtp.qq.com，qq企业邮箱使用smtp.exmail.qq.com），参数2（邮箱登陆账号），参数3（邮箱登陆密码，也有可能是独立密码，就是开启pop3/smtp时的授权码），参数4（默认25，腾云服务器屏蔽25端口，所以用的465），参数5（是否开启ssl，用465就得开启）//$mail->setServer("XXXXX", "joffe@XXXXX", "XXXXX", 465, true);
        $mail->setFrom("zhouqing@xinball.top","EPS"); //发送者邮箱
        $mail->setReceiver($test); //接收者邮箱
        $mail->addAttachment($file); //Attachment 附件，不用可注释
        $mail->setMail($subject, $body); //标题和内容
        $mail->send();//可以var_dump一下，发送成功会返回true，失败false
    }

    //邮箱语法检查
    static public function isEmail($email){
        $pattern="/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/";
        return (bool)preg_match($pattern, $email);
    }

    //获取长度
    static public function Length($text){
        return mb_strlen($text);
    }

    //名字语法检查
    static public function isUname($uname,$min=2,$max=30){
        $pattern="/^[<\x{4e00}-\x{9fa5}>+\·?<\x{4e00}-\x{9fa5}>+]{2,30}$/u";
        return (bool)preg_match($pattern, $uname)&&self::Length($uname)>=$min&&self::Length($uname)<=$max;
    }

    //id语法检查
    static public function isUid($uname){
        $pattern="/^\d{1,12}$/";
        return (bool)preg_match($pattern, $uname);
    }
    
    //id的种类语法检查
    static public function isUidtype($uidtype){
        $pattern="/^\d{1}$/";
        if(!(bool)preg_match($pattern, $uidtype))
            return false;
        $uidtypeval=intval($uidtype);
        return $uidtypeval>=0&&$uidtypeval<=5;
    }

    //身份证号语法检查
    static public function isUidno($uidno,$uidtype=0){
        $pattern=[
            "/^[1-9]\d{5}(18|19|([23]\d))\d{2}(0[1-9]|10|11|12)([0-2][1-9]|10|20|30|31)\d{3}[0-9Xx]$/",
            "/^$/",
            "/^$/",
            "/^$/",
            "/^$/",
            "/^$/",
        ];
        return (bool)preg_match($pattern[$uidtype], $uidno);
    }


    static public function isUidnoUname($uidno,$uname){
        return true;
    }

    //前缀
    static public function isPrefix($prefix,$min=3,$max=6){
        $pattern="/^[a-zA-Z][a-zA-Z]*[0-9]{0,2}$/";
        return (bool)preg_match($pattern, $prefix)&&self::Length($prefix)>=$min&&self::Length($prefix)<=$max;
    }

    //密码语法检查
    static public function isPwd($pwd,$min=8,$max=20){
        $pattern="/^[a-zA-Z0-9_,.!#%^&*]{".$min.",".$max."}$/";
        return (bool)preg_match($pattern, $pwd);
    }


    static public function isNum($num,$min=2,$max=10){
        $pattern="/^[0-9]{".$min.",".$max."}$/";
        return (bool)preg_match($pattern, $num);
    }
    static public function isNick($nick,$min=2,$max=20){
        $pattern="/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{".$min.",".$max."}$/u";
        return (bool)preg_match($pattern, $nick);
    }
    static public function isTel($nick){
        $pattern="/^[ 0-9-]{4,20}$/u";
        return (bool)preg_match($pattern, $nick);
    }

    //补充获取用户信息，头像和背景，并对json格式字符串解码
    static public function getUser(User $user){
        $user->avatar=Func::getAvatar($user->uid);
        $user->banner=Func::getBanner($user->uid);
        $user->uinfo=json_decode($user->uinfo,true);
    }

    //登录页面，输入完身份证或者邮箱后，点击别处会刷新，如果该用户有自己头像就显示自己头像，没有就显示默认头像
    static public function getAvatar($uid=null){
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


    static public function getCAvatar($cid=null){
        if($cid){
            $href=($basicConfig['contestavatar'] ?? "/img/contest/").$cid.".png";
            if(is_file(public_path($href))){
                return url('/').$href."?".filectime(public_path($href));
            }
        }
        return url('/bootstrap/icon/trophy-fill-user.svg');
    }


    //有则用自己，没有用默认
    static public function getBanner($uid=null){
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

    //得到用户的IP
    static public function getIp() {
        $ch = curl_init('http://www.ip138.com/ip2city.asp');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $a  = curl_exec($ch);
        preg_match('/[(.*)]/', $a, $ip);
        if($ip==null||$ip[1]==null||$ip[1]==''){
            $realIp="";
            if (isset($_SERVER)){
                if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                    $realIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $realIp = $_SERVER["HTTP_CLIENT_IP"];
                } else {
                    $realIp = $_SERVER["REMOTE_ADDR"];
                }
            } else {
                if (getenv("HTTP_X_FORWARDED_FOR")){
                    $realIp = getenv("HTTP_X_FORWARDED_FOR");
                } else if (getenv("HTTP_CLIENT_IP")) {
                    $realIp = getenv("HTTP_CLIENT_IP");
                } else {
                    $realIp = getenv("REMOTE_ADDR");
                }
            }
            return $realIp;
        }else{
            return $ip[1];
        }
    }

    //？？？
    static public function getStatement(){
        while(1){
            $rand=rand(1,100);
            if($rand>0&&$rand<=35){
                $time=json_decode(Redis::hGet('statement','time')!=null?Redis::hGet('statement','time'):"[]",true);
                if(sizeof($time)>0){
                    return $time[rand(0,sizeof($time)-1)];
                }else{
                    continue;
                }
            }elseif($rand>35&&$rand<=70){
                $hours=json_decode(Redis::hGet('statement','hours')!=null?Redis::hGet('statement','hours'):"{}",true);
                $index=floor(((date('G')+1)%24)/2);
                if(isset($hours[$index])&&$hours[$index]!=null){
                    $hour=$hours[$index];
                    return $hour[rand(0,sizeof($hour)-1)];
                }else{
                    continue;
                }
            }else{
                $statement=json_decode(file_get_contents(Redis::hGet('statement','api')!=null?Redis::hGet('statement','api'):"https://v1.hitokoto.cn"));
                if($statement!=false&&isset($statement->id))
                return $statement->hitokoto."——".$statement->from.($statement->from_who!=null?$statement->from_who:"");
            }
        }
    }
}
