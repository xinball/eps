<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Mews\Purifier\Facades\Purifier;
use App\Library\Func;
use Illuminate\Support\Facades\DB;

class StationController extends Controller
{
    //
    public function listview(Request $request){
        return view('station.list')->with('sactive',true);
    }
    public function indexview(Request $request,$sid){
        if($this->ladmin!==null){
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
                if($key==="addr"||$key==="time"){
                    $where[]=['sinfo->'.$key,'like','%'.$v.'%'];
                }elseif($key==="city_id"||$key==="region_id"){
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
                        $where[]=['sinfo->'.$v,'=',true];
                    }else{
                        $v==='p';
                        $params['service']='p';
                        $where[]=['sinfo->p','=',true];
                    }
                }
            }
        }
        
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
        if(DB::table("admin_area")->where("uid",$this->ladmin->uid))
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="addr"||$key==="time"){
                    $where[]=['sinfo->'.$key,'like','%'.$v.'%'];
                }elseif($key==="sstate"||$key==="city_id"||$key==="region_id"){
                    $where[]=[$key,'=',$v];
                }elseif($key==="atime"){
                    if(!Func::checkNextDay($v)){
                        //$params['atime']=date("Y-m-d",strtotime("+1 day"));
                    }
                }elseif($key==="service"){
                    if(in_array($v,$this->config_station['typekey']['total'])){
                        $where[]=['sinfo->'.$v,'=',true];
                    }else{
                        $v==='p';
                        $params['service']='p';
                        $where[]=['sinfo->p','=',true];
                    }
                }
            }
        }
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
    public function get(Request $request,$sid){
        $station=Station::select("sid","sname","sstate","city_id","region_id","slng","slat","sinfo","stime")->where('sid',$sid)->first();
        $this->url="/station";
        if($station!==null){
            if($station->stype==='o'){
                $this->successMsg="";
                $this->url=null;
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
        $this->url="/station";
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取该站点信息！";
        }else{
            $station=Station::select("sid","sname","sstate","city_id","region_id","slng","slat","sinfo","stime")->where('sid',$sid)->first();
            if($station!==null){
                $this->successMsg="";
                $this->url=null;
                $this->result->data=[
                    'station'=>$station,
                ];
                $this->url=null;
            }else{
                $this->errMsg="该站点不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function check($ptitle,$pdes,$pinfo,$pcases,$in,$out,$cases,$tids,$timelimit,$spacelimit,$tip,$source,$pcasesfile){
        if($ptitle===null||Func::Length($ptitle)>50){
            $this->errMsg="问题标题格式不合规范【长度不得大于50】！";
        }elseif($pdes===null||Func::Length($pdes)>100){
            $this->errMsg="问题描述格式不合规范【长度不得大于100】！";
        }elseif($pinfo===""||Func::Length($pinfo)>100000){
            $this->errMsg="问题详细描述格式不合规范【长度不得大于100000】！";
        }elseif($pcases!=="[]"&&!json_decode($pcases,true)){
            $this->errMsg="问题测试样例格式不合规范！";
        }elseif($tip===""||Func::Length($tip)>200){
            $this->errMsg="提示格式不合规范【长度不得大于200】！";
        }elseif($source===""||Func::Length($source)>200){
            $this->errMsg="来源格式不合规范【长度不得大于200】！";
        }elseif($in===""||Func::Length($in)>1000){
            $this->errMsg="问题输入描述格式不合规范【长度不得大于1000】！";
        }elseif($out===""||Func::Length($out)>1000){
            $this->errMsg="问题输出描述格式不合规范【长度不得大于1000】！";
        }elseif($cases!=="[]"&&!json_decode($cases,true)){
            $this->errMsg="示例格式不合规范！";
        }elseif($tids!=="[]"&&!json_decode($tids,true)){
            $this->errMsg="标签格式不合规范！";
        }elseif(!Func::isNum($timelimit,1)||$timelimit<1||$timelimit>3000){
            $this->errMsg="时间限制不得大于3000ms！";
        }elseif(!Func::isNum($spacelimit,1)||$spacelimit<1||$spacelimit>100000){
            $this->errMsg="空间限制不得大于100000KB！";
        }
        if($this->errMsg!==null){
            return false;
        }
        $cases=json_decode($cases,true);
        foreach($cases as $case){
            if(!isset($case['in'])||!isset($case['out'])){
                $this->errMsg="示例格式不合规范！";
                return false;
            }
        }
        if($pcasesfile!==null){
            $pcases=json_decode($pcases,true);
            foreach($pcases as $i=>$pcase){

                // $this->errMsg=(isset($pcase['in'])===false)."|".!isset($pcase['out'])."|".($pcase['in']!==$i.'.in')."|".($pcase['out']!==$i.'.out')."|".(!isset($pcase['score']))."|".(!Func::isNum($pcase['score'],1));
                if(!isset($pcase['in'])||!isset($pcase['out'])||$pcase['in']!==($i+1).'.in'||$pcase['out']!==($i+1).'.out'||!isset($pcase['score'])||!Func::isNum($pcase['score'],1)){
                    $this->errMsg.="样例格式不合规范！!";
                    return false;
                }
            }
        }
        return true;
    }
    public function insert(Request $request){
        $sstate=$request->post("sstate",null);
        if($this->ladmin===null){
            $this->errMsg="您不是管理员，没有权限添加站点！";
        }elseif($sstate!=='o'&&$sstate!=='c'){
            $this->errMsg="站点类型有误！请重新创建站点";
        }else{
            $station = new Station();
            $city_id=$request->post("city_id",null);
            $region_id=$request->post("region_id",null);
            if(!DB("admin_station")::where("city_id",$city_id)->where("region_id",$region_id)->where("uid",$this->ladmin->uid)->exists()){
                $this->errMsg="您对该行政区不具备站点管理权限";
            }
            $sname=$request->post('sname',null);
            $sdes=$request->post('sdes',null);
            $stime=$request->post('stime',null);

            $slng=$request->post("slng",null);
            $slat=$request->post("slat",null);
            $pnum=$request->post('pnum',null);
            $rnum=$request->post('rnum',null);
            $vnum=$request->post('vnum',null);
            if($this->check($ptitle,$pdes,$pinfo,$pcases,$in,$out,$cases,$tids,$timelimit,$spacelimit,$tip,$source,$pcasesfile)){
                $poption=[
                    'timelimit'=>$timelimit,
                    'spacelimit'=>$spacelimit,
                    'in'=>$in,
                    'out'=>$out,
                    'tip'=>$tip,
                    'cases'=>json_decode($cases,true),
                    'source'=>$source,
                ];
                $problem->ptitle=$ptitle;
                $problem->pdes=$pdes;
                $problem->ptype=$ptype;
                $problem->pinfo=$pinfo;
                $problem->poption=json_encode($poption,JSON_UNESCAPED_UNICODE);
                if($problem->save()){
                    $this->successMsg="添加问题成功！";
                    $this->setcases($pcasesfile,$pcases,$problem);
                    $tids=array_unique(json_decode($tids,true));
                    foreach($tids as $tid){
                        if(Tag::where('tid','=',$tid)->first()){
                            DB::table('problem_tag')->insert([
                                'pid'=>$problem->pid,
                                'tid'=>$tid
                            ]);
                        }
                    }
                }else{
                    $this->errMsg='添加问题失败！';
                }
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function del(Request $request,$pid){
        $utype=$request->get("utype",'');
        $problem=Problem::where('pid',$pid)->first();
        if($problem!==null){
            if($utype==='a'){
                if($this->ladmin===null){
                    $this->errMsg="您不是管理员，没有权限删除问题！";
                }elseif($problem->ptype==='d'){
                    $this->errMsg="该问题已删除，无需再次删除！";
                }
            }else{
                if($this->luser===null){
                    $this->errMsg="您没有权限删除问题，请重新登录！";
                }elseif($problem->ptype==='d'){
                    $this->errMsg="该问题已删除，无需再次删除！";
                }elseif($problem->puid!==$this->luser->uid){
                    $this->errMsg="您不是该问题创建者，无法删除该问题！";
                }
            }
            if($this->errMsg!==null){
                $this->getResult();
                return $this->result->toJson();
            }
            if(DB::table('contest_problem')->where('pid','=',$pid)->first()){
                $this->errMsg="该问题已添加至比赛中，无法删除！";
            }else{
                $problem->ptype='d';
                if($problem->update()>0){
                    $this->successMsg="删除该问题成功！";
                }else{
                    $this->errMsg="删除该问题失败！";
                }
            }
        }else{
            $this->errMsg="该问题不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function alter(Request $request,$pid){
        $utype=$request->post("utype",'');
        $ptype=$request->post("ptype",'');
        $pinfo=$request->post('pinfo','');
        $in=$request->post('in','');
        $out=$request->post('out','');
        $problem=Problem::where('pid',$pid)->first();
        if($problem!==null){
            if($utype==='a'){
                if($this->ladmin===null){
                    $this->errMsg="您不是管理员，没有权限修改问题！";
                }elseif($problem->ptype==='d'){
                    $this->errMsg="该问题已删除，无法修改！";
                }
            }else{
                if($this->luser===null){
                    $this->errMsg="您没有权限修改问题，请重新登录！";
                }elseif($problem->ptype==='d'){
                    $this->errMsg="该问题已删除，无法修改！";
                }elseif($problem->puid!==$this->luser->uid){
                    $this->errMsg="您不是该问题创建者，无法修改该问题！";
                }else{
                    $pinfo=Purifier::clean($pinfo);
                    $in=Purifier::clean($in);
                    $out=Purifier::clean($out);
                }
            }
            if($this->errMsg!==null){
                $this->getResult();
                return $this->result->toJson();
            }
            if($ptype!=='m'&&DB::table('contest_problem')->where('pid','=',$pid)->first()){
                $this->errMsg="该问题已添加至比赛中，无法修改类型！";
            }else{
                $ptitle=$request->post('ptitle','');
                $pdes=$request->post('pdes',"");
                $pcases="[]";
                $pcasesfile=null;
                if($request->hasFile('pcasesfile')){
                    $pcasesfile=$request->file('pcasesfile');
                    if($pcasesfile!==null&&$pcasesfile->isValid()){
                        $pcases=$request->post('pcases',"[]");
                    }
                }
    
                $tip=$request->post('tip','');
                $source=$request->post('source','');
                $cases=$request->post('cases',"");
                $tids=$request->post('tids','');
                $timelimit=$request->post('timelimit',"");
                $spacelimit=$request->post('spacelimit',"");
                if($this->check($ptitle,$pdes,$pinfo,$pcases,$in,$out,$cases,$tids,$timelimit,$spacelimit,$tip,$source,$pcasesfile)){
                    $poption=[
                        'timelimit'=>$timelimit,
                        'spacelimit'=>$spacelimit,
                        'in'=>$in,
                        'out'=>$out,
                        'tip'=>$tip,
                        'source'=>$source,
                        'cases'=>json_decode($cases,true),
                    ];
                    $problem->ptitle=$ptitle;
                    $problem->pdes=$pdes;
                    $problem->ptype=$ptype;
                    $problem->pinfo=$pinfo;
                    $this->setcases($pcasesfile,$pcases,$problem);
                    $problem->poption=json_encode($poption,JSON_UNESCAPED_UNICODE);
                    if($problem->update()){
                        $this->successMsg="修改问题成功！";
                        $pretids=Tag::getTidsByPid($pid);
                        $tids=array_unique(json_decode($tids,true));
                        foreach($tids as $tid){
                            if(!in_array($tid,$pretids)){
                                if(Tag::where('tid',$tid)->first()){
                                    DB::table('problem_tag')->insert([
                                        'pid'=>$pid,
                                        'tid'=>$tid
                                    ]);
                                }
                            }else{
                                array_splice($pretids,array_search($tid,$pretids),1);
                            }
                        }
                        foreach($pretids as $tid){
                            DB::table('problem_tag')->where('tid','=',$tid)->where('pid','=',$pid)->delete();
                        }
                    }else{
                        $this->errMsg='修改问题失败！';
                    }
                }
            }
        }else{
            $this->errMsg="该问题不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function recover(Request $request,$pid){
        $utype=$request->get("utype",'');
        $problem=Problem::where('pid',$pid)->first();
        if($problem!==null){
            if($utype==='a'){
                if($this->ladmin===null){
                    $this->errMsg="您不是管理员，没有权限恢复问题！";
                }elseif($problem->ptype!=='d'){
                    $this->errMsg="该问题未删除，无需恢复！";
                }else{
                    $problem->ptype='h';
                }
            }else{
                if($this->luser===null){
                    $this->errMsg="您没有权限恢复问题，请重新登录！";
                }elseif($problem->ptype!=='d'){
                    $this->errMsg="该问题未删除，无需恢复！";
                }elseif($problem->puid!==$this->luser->uid){
                    $this->errMsg="您不是该问题创建者，无法恢复该问题！";
                }else{
                    $problem->ptype='m';
                }
            }
            if($this->errMsg!==null){
                $this->getResult();
                return $this->result->toJson();
            }
            if($problem->update()>0){
                $this->successMsg="恢复该问题成功！";
            }else{
                $this->errMsg="恢复该问题失败！";
            }
        }else{
            $this->errMsg="该问题不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
}
