<?php

namespace App\Http\Controllers;

use App\Library\CropAvatar;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Mews\Purifier\Facades\Purifier;
use App\Library\Func;
use DB;

class StationController extends Controller
{
    //
    public function listview(Request $request){
        return view('station.list')->with('sactive',true);
    }
    public function indexview(Request $request,$sid){
        if($this->ladmin!==null){
            //aget与get的区别为aget的返回地址为admin/station，get的返回地址为station
            $result=$this->aget($request,$sid);
        }else{
            $result=$this->get($request,$sid);
        }
        return view('station.index')->with('sactive',true)->with('result',$result);
    }

    public function getlist(Request $request){
        $params=$request->all();
        $where[]=['sstate','=','o'];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="addr"||$key==="des"||$key==="time"){
                    $where[]=['sinfo->'.$key,'like','%'.$v.'%'];
                }elseif($key==="sname"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }elseif($key==="sid"||$key==="state_id"||$key==="city_id"||$key==="region_id"){
                    $where[]=[$key,'=',$v];
                }elseif($key==="atime"){
                    if(!Func::checkNextDay($v)){
                        $this->infoMsg="请选择未来七天内的日期！";
                        $this->getResult();
                        return $this->result->toJson();
                        //$params['atime']=date("Y-m-d",strtotime("+1 day"));
                    }
                }elseif($key==="service"){
                    if(in_array($v,$this->config_station['typekey']['total'])){
                        $where[]=['sinfo->'.$v,'=',"1"];
                    }else{
                        $v==='p';
                        $params['service']='p';
                        $where[]=['sinfo->p','=',"1"];
                    }
                }
            }
        }
        $params['lng']=($params['lng']??73);
        $params['lat']=($params['lat']??3);
        $params['atime']=($params['atime']??date("Y-m-d",strtotime("+1 day")));
        $params['service']=($params['service']??'p');
        
        $sql=Station::getStationlist($where,$params);
        
        //echo $sql->toSql();
        $stations=$sql->paginate($this->config_station['listnum'])->withQueryString();
        $this->listMsg($stations);

        foreach ($stations as $station){
            $this->getStation($station);
        }
        $this->result->data=[
            'stations'=>$stations,
        ];
        $this->getResult();
        return $this->result->toJson();

    }
    
    public function agetlist(Request $request){
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取站点列表！";
            $this->getResult();
            return $this->result->toJson();
        }
        $params=$request->all();
        $where=[];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="addr"||$key==="des"||$key==="time"){
                    $where[]=['sinfo->'.$key,'like','%'.$v.'%'];
                }elseif($key==="sname"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }elseif($key==="sid"||$key==="sstate"||$key==="state_id"||$key==="city_id"||$key==="region_id"){
                    $where[]=[$key,'=',$v];
                }elseif($key==="atime"){
                    // if(!Func::checkNextDay($v)){
                    //     //$params['atime']=date("Y-m-d",strtotime("+1 day"));
                    //     // $this->infoMsg="请选择未来七天内的日期！";
                    //     // $this->getResult();
                    //     // return $this->result->toJson();
                    // }
                }elseif($key==="service"){
                    if(in_array($v,$this->config_station['typekey']['total'])){
                        $where[]=['sinfo->'.$v,'=',"1"];
                    }else{
                        $v==='p';
                        $params['service']='p';
                        $where[]=['sinfo->p','=',"1"];
                    }
                }
            }
        }
        $params['lng']=($params['lng']??73);
        $params['lat']=($params['lat']??3);
        $params['atime']=($params['atime']??date("Y-m-d",strtotime("+1 day")));
        $params['service']=($params['service']??'p');
        $orwhere=[];
        
        $sql=Station::getStationlist($where,$params);
        $sids = Station::getSid($this->ladmin->uid);
        $stations=$sql->paginate($this->config_station['listnum'])->withQueryString();
        $this->listMsg($stations);

        foreach ($stations as $station){
            $this->getStation($station);
            if($this->checkauth('x')||in_array($station->sid,$sids)){
                $station->editable=true;
            }else{
                $station->editable=false;
            }
        }
        $this->result->data=[
            'stations'=>$stations,
        ];
        $this->getResult();
        return $this->result->toJson();
    }
    public function get(Request $request,$sid){
        $station=Station::select("sid","sname","sstate","state_id","city_id","region_id","slng","slat","sinfo","stime")->where('sid',$sid)->first();
        $this->url="/station";
        if($station!==null){
            if($station->sstate==='o'){
                $this->successMsg="获取该站点信息成功";
                $this->url=null;
                $station->sadmin=Station::getSAdminBy($station);
                $station->aadmin=Station::getAAdminBy($station);
                $this->getStation($station);
                $this->result->data=[
                    'station'=>$station,
                ];
            }elseif($station->sstate==='d'){
                $this->errMsg="该站点已删除！";
            }else{
                $this->errMsg="您没有权限查看该站点！";
            }
        }else{
            $this->errMsg="该站点不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function aget(Request $request,$sid){
        $this->url="/admin/station";
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取该站点信息！";
        }else{
            $station=Station::select("sid","sname","sstate","state_id","city_id","region_id","slng","slat","sinfo","stime")->where('sid',$sid)->first();
            if($station!==null){
                if($station->sstate!=='d'){
                    $this->successMsg="您正以 管理员身份 查看该站点信息";
                    $this->url=null;
                    $station->sadmin=Station::getSAdminBy($station);
                    $station->aadmin=Station::getAAdminBy($station);
                    $this->getStation($station);
                    if($this->checkstationauth($station,$this->ladmin->uid)){
                        $station->editable=true;
                    }else{
                        $station->editable=false;
                    }
                    $this->result->data=[
                        'station'=>$station,
                    ];
                    $this->url=null;
                }else{
                    $this->errMsg="该站点已删除！";
                }
            }else{
                $this->errMsg="该站点不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function check($sname,$stime,$state_id,$city_id,$region_id,$sstate,$slng,$slat,&$r,&$p,&$a,&$v,$rnum,$pnum,$anum,$vnum,$des,$addr,$time,&$approvetime,$sid=null){
        if($sstate!=='o'&&$sstate!=='c'){
            $this->errMsg="站点类型有误！请重新创建站点";
        }elseif($sname===null||Func::Length($sname)>50){
            $this->errMsg="站点名称格式不合规范【长度不得大于50】！";
        }elseif($slng===null||!Func::isDec($slng)||doubleval($slng)<73||doubleval($slng)>136){
            $this->errMsg="经度格式不合规范！【73~136】";
        }elseif($slat===null||!Func::isDec($slat)||doubleval($slat)<3||doubleval($slat)>54){
            $this->errMsg="纬度格式不合规范！【3~54】";
        }elseif($stime!=="[]"&&!json_decode($stime,true)){
            $this->errMsg="开放时间格式不合规范！";
        }elseif($addr===null||$addr===""||Func::Length($addr)>200){
            $this->errMsg="地址格式不合规范【长度不得大于200】！";
        }elseif($time===null||$time===""||Func::Length($time)>200){
            $this->errMsg="开放时间描述格式不合规范【长度不得大于200】！";
        }elseif($des===null||$des===""||Func::Length($des)>1000){
            $this->errMsg="站点描述格式不合规范【长度不得大于1000】！";
        }elseif($r!=='1'&&$r!=='0'){
            $this->errMsg="是否提供报备服务格式有误！";
        }elseif($p!=='1'&&$p!=='0'){
            $this->errMsg="是否提供核酸检测服务格式有误！";
        }elseif($a!=='1'&&$a!=='0'){
            $this->errMsg="是否提供抗原检测服务格式有误！";
        }elseif($v!=='1'&&$v!=='0'){
            $this->errMsg="是否提供疫苗接种服务格式有误！";
        }elseif($approvetime!=='1'&&$approvetime!=='0'){
            $this->errMsg="是否限制时间格式有误！";
        }elseif($r==='1'&&$rnum!==null){
            if(!Func::isNum($rnum)||intval($rnum)<0||intval($rnum)>100000){
                $this->errMsg="报备人数格式不合规范！【0~100000】";
            }
        }elseif($p==='1'&&$pnum!==null){
            if(!Func::isNum($pnum)||intval($pnum)<0||intval($pnum)>100000){
                $this->errMsg="核酸检测人数格式不合规范！【0~100000】";
            }
        }elseif($a==='1'&&$anum!==null){
            if(!Func::isNum($anum)||intval($anum)<0||intval($anum)>100000){
                $this->errMsg="抗原检测人数格式不合规范！【0~100000】";
            }
        }elseif($v==='1'&&$vnum!==null){
            if(!Func::isNum($vnum)||intval($vnum)<0||intval($vnum)>100000){
                $this->errMsg="疫苗接种人数格式不合规范！【0~100000】";
            }
        }else{
            if(!$this->checkstationaddr($region_id,$city_id,$state_id)){
                $this->errMsg="您填写的行政区信息有误！";
            }elseif(!$this->checkstationinfoauth($region_id,$city_id,$state_id,$this->ladmin->uid,$sid)){
                $this->errMsg="您对该行政区或站点不具备站点管理权限！";
            }
        }
        if($this->errMsg!==null){
            return false;
        }
        $stime=json_decode($stime,true);
        if(count($stime)!==7){
            $this->errMsg="开放时间格式不合规范【需包含周一到周日开放时间规则】！";
            return false;
        }
        foreach($stime as $sday){
            foreach($sday as $item){
                if(!isset($item['start'])||!isset($item['end'])||!Func::isTime($item['start'])||!Func::isTime($item['end'])){
                    $this->errMsg="开放时间格式不合规范！【需在00:00~23:59之间】";
                    return false;
                }else{
                    $start=strtotime($item['start'])-strtotime("00:00");
                    $end=strtotime($item['end'])-strtotime("00:00");
                    if($start>$end){
                        $this->errMsg='时间段开始时间'.$start.'不得在结束时间'.$end.'之后！';
                        return false;
                    }
                }
            }
        }
        return true;
    }
    public function insert(Request $request){
        if($this->ladmin!==null){
            $sname=$request->post('sname',null);
            $sstate=$request->post("sstate",null);
            $state_id=$request->post("state_id",null);
            $city_id=$request->post("city_id",null);
            $region_id=$request->post("region_id",null);
            $slng=$request->post("slng",null);
            $slat=$request->post("slat",null);

            $stime=$request->post('stime',null);
            $p=$request->post('p',"0");
            $r=$request->post('r',"0");
            $a=$request->post('a',"0");
            $v=$request->post('v',"0");
            $pnum=$request->post('pnum',null);
            $rnum=$request->post('rnum',null);
            $anum=$request->post('anum',null);
            $vnum=$request->post('vnum',null);
            $des=$request->post('des',"");
            $des=Purifier::clean($des);
            $addr=$request->post('addr',"");
            $time=$request->post('time',"");
            $approvetime=$request->post('approvetime',"0");
            if($this->check($sname,$stime,$state_id,$city_id,$region_id,$sstate,$slng,$slat,$r,$p,$a,$v,$rnum,$pnum,$anum,$vnum,$des,$addr,$time,$approvetime)){
                $station = new Station();
                $station->sname=$sname;
                $station->slng=$slng;
                $station->slat=$slat;
                $station->state_id=$state_id;
                $station->city_id=$city_id;
                $station->region_id=$region_id;
                $station->sstate=$sstate;
                $station->stime=$stime;
                $station->sinfo=json_encode([
                    'r'=>$r,
                    'p'=>$p,
                    'a'=>$a,
                    'v'=>$v,
                    'rnum'=>intval($rnum),
                    'pnum'=>intval($pnum),
                    'anum'=>intval($anum),
                    'vnum'=>intval($vnum),
                    'addr'=>$addr,
                    'des'=>$des,
                    'time'=>$time,
                    'approvetime'=>$approvetime,
                ],JSON_UNESCAPED_UNICODE);
                if($station->save()){
                    $this->successMsg="添加站点成功！";
                }else{
                    $this->errMsg='添加问题失败！';
                }
            }
        }else{
            $this->errMsg="您不是管理员，没有权限添加站点！";
        }
        $this->getResult();
        if($this->successMsg){
            $this->insertOperation($request,$this->ladmin->uid,"asi");
        }
        return $this->result->toJson();
    }
    public function del(Request $request,$sid){
        if($this->ladmin!==null){
            $station=Station::where('sid',$sid)->first();
            if($station!==null){
                if($station->sstate==='d'){
                    $this->errMsg="该站点已删除，无需再次删除！";
                }elseif(!$this->checkstationauth($station,$this->ladmin->uid)){
                    $this->errMsg="您对该站点或站点所在区域不具备管理权限，无法删除！";
                }else{
                    $station->sstate='d';
                    if($station->update()>0){
                        $this->successMsg="删除该站点成功！";
                    }else{
                        $this->errMsg="删除该站点失败！";
                    }
                }
            }else{
                $this->errMsg="该站点不存在！";
            }
        }else{
            $this->errMsg="您不是管理员，没有权限删除站点！";
        }
        $this->getResult();
        if($this->successMsg){
            $this->insertOperation($request,$this->ladmin->uid,"asd");
        }
        return $this->result->toJson();
    }
    public function alter(Request $request,$sid){
        if($this->ladmin!==null){                //看管理员是否登录
            $station=Station::where('sid',$sid)->first();               //找到这个站点
            if($station!==null){
                $sname=$request->post('sname',null);
                $sstate=$request->post("sstate",null);
                $state_id=$request->post("state_id",null);
                $city_id=$request->post("city_id",null);
                $region_id=$request->post("region_id",null);
                $slng=$request->post("slng",null);
                $slat=$request->post("slat",null);
    
                $stime=$request->post('stime',null);
                $r=$request->post('r',"0");
                $p=$request->post('p',"0");
                $a=$request->post('a',"0");
                $v=$request->post('v',"0");
                $rnum=$request->post('rnum',null);
                $pnum=$request->post('pnum',null);
                $anum=$request->post('anum',null);
                $vnum=$request->post('vnum',null);
                $des=$request->post('des',null);
                $des=Purifier::clean($des);
                $addr=$request->post('addr',null);
                $time=$request->post('time',null);
                $approvetime=$request->post('approvetime',"0");
                if($this->check($sname,$stime,$state_id,$city_id,$region_id,$sstate,$slng,$slat,$r,$p,$a,$v,$rnum,$pnum,$anum,$vnum,$des,$addr,$time,$approvetime,$sid)){
                    $uids=$request->post('sadmin',null);
                    if($uids==="[]"||json_decode($uids,true)){
                        $uids=json_decode($uids,true);
                        if($this->checkstationauth($station,$this->ladmin->uid)===4){
                            array_push($uids,$this->ladmin->uid);
                        }
                        $uids=array_unique($uids,SORT_NUMERIC);
                        $preuids=[];
                        $tmp=Station::getSAdminBy($station);
                        foreach($tmp as $item){
                            $preuids[]=$item->uid;
                        }
                        foreach($uids as $index=>$uid){
                            $user=User::where('uid',$uid)->first();
                            if($user&&$user->utype!=='d'){
                                if(!in_array($uid,$preuids)){
                                    DB::table('admin_station')->insert(['uid'=>$uid,'sid'=>$sid,'pri'=>count($uids)-$index]);
                                }else{
                                    array_splice($preuids,array_search($uid,$preuids),1);
                                    DB::table('admin_station')->where(['uid'=>$uid,'sid'=>$sid])->update(['pri'=>count($uids)-$index]);
                                }
                            }
                        }
                        foreach($preuids as $uid){
                            DB::table('admin_station')->where('sid','=',$sid)->where('uid','=',$uid)->delete();
                        }
                        $station->sname=$sname;
                        $station->slng=$slng;
                        $station->slat=$slat;
                        $station->state_id=$state_id;
                        $station->city_id=$city_id;
                        $station->region_id=$region_id;
                        $station->sstate=$sstate;
                        $station->stime=$stime;
                        $station->sinfo=json_encode([
                            'r'=>$r,
                            'p'=>$p,
                            'a'=>$a,
                            'v'=>$v,
                            'rnum'=>intval($rnum),
                            'pnum'=>intval($pnum),
                            'anum'=>intval($anum),
                            'vnum'=>intval($vnum),
                            'addr'=>$addr,
                            'des'=>$des,
                            'time'=>$time,
                            'approvetime'=>$approvetime,
                        ],JSON_UNESCAPED_UNICODE);
                        if($station->update()){                //update为真说明修改过，修改成功
                            $this->successMsg="修改站点成功！";
                        }else{
                            $this->errMsg='修改站点失败！';
                        }
                    }else{
                        $this->errMsg="管理员数据格式不合规范！";
                    }
                }
            }else{
                $this->errMsg="该站点不存在！";
            }
        }else{
            $this->errMsg="您不是管理员，没有权限修改站点！";
        }
        $this->getResult();
        if($this->successMsg){
            $this->insertOperation($request,$this->ladmin->uid,"asa");
        }
        return $this->result->toJson();
    }
    public function recover(Request $request,$sid){
        if($this->ladmin!==null){
            $station=Station::where('sid',$sid)->first();
            if($station!==null){
                if($station->sstate!=='d'){
                    $this->errMsg="该站点未删除，无需恢复！";
                }elseif(!$this->checkstationauth($station,$this->ladmin->uid)){
                    $this->errMsg="您对该站点或站点所在区域不具备管理权限，无法恢复！";
                }else{
                    $station->sstate='c';
                    if($station->update()>0){
                        $this->successMsg="恢复该站点成功！";
                    }else{
                        $this->errMsg="恢复该站点失败！";
                    }
                }
            }else{
                $this->errMsg="该站点不存在！";
            }
        }else{
            $this->errMsg="您不是管理员，没有权限恢复站点！";
        }
        $this->getResult();
        if($this->successMsg){
            $this->insertOperation($request,$this->ladmin->uid,"asr");
        }
        return $this->result->toJson();
    }
    public function uploadavatar(Request $request,$sid){
        if($this->ladmin!==null){
            $station=Station::where('sid',$sid)->first();
            if($station!==null){
                if($station->sstate==='d'){
                    $this->errMsg="该站点已删除，无法修改站点图片！";
                }elseif(!$this->checkstationauth($station,$this->ladmin->uid)){
                    $this->errMsg="您对该站点或站点所在区域不具备管理权限，无法修改站点图片！";
                }else{
                    $dstwidth=$this->config_basic["stationwidth"];
                    $crop = new CropAvatar($request->post('avatar_src'), $request->post('avatar_data'), $_FILES['avatar_file'], $this->config_basic["stationavatar"].$sid,$dstwidth,$dstwidth);
                    $response = array(
                        'state'  => 200,
                        'status' => $crop -> getResult()!==null?1:4,
                        'imgurl' => $crop -> getResult()."?".filectime(public_path($crop -> getResult())),
                        'message' => ($crop -> getMsg()!==null?$crop -> getMsg():"上传站点图片成功！")
                    );
                }
            }else{
                $this->errMsg="该站点不存在！";
            }
        }else{
            $this->errMsg="您不是管理员，没有权限修改站点图片！";
        }
        if($this->errMsg!==null){
            $response = array(
                'state'  => 200,
                'status' => 4,
                'imgurl' => $this->config_basic['defaultavatar'],
                'message' => "您没有权限修改站点图片，请重新登录！"
            );
        }else{
            $this->insertOperation($request,$this->ladmin->uid,"asu",json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        echo json_encode($response);
    }
}
