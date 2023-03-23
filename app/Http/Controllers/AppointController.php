<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\Appoint;
use App\Models\Aprocess;
use App\Library\Func;

class AppointController extends Controller
{
    public function indexview(Request $request,$aid){
        if($this->ladmin!==null){
            return view('appoint.index')->with('aactive',true)->with('result',$this->aget($aid));
        }
        return view('appoint.index')->with('aactive',true)->with('result',$this->get($aid));
    }
    public function listview(Request $request){
        return view('appoint.list')->with('aactive',true);
    }
    public function get($aid){
        $this->url="/appoint";
        if (!$this->luser){
            $this->errMsg="您没有权限获取预约信息，请重新登录！";
        }else{
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint===null){
                $this->errMsg="该预约不存在！";
            }elseif($appoint->astate==='d'){
                $this->errMsg="该预约已删除！";
            }elseif($appoint->uid!==$this->luser->uid){
                $this->errMsg="您没有权限查看该预约！";
            }else{
                $appoint->aprocesses=Aprocess::getAprocessByAid($aid);
                $this->getAppoint($appoint);
                $this->successMsg="";
                $this->result->data=[
                    'appoint'=>$appoint
                ];
                $this->url=null;
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function aget($aid){
        $this->url="/appoint";
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取该预约信息！";
        }else{
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint===null){
                $this->errMsg="该预约不存在！";
            }elseif($appoint->astate==='d'){
                $this->errMsg="该预约已删除！";
            }else{
                $appoint->aprocesses=Aprocess::getAprocessByAid($aid);
                $this->getAppoint($appoint);
                $this->successMsg="";
                $this->result->data=[
                    'appoint'=>$appoint
                ];
                $this->url=null;
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function getlist(Request $request){
        if($this->luser===null){
            $this->errMsg="您没有权限获取预约列表！";
            $this->getResult();
            return $this->result->toJson();
        }
        $params=$request->all();

        $sql=Appoint::distinct();
        //条件筛选
        $where[]=['uid','=',$this->luser->uid];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="start"&&strtotime($v)){
                    $where[]=['atime','>=',$v];
                }elseif($key==="end"&&strtotime($v)){
                    $where[]=['atime','<=',$v];
                }elseif($key==="state"&&in_array($v,$this->config_appoint['statekey']['all'])){
                    $where[]=['astate','=',$v];
                }elseif($key==="type"&&in_array($v,['p','r','v'])){
                    $where[]=['atype','=',$v];
                }elseif($key==="uid"||$key==="sid"||$key==="aid"){
                    $where[]=[$key,'=',$v];
                }
            }
        }
        // echo $sql->toSql();

        $sql=$sql->where($where);
        //排序 预约时间倒序 编号倒序
        $orderPara = $params['order']??"";
        if($orderPara==="stime"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('appoint.aid');
        }else{
            $sql=$sql->orderByDesc('appoint.aid');
        }

        //分页
        $appoints=$sql->paginate($this->config_appoint['listnum'])->withQueryString();
        $this->listMsg($appoints);
        //处理预约对象
        foreach ($appoints as $appoint){
            $this->getAppoint($appoint);
        }
        //获取每种预约的数量
        $this->result->data=[
            'appoints'=>$appoints,
            'num'=>$this->getTypeNum(),
        ];
        
        $this->getResult();
        return $this->result->toJson();
    }
    public function agetlist(Request $request){
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取预约列表！";
            $this->getResult();
            return $this->result->toJson();
        }
        $sql=Appoint::distinct();
        $sidsql = DB::table('admin_station')->where('uid',$this->ladmin->uid);
        //条件筛选
        $params=$request->all();
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="cstart"&&strtotime($v)){
                    $where[]=['atime','>=',$v];
                }elseif($key==="cend"&&strtotime($v)){
                    $where[]=['atime','<=',$v];
                }elseif($key==="state"&&in_array($v,$this->config_appoint['statekey']['total'])){
                    $where[]=['astate','=',$v];
                }elseif($key==="type"&&in_array($v,['p','r','v'])){
                    $where[]=['atype','=',$v];
                    $sidsql = $sidsql->where("type","=",$v);
                }elseif($key==="uid"||$key==="sid"||$key==="aid"){
                    $where[]=[$key,'=',$v];
                }
            }
        }
        //管理员筛选预约
        $sids = $sidsql->pluck("sid")->toArray();
        $sql = $sql->whereIn("sid",$sids);

        //排序 开始时间倒序 编号倒序
        $orderPara = $params['order']??"";
        if($orderPara==="cstart"||$orderPara==="cnum"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('appoint.aid');
        }else{
            $sql=$sql->orderByDesc('appoint.aid');
        }

        //分页
        $appoints=$sql->paginate($this->config_appoint['listnum'])->withQueryString();
        $this->listMsg($appoints);
        //获取每种预约的数量
        $this->result->data=[
            'appoints'=>$appoints,
            'num'=>$this->getTypeNum('a',$sids)
        ];
        view()->share("appoints",$appoints);
        $this->getResult();
        return $this->result->toJson();
    }
    public function getTypeNum($utype="u",$sids=null){
        if($utype=="a"){
            $sql = Appoint::select('astate as state',DB::raw("count('aid') as num"))->whereIn("sid",$sids);
            $statekey='total';
        }else{
            $sql=Appoint::select('astate as state',DB::raw("count('aid') as num"))
            ->where('uid','=', $this->luser->uid);
            $statekey='all';
        }
        // echo $sql->toSql();
        $nums = $sql->groupBy('astate')->get();
        $statenum=[
            'sum'=>0,
        ];
        foreach($nums as $item){
            $statenum[$item->state]=$item->num;
        }
        foreach($this->config_appoint['statekey'][$statekey] as $state){
            if(!isset($statenum[$state])){
                $statenum[$state]=0;
            }
            $statenum['sum']+=$statenum[$state];
        }
        return $statenum;
    }
    public function check($sid,$atype,$msg,$atime,&$station=null){
        if(!in_array($atype,$this->config_station['typekey']['total'])){
            $this->errMsg="预约类型有误！";
        }elseif($msg===null||Func::Length($msg)>50){
            $this->errMsg="备注格式不合规范【长度不得大于50】！";
        }elseif(!is_numeric(strtotime($atime))){
            $this->errMsg="预约时间格式有误！";
        }else{
            $station=Station::where('sid',$sid)->first();
            $this->getStation($station);
            if($station===null){
                $this->errMsg="站点不存在！";
            }elseif($station->sstate!=="o"){
                $this->errMsg="站点未开放，无法预约！";
            }elseif($station->sinfo->$atype===false){
                $this->errMsg="站点不提供该类型服务！";
            }elseif(!Func::checkNextDay($atime)){
                $this->errMsg="预约时间不得在明日零时之前！";
            }elseif(!Func::checkDay($atime,$station->stime)){
                $this->errMsg="预约时间不在站点开放时间内！";
            }
        }
        if($this->errMsg!==null){
            return false;
        }
        return true;
    }

    
    public function del($aid){
        if($this->luser===null){
            $this->errMsg="您没有权限删除该预约，请重新登录用户！";
        }else{
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                    if($appoint->astate==='n'||$appoint->astate==='r'||$appoint->astate==='f'){
                        $appoint->astate='d';
                        if($appoint->update()>0){
                            $this->insertAprocess($appoint->aid,$this->luser->uid,'d',"");
                            $this->successMsg="删除该预约成功！";
                        }else{
                            $this->errMsg="删除该预约失败！";
                        }
                    }elseif($appoint->astate==='d'){
                        $this->errMsg="该预约已删除，无需再次删除！";
                    }else{
                        $this->errMsg="该预约已提交，无法删除！";
                    }
                }else{
                    $this->errMsg="您不是该预约的创建者，无法删除！";
                }
            }else{
                $this->errMsg="该预约不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function recover($aid){
        if($this->luser===null){
            $this->errMsg="您没有权限恢复该预约，请重新登录用户！";
        }else{
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                    if($appoint->astate==='d'){
                        $aptype = $this->getAprocess(Aprocess::getAprocessByAid($appoint->aid)[1])->apinfo->type;
                        $appoint->astate=$aptype==='f'?'f':'n';
                        if($appoint->update()>0){
                            $this->insertAprocess($appoint->aid,$this->luser->uid,$appoint->astate,"");
                            $this->successMsg="恢复该预约成功！";
                        }else{
                            $this->errMsg="恢复该预约失败！";
                        }
                    }else{
                        $this->errMsg="该预约未被删除，无需恢复！";
                    }
                }else{
                    $this->errMsg="您不是该预约的创建者，无法恢复！";
                }
            }else{
                $this->errMsg="该预约不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    
    public function refuse(Request $request,$aid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限拒绝该预约，请重新登录管理员！";
        }else{
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->astate==='s'){
                    $msg=$request->post("msg",'');
                    if(!Func::Length($msg)>50){
                        if(DB::table("admin_station")->where("uid",$this->ladmin->uid)->where("sid",$appoint->sid)->exists()){
                            if($appoint->update()>0){
                                $this->insertAprocess($appoint->aid,$this->ladmin->uid,'r',$msg);
                                $this->successMsg="拒绝该预约成功！";
                            }else{
                                $this->errMsg="拒绝该预约失败！";
                            }
                        }else{
                            $this->errMsg="您不是该站点的管理员！";
                        }
                    }else{
                        $this->warnMsg="拒绝理由过长，请修改后重新提交！";
                    }
                }elseif($appoint->astate==='f'){
                    $this->errMsg="该预约已完成，无法拒绝！";
                }elseif($appoint->astate==='d'){
                    $this->errMsg="该预约已删除，无法拒绝！";
                }else{
                    $this->errMsg="该预约未申请，无法拒绝！";
                }
            }else{
                $this->errMsg="该预约不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function approve($aid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限完成该预约，请重新登录管理员！";
        }else{
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->astate==='s'){
                    $station=Station::where('sid',$sid)->first();
                    $this->getStation($station);
                    if($station===null){
                        $this->errMsg="站点不存在，无法完成预约！";
                    }elseif($station->sstate!=="o"){
                        $this->errMsg="站点未开放，无法预约！";
                    }elseif($station->sinfo->$atype===false){
                        $this->errMsg="站点不提供该类型服务！";
                    }else{
                        if(DB::table("admin_station")->where("uid",$this->ladmin->uid)->where("sid",$station->sid)->exists()){
                            if(strtotime($appoint->atime)/86400===time()/86400){
                                if($station->sinfo->approvetime===false||Func::checkDay(time(),$station->stime)){
                                    $appoint->astate='f';
                                    if($appoint->update()>0){
                                        $this->insertAprocess($appoint->aid,$this->ladmin->uid,'f',"");
                                        $this->successMsg="完成该预约成功！";
                                    }else{
                                        $this->errMsg="完成该预约失败！";
                                    }
                                }else{
                                    $this->errMsg="该站点开启了时间限制，请在开放时间内完成！";
                                }
                            }else{
                                $this->errMsg="该预约已过期或未到预定时间！";
                            }
                        }else{
                            $this->errMsg="您不是该站点的管理员！";
                        }
                    }
                }elseif($appoint->astate==='f'){
                    $this->infoMsg="该预约已完成，无需再次完成！";
                }elseif($appoint->astate==='d'){
                    $this->errMsg="该预约已删除，无法完成！";
                }else{
                    $this->errMsg="该预约未申请，无法完成！";
                }
            }else{
                $this->errMsg="该预约不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

    public function cancel(Request $request,$aid){
        if($this->luser!==null){
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                    if($appoint->astate==='s'){
                        $appoint->astate='n';
                        $msg=$request->post("msg",'');
                        if(!Func::Length($msg)>50){
                            if($appoint->update()>0){
                                $this->insertAprocess($appoint->aid,$this->luser->uid,'n',$msg);
                                $this->successMsg="撤销该预约成功！";
                            }else{
                                $this->errMsg="撤销该预约失败！";
                            }
                        }else{
                            $this->warnMsg="撤销理由过长，请修改后重新提交！";
                        }
                    }elseif($appoint->astate==='d'){
                        $this->errMsg="该预约已删除，无法撤销！";
                    }elseif($appoint->astate==='f'){
                        $this->warnMsg="该预约已完成，无法撤销！";
                    }else{
                        $this->errMsg="该预约未申请，无需撤销！";
                    }
                }else{
                    $this->errMsg="您不是该预约的创建者，无法撤销！";
                }
            }else{
                $this->errMsg="该预约不存在！";
            }
        }else{
            $this->errMsg="您没有权限撤销该预约！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function apply($aid){
        if($this->luser!==null){
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                    if($appoint->astate==='n'||$appoint->astate==='r'){
                        $station;
                        if(!$this->check($appoint->sid,$appoint->atype,"",$appoint->atime,$station)){
                            $this->getResult();
                            return $this->result->toJson();
                        }
                        $where=[
                            ['appoint.sid',$appoint->sid],
                            ['atype',$appoint->atype],
                            ['astate','s'],
                        ];
                        $anum=Appoint::where($where)->whereRaw('TO_DAYS(atime)=TO_DAYS(?)',$appoint->atime)->count();
                        if($station->sinfo->{$appoint->atype."num"}-$anum>0){
                            $appoint->astate='s';
                            if($appoint->update()>0){
                                $this->insertAprocess($appoint->aid,$this->luser->uid,'s',json_encode($appoint,JSON_UNESCAPED_UNICODE));
                                $this->successMsg="申请预约成功，联系管理员可快速通过申请！";
                            }else{
                                $this->errMsg="申请该预约失败！";
                            }
                        }else{
                            $this->errMsg="该时间预约已满，请重新选择提交！";
                        }
                    }elseif($appoint->astate==='s'){
                        $this->warnMsg="该预约已申请，请不要重复提交申请！";
                    }elseif($appoint->astate==='f'){
                        $this->infoMsg="该预约已完成，无需申请！";
                    }else{
                        $this->errMsg="该预约已删除，无法申请！";
                    }
                }else{
                    $this->errMsg="您不是该预约的创建者，无法申请！";
                }
            }else{
                $this->errMsg="该预约不存在！";
            }
        }else{
            $this->errMsg="您没有权限申请该预约！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function alter(Request $request,$aid){
        if($this->luser===null){
            $this->errMsg="您没有权限修改预约，请重新登录用户！";
            $this->getResult();
            return $this->result->toJson();
        }
        $appoint=Appoint::where('aid',$aid)->first();
        if($appoint!==null){
            if($appoint->uid===$this->luser->uid){
                if($appoint->astate==='n'||$appoint->astate==='r'){
                    $sid=$request->post("sid",null);
                    $atype=$request->post("atype",null);
                    $atime=$request->post("atime",null);
                    $msg=$request->post('msg',null);
                    if(!$this->check($sid,$atype,$msg,$atime)){
                        $this->getResult();
                        return $this->result->toJson();
                    }
                    $ainfo=[
                        'msg'=>$msg
                    ];
                    $appoint->uid=$this->luser->uid;
                    $appoint->sid=$sid;
                    $appoint->ainfo=json_encode($ainfo,JSON_UNESCAPED_UNICODE);
                    $appoint->atype=$atype;
                    $appoint->atime=$atime;
                    $appoint->astate='n';
                    if($appoint->update()){
                        $this->insertAprocess($appoint->aid,$this->luser->uid,'a',json_encode($appoint,JSON_UNESCAPED_UNICODE));
                        $this->successMsg="预约修改成功！";
                    }else{
                        $this->errMsg='预约修改失败！';
                    }
                }elseif($appoint->astate==='s'){
                    $this->warnMsg="该预约已申请，如需修改，请撤销后再次申请！";
                }elseif($appoint->astate==='f'){
                    $this->errMsg="该预约已完成，无法修改！";
                }else{
                    $this->errMsg="该预约已删除，无法修改！";
                }
            }else{
                $this->errMsg="您不是该预约的创建者，无法修改！";
            }
        }else{
            $this->errMsg="该预约不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }

    public function insert(Request $request){
        if($this->luser===null){
            $this->errMsg="您没有权限预约，请重新登录用户！";
            $this->getResult();
            return $this->result->toJson();
        }
        $sid=$request->post("sid",null);
        $atype=$request->post("atype",null);
        $atime=$request->post("atime",null);
        $msg=$request->post('msg',null);
        if(!$this->check($sid,$atype,$msg,$atime)){
            $this->getResult();
            return $this->result->toJson();
        }
        $ainfo=[
            'msg'=>$msg
        ];
        $appoint=new Appoint();
        $appoint->uid=$this->luser->uid;
        $appoint->sid=$sid;
        $appoint->ainfo=json_encode($ainfo,JSON_UNESCAPED_UNICODE);
        $appoint->atype=$atype;
        $appoint->atime=$atime;
        $appoint->astate='n';
        if($appoint->save()){
            $this->insertAprocess($appoint->aid,$this->luser->uid,'n',json_encode($appoint,JSON_UNESCAPED_UNICODE));
            $this->successMsg="预约创建成功！";
        }else{
            $this->errMsg='预约创建失败！';
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function insertAprocess($aid,$uid,$type,$msg){
        $aprocess=new Aprocess();
        $aprocess->aid=$aid;
        $aprocess->uid=$uid;
        $aprocess->apinfo=json_encode([
            'type'=>$type,
            'msg'=>$msg,
        ],JSON_UNESCAPED_UNICODE);
        $aprocess->save();
    }
}
