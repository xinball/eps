<?php


namespace App\Library;


use Mail;
use App\Models\User;
use App\Mail\Usermail;
use Illuminate\Support\Facades\Redis;

//Func主要用来发送邮件和检查格式是否正确
class Func
{
    static public function sendUserMail($user,$option){
        Mail::to($user->uemail)->send(new Usermail($user,$option));
    }

    //邮箱语法检查
    static public function isEmail($email){
        $pattern="/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/";
        return (bool)preg_match($pattern, $email);
    }
    //时间语法检查
    static public function isTime($time){
        $pattern="/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/";
        return (bool)preg_match($pattern, $time);
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

    //身份证号格式检查
    static public function isUidno($uidno,$uidtype=0){
        switch($uidtype){
            case 0:return Func::checkID($uidno);
            case 1:return Func::checkID($uidno,"hmt");
            case 5:return Func::checkID($uidno);
            case 2:return Func::checkHM($uidno);
            case 3:return Func::checkTW($uidno);
            case 4:return Func::checkPassport($uidno);
        }
    }
    static public function checkID($id,$type=null) {
        $id = strtoupper ($id);
        if($type==="hmt"&&substr($id,0,2)!=="81"&&substr($id,0,2)!=="82"&&substr($id,0,2)!=="83"){
            return false;
        }
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if(!preg_match ($regx, $id)) {
            return false;
        }
        //检查15位
        if(15==strlen ($id)) {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
            @preg_match ($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19".$arr_split[2]. '/'. $arr_split[3]. '/'.$arr_split[4];
            if(!strtotime ($dtm_birth)) {
                return false;
            }else{
                return true;
            }
        }else{
            //检查18位
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/"; 
            @preg_match ($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2]. '/'. $arr_split[3]. '/'.$arr_split[4];
            //检查生日日期是否正确
            if(!strtotime ($dtm_birth)) {
                return false;
            }else{
                //检验18位身份证的校验码是否正确。
                //计算校验码
                $sigma = 0;
                for ($i = 17;$i >= 0;$i --) {
                    $sigma += (pow (2,$i % 10) % 11) * intval ($id{17 - $i},11);
                }
                if($sigma % 11 != 1) {
                    return false;
                }else{
                    return true;
                }
            }
        }
    }
    static public function checkHM($uidno){
        return true;
    }
    static public function checkTW($uidno){
        return true;
    }
    static public function checkPassport($uidno){
        return true;
    }
    //身份证信息与姓名验证接口，可接入身份系统
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

    //是否是数字
    static public function isNum($num,$min=1,$max=10){
        $pattern="/^-?[0-9]{".$min.",".$max."}$/";
        return (bool)preg_match($pattern, $num);
    }
    //是否是浮点数
    static public function isDec($num,$min=1,$max=10){
        $pattern="/^-?[0-9]{".$min.",".$max."}(\.[0-9]{1,})?$/";
        return (bool)preg_match($pattern, $num);
    }
    static public function isNick($nick,$min=2,$max=20){
        $pattern="/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{".$min.",".$max."}$/u";
        return (bool)preg_match($pattern, $nick);
    }

    //是否是号码
    static public function isTel($nick){
        $pattern="/^[ 0-9-]{4,20}$/u";
        return (bool)preg_match($pattern, $nick);
    }

    //得到登录地址的IP
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

    static public function checkNextDay($v){
        return (strtotime($v)>=strtotime(date("Y-m-d",strtotime("+1 day"))))&&(strtotime($v)<=strtotime(date("Y-m-d",strtotime("+8 day"))));
    }
    static public function checkDay($v,$wtime){
        $val=strtotime($v);
        $dtime = $wtime[date("w",$val)];
        if(count($dtime)===0){
            return false;
        }
        $val-=strtotime(date("Y-m-d",$val));
        foreach($dtime as $item){
            if(strtotime($item['start'])-strtotime("00:00")<=$val&&strtotime($item['end'])-strtotime("00:00")>=$val){
                return true;
            }
        }
        return false;
    }

    //
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
