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
    public function checklim($appoint){
        if(Aprocess::counttoday($appoint->aid,$appoint->uid)>$this->config_appoint['todaylim']){
            $this->errMsg="您今日对该条预约/报备操作已超出限制！";
            $this->getResult();
            return false;
        }
        if(Aprocess::countappoint($appoint->aid,$appoint->uid)>$this->config_appoint['applim']){
            $this->errMsg="您对该条预约/报备操作已超出限制！";
            $this->getResult();
            return false;
        }
        if(Aprocess::counttotal($appoint->uid)>$this->config_appoint['totallim']){
            $this->errMsg="您今日操作已超出限制！";
            $this->getResult();
            return false;
        }
        return true;
    }
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
            $this->errMsg="您没有权限获取预约/报备信息，请重新登录！";
        }else{
            $appoint=Appoint::select("aid","ainfo","astate","atime","atype","appoint.sid","appoint.uid","uname","sname")->where('aid',$aid)->join('user','user.uid','appoint.uid')->join('station','station.sid','appoint.sid')->first();
            if($appoint===null){
                $this->errMsg="该预约/报备不存在！";
            }elseif($appoint->astate==='d'){
                $this->errMsg="该预约/报备已删除！";
            }elseif($appoint->uid!==$this->luser->uid){
                $this->errMsg="您没有权限查看该预约/报备！";
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
            $this->errMsg="您没有权限获取该预约/报备信息！";
        }else{
            $appoint=Appoint::select("aid","ainfo","astate","atime","atype","appoint.sid","appoint.uid","uname","sname")->where('aid',$aid)->join('user','user.uid','appoint.uid')->join('station','station.sid','appoint.sid')->first();
            if($appoint===null){
                $this->errMsg="该预约/报备不存在！";
            }elseif($appoint->astate==='d'){
                $this->errMsg="该预约/报备已删除！";
            }else{
                $station=Station::where('sid',$appoint->sid)->first();
                if($station===null||!$this->checkstationauth($station,$this->ladmin->uid)){
                    $this->errMsg="您没有权限查看该 预约/报备 所在站点的 预约/报备数据！";
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
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function getlist(Request $request){
        if($this->luser===null){
            $this->errMsg="您没有权限获取预约/报备列表！";
            $this->getResult();
            return $this->result->toJson();
        }
        $params=$request->all();

        $sql=Appoint::select("aid","appoint.uid","appoint.sid","user.uname","station.sname","atime","atype","astate","ainfo")->distinct();
        //条件筛选
        $where[]=['appoint.uid','=',$this->luser->uid];
        $types=[];
        $astate=null;
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="msg"){
                    $where[]=['ainfo->msg','like','%'.$v.'%'];
                }elseif($key==="start"&&strtotime($v)){
                    $where[]=['atime','>=',$v];
                }elseif($key==="end"&&strtotime($v)){
                    $where[]=['atime','<=',$v];
                }elseif($key==="astate"&&in_array($v,$this->config_appoint['statekey']['total'])){
                    $astate=$v;
                }elseif($key==="type"){
                    $type = json_decode($v);
                    if($type!==null){
                        foreach($type as $item){
                            if(isset($this->config_station['type'][$item])){
                                $types[]=$item;
                            }
                        }
                    }
                }elseif($key==="sid"||$key==="aid"){
                    $where[]=["appoint.".$key,'=',$v];
                }
            }
        }

        $sql=$sql->where($where);
        if($astate!==null){
            $sql=$sql->where('astate',$astate);
        }
        if(count($types)>0){
            $sql=$sql->whereIn('atype',$types);
        }
        //echo $sql->toSql();
        //排序 预约/报备时间倒序 编号倒序
        $orderPara = $params['order']??"";
        $desc = $params['desc']??"1";
        if($orderPara==="atime"){
            if($desc==='1'){
                $sql=$sql->orderByDesc($orderPara)->orderBy('appoint.aid');
            }else{
                $sql=$sql->orderBy($orderPara)->orderBy('appoint.aid');
            }
        }else{
            if($desc==='1'){
                $sql=$sql->orderByDesc('appoint.aid');
            }else{
                $sql=$sql->orderBy('appoint.aid');
            }
        }

        //分页
        $appoints=$sql->join('station','appoint.sid','station.sid')->join('user','appoint.uid','user.uid')->paginate($this->config_appoint['listnum'])->withQueryString();
        $this->listMsg($appoints);
        //处理预约/报备对象
        foreach ($appoints as $appoint){
            $this->getAppoint($appoint);
        }
        //获取每种预约/报备的数量
        $this->result->data=[
            'appoints'=>$appoints,
            'num'=>$this->getTypeNum($where,$types,'u'),
        ];
        
        $this->getResult();
        return $this->result->toJson();
    }
    public function agetlist(Request $request){
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取预约/报备列表！";
            $this->getResult();
            return $this->result->toJson();
        }
        $sql=Appoint::select("aid","appoint.uid","appoint.sid","user.uname","station.sname","atime","atype","astate","ainfo")->distinct();
        //条件筛选
        $params=$request->all();
        $where=[];
        $types=[];
        $astate=null;
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="msg"){
                    $where[]=['ainfo->msg','like','%'.$v.'%'];
                }elseif($key==="start"&&strtotime($v)){
                    $where[]=['atime','>=',$v];
                }elseif($key==="end"&&strtotime($v)){
                    $where[]=['atime','<=',$v];
                }elseif($key==="astate"&&in_array($v,$this->config_appoint['statekey']['total'])){
                    $astate=$v;
                }elseif($key==="type"){
                    $type = json_decode($v);
                    if($type!==null){
                        foreach($type as $item){
                            if(isset($this->config_station['type'][$item])){
                                $types[]=$item;
                            }
                        }
                    }
                }elseif($key==="uid"||$key==="sid"||$key==="aid"){
                    $where[]=["appoint.".$key,'=',$v];
                }
            }
        }
        //管理员筛选预约/报备
        $sids = Station::getSid($this->ladmin->uid);
        $sql = $sql->join('station','appoint.sid','station.sid')->join('user','appoint.uid','user.uid')->where($where);
        if($astate!==null){
            $sql=$sql->where('astate',$astate);
        }
        if(!$this->checkauth('x')){
            $sql=$sql->whereIn("appoint.sid",$sids);
        }
        if(count($types)>0){
            $sql=$sql->whereIn('atype',$types);
        }

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
        //获取每种预约/报备的数量
        $this->result->data=[
            'appoints'=>$appoints,
            'num'=>$this->getTypeNum($where,$types,'a',$sids)
        ];
        view()->share("appoints",$appoints);
        $this->getResult();
        return $this->result->toJson();
    }
    public function getTypeNum($where,$types,$utype,$sids=null){
        $statekey='total';
        if($utype=="a"){
            $sql = Appoint::select('astate',DB::raw("count('aid') as num"))->where($where)->whereIn("sid",$sids);
        }else{
            $sql=Appoint::select('astate',DB::raw("count('aid') as num"))->where($where)->where('uid','=', $this->luser->uid);
        }
        if(count($types)>0){
            $sql=$sql->whereIn('atype',$types);
        }
        // echo $sql->toSql();
        $nums = $sql->groupBy('astate')->get();
        $statenum=[
            ''=>0,
        ];
        foreach($nums as $item){
            $statenum[$item->astate]=$item->num;
        }
        foreach($this->config_appoint['statekey'][$statekey] as $astate){
            if(!isset($statenum[$astate])){
                $statenum[$astate]=0;
            }
            $statenum['']+=$statenum[$astate];
        }
        return $statenum;
    }
    public function check($sid,$atype,$msg,$atime,&$station=null){
        if(!in_array($atype,$this->config_station['typekey']['total'])){
            $this->errMsg="预约/报备服务有误！";
        }elseif($msg===""||Func::Length($msg)>50){
            $this->errMsg="备注格式不合规范【长度不得大于50】！";
        }elseif(!is_numeric(strtotime($atime))){
            $this->errMsg="预约/报备时间格式有误！";
        }else{
            $station=Station::where('sid',$sid)->first();
            $this->getStation($station);
            if($station===null){
                $this->errMsg="站点不存在！";
            }elseif($station->sstate!=="o"){
                $this->errMsg="站点未开放，无法预约/报备！";
            }elseif($station->sinfo->{$atype}==='0'){
                $this->errMsg="站点暂时不提供 ".$this->config_station['type'][$atype]['label']." 类型服务！";
            }elseif(!Func::checkNextDay($atime)){
                $this->errMsg="预约/报备时间不得在明日零时之前！";
            }elseif(!Func::checkDay($atime,$station->stime)){
                $this->errMsg="预约/报备时间不在站点开放时间内！";
            }
        }
        if($this->errMsg!==null){
            $this->getResult();
            return false;
        }
        return true;
    }
    public function checkreport($atype,$ainfo){
        if(($atype==='r'&&isset($ainfo->taddr)&&isset($ainfo->faddr)&&isset($ainfo->tstate_id)&&isset($ainfo->fstate_id)&&isset($ainfo->tcity_id)&&isset($ainfo->fcity_id)&&isset($ainfo->tregion_id)&&isset($ainfo->fregion_id)&&isset($ainfo->msg))||$atype!=='r'){
            return true;
        }
        $this->errMsg="报备信息有误";
        $this->getResult();
        return false;
    }
    public function checkapprove($atype,$apinfo){
        if($atype==='r'){
            if(isset($apinfo->state_id)&&isset($apinfo->msg)){
                return true;
            }else{
                $this->errMsg="报备完成信息有误！";
            }
        }elseif($atype==='a'){
            if(isset($apinfo->result)&&isset($apinfo->msg)){
                return true;
            }else{
                $this->errMsg="抗原完成信息有误！";
            }
        }elseif($atype==='v'){
            if(isset($apinfo->result)&&isset($apinfo->msg)){
                return true;
            }else{
                $this->errMsg="疫苗完成信息有误！";
            }
        }elseif($atype==='p'){
            if(isset($apinfo->result)&&isset($apinfo->msg)){
                return true;
            }else{
                $this->errMsg="核酸完成信息有误！";
            }
        }
        $this->getResult();
        return false;
    }
    //删除
    public function del($aid){
        if($this->luser!==null){
            if(!$this->checkAprocess($this->luser->uid)){
                return $this->result->toJson();
            }
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                        if($appoint->astate==='n'||$appoint->astate==='r'||$appoint->astate==='f'){
                            $appoint->astate='d';
                            if($appoint->update()>0){
                                $this->insertAprocess($appoint->aid,$this->luser->uid,'d',"");
                                Func::sendAppointMail($this->luser,[
                                    'subject'=>$this->config_basic['name']."-预约/报备-删除",
                                    'name'=>$this->luser->uname,
                                    'utype'=>'用户',
                                    'des'=>"删除了一条预约/报备！",
                                    'aid'=>$aid,
                                ],$this->luser->uemail);
                                $this->successMsg="删除该预约/报备成功！";
                            }else{
                                $this->errMsg="删除该预约/报备失败！";
                            }
                        }elseif($appoint->astate==='d'){
                            $this->errMsg="该预约/报备已删除，无需再次删除！";
                        }else{
                            $this->errMsg="该预约/报备已提交，无法删除！";
                        }
                }else{
                    $this->errMsg="您不是该预约/报备的创建者，无法删除！";
                }
            }else{
                $this->errMsg="该预约/报备不存在！";
            }
        }else{
            $this->errMsg="您没有权限删除该预约/报备，请重新登录用户！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function recover($aid){
        if($this->luser===null){
            $this->errMsg="您没有权限恢复该预约/报备，请重新登录用户！";
        }else{
            if(!$this->checkAprocess($this->luser->uid)){
                return $this->result->toJson();
            }
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                    if($appoint->astate==='d'){
                        if(!$this->checklim($appoint)){
                            return $this->result->toJson();
                        }
                        $temappoint = clone $appoint;
                        $appoint->aprocesses=Aprocess::getAprocessByAid($aid);
                        $this->getAppoint($appoint);
                        foreach($appoint->aprocesses as $aprocess){
                            $aptype=$aprocess->apinfo->type;
                            if($aptype==='d'||$aptype==='e'){
                                continue;
                            }elseif($aptype==='a'||$aptype==='n'||$aptype==='c'){
                                $temappoint->astate='n';
                                break;
                            }elseif($aptype==='f'){
                                $temappoint->astate='f';
                                break;
                            }elseif($aptype==='r'){
                                $temappoint->astate='r';
                                break;
                            }
                        }
                        if($temappoint->update()>0){
                            $this->insertAprocess($temappoint->aid,$this->luser->uid,'e',"");
                            Func::sendAppointMail($this->luser,[
                                'subject'=>$this->config_basic['name']."-预约/报备-恢复",
                                'name'=>$this->luser->uname,
                                'utype'=>'用户',
                                'des'=>"恢复了一条预约/报备！",
                                'aid'=>$aid,
                            ],$this->luser->uemail);
                            $this->successMsg="恢复该预约/报备成功！";
                        }else{
                            $this->errMsg="恢复该预约/报备失败！";
                        }
                    }else{
                        $this->errMsg="该预约/报备未被删除，无需恢复！";
                    }
                }else{
                    $this->errMsg="您不是该预约/报备的创建者，无法恢复！";
                }
            }else{
                $this->errMsg="该预约/报备不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    

    //拒绝预约/报备
    public function refuse(Request $request,$aid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限拒绝该预约/报备，请重新登录管理员！";
        }else{
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->astate==='s'){
                    $msg=$request->post("msg",'');
                    if(Func::Length($msg)<=50&&Func::Length($msg)>=5){
                        $station=Station::where('sid',$appoint->sid)->first();
                        if($this->checkstationauth($station,$this->ladmin->uid)){
                            $appoint->astate='r';
                            if($appoint->update()>0){
                                $this->insertAprocess($appoint->aid,$this->ladmin->uid,'r',$msg);
                                $user = $this->getUserBy($appoint->uid);
                                Func::sendAppointDefMail($this->ladmin,[
                                    'subject'=>$this->config_basic['name']."-预约/报备-拒绝",
                                    'name'=>$user->uname,
                                    'utype'=>'管理员',
                                    'des'=>"拒绝了一条预约/报备，请您进行后续处理！",
                                    'aid'=>$aid,
                                ],$user->uemail,Station::getAdminBy($station));
                                $this->successMsg="拒绝该预约/报备成功！";
                            }else{
                                $this->errMsg="拒绝该预约/报备失败！";
                            }
                        }else{
                            $this->errMsg="您不是该站点的管理员！";
                        }
                    }else{
                        $this->warnMsg="拒绝理由格式有误【5~50个字符】，请修改后重新提交！";
                    }
                }elseif($appoint->astate==='f'){
                    $this->errMsg="该预约/报备已完成，无法拒绝！";
                }elseif($appoint->astate==='d'){
                    $this->errMsg="该预约/报备已删除，无法拒绝！";
                }else{
                    $this->errMsg="该预约/报备未申请，无法拒绝！";
                }
            }else{
                $this->errMsg="该预约/报备不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

    //同意
    public function approve($aid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限完成该预约/报备，请重新登录管理员！";
        }else{
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->astate==='s'){
                    $station=Station::where('sid',$appoint->sid)->first();
                    $this->getStation($station);
                    if($this->checkstationauth($station,$this->ladmin->uid)){
                        if(!json_decode($apinfo)){
                            $this->errMsg="预约/报备完成信息格式有误！";
                            $this->getResult();
                            return $this->result->toJson();
                        }else{
                            $apinfo=json_decode($apinfo);
                            if(!$this->checkapprove($appoint->atype,$apinfo)){
                                return $this->result->toJson();
                            }
                        }
                        if(strtotime($appoint->atime)<strtotime(date("Y-m-d",strtotime("+1 day")))&&strtotime($appoint->atime)>strtotime(date("Y-m-d",strtotime("today")))){
                            if($station->sinfo->approvetime===false||Func::checkDay(time(),$station->stime)){
                                $temappoint->astate='f';
                                if($temappoint->update()>0){
                                    $this->insertAprocess($appoint->aid,$this->ladmin->uid,'f',$apinfo);
                                    $user = $this->getUserBy($appoint->uid);
                                    Func::sendAppointDefMail($this->ladmin,[
                                        'subject'=>$this->config_basic['name']."-预约/报备-同意",
                                        'name'=>$user->uname,
                                        'utype'=>'管理员',
                                        'des'=>"同意了一条预约/报备！",
                                        'aid'=>$aid,
                                    ],$user->uemail,Station::getAdminBy($station));
                                    $this->successMsg="完成该预约/报备成功！";
                                }else{
                                    $this->errMsg="完成该预约/报备失败！";
                                }
                            }else{
                                $this->errMsg="该站点开启了时间限制，请在开放时间内完成！";
                            }
                        }else{
                            $this->errMsg="该预约/报备需在当天完成！";
                        }
                    }else{
                        $this->errMsg="您不是该站点的管理员！";
                    }
                }elseif($appoint->astate==='f'){
                    $this->infoMsg="该预约/报备已完成，无需再次完成！";
                }elseif($appoint->astate==='d'){
                    $this->errMsg="该预约/报备已删除，无法完成！";
                }else{
                    $this->errMsg="该预约/报备未申请，无法完成！";
                }
            }else{
                $this->errMsg="该预约/报备不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

    //撤销预约/报备
    public function cancel(Request $request,$aid){
        if($this->luser!==null){
            if(!$this->checkAprocess($this->luser->uid)){
                return $this->result->toJson();
            }
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                    if($appoint->astate==='s'){
                        $appoint->astate='n';
                        $msg=$request->post("msg",'');
                        if(Func::Length($msg)<=50&&Func::Length($msg)>=5){
                            if($appoint->update()>0){
                                $this->insertAprocess($appoint->aid,$this->luser->uid,'c',$msg);
                                $station=Station::where('sid',$appoint->sid)->first();
                                Func::sendAppointDefMail($this->luser,[
                                    'subject'=>$this->config_basic['name']."-预约/报备-撤销",
                                    'name'=>"各位站点管理员及区域管理员",
                                    'utype'=>'用户',
                                    'des'=>"撤销了一条预约/报备！",
                                    'aid'=>$aid,
                                ],Station::getAdminBy($station),$this->luser->uemail);
                                $this->successMsg="撤销该预约/报备成功！";
                            }else{
                                $this->errMsg="撤销该预约/报备失败！";
                            }
                        }else{
                            $this->warnMsg="撤销理由格式有误【5~50个字符】，请修改后重新提交！";
                        }
                    }elseif($appoint->astate==='d'){
                        $this->errMsg="该预约/报备已删除，无法撤销！";
                    }elseif($appoint->astate==='f'){
                        $this->warnMsg="该预约/报备已完成，无法撤销！";
                    }else{
                        $this->errMsg="该预约/报备未申请，无需撤销！";
                    }
                }else{
                    $this->errMsg="您不是该预约/报备的创建者，无法撤销！";
                }
            }else{
                $this->errMsg="该预约/报备不存在！";
            }
        }else{
            $this->errMsg="您没有权限撤销该预约/报备！";
        }
        $this->getResult();
        return $this->result->toJson();
    }

    //申请
    public function apply($aid){
        if($this->luser!==null){
            if(!$this->checkAprocess($this->luser->uid)){
                return $this->result->toJson();
            }
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                    if($appoint->astate==='n'||$appoint->astate==='r'){
                        if(!$this->checklim($appoint)){
                            return $this->result->toJson();
                        }
                        $temappoint = clone $appoint;
                        $this->getAppoint($appoint);
                        $station;
                        if(!$this->check($appoint->sid,$appoint->atype,$appoint->ainfo->msg,$appoint->atime,$station)){
                            return $this->result->toJson();
                        }
                        $where=[
                            ['appoint.sid',$appoint->sid],
                            ['atype',$appoint->atype],
                            ['astate','s'],
                        ];
                        $anum=Appoint::where($where)->whereRaw('TO_DAYS(atime)=TO_DAYS(?)',$appoint->atime)->count();
                        if($station->sinfo->{$appoint->atype."num"}-$anum>0){
                            $temappoint->astate='s';
                            if($temappoint->update()>0){
                                $this->insertAprocess($temappoint->aid,$this->luser->uid,'s',$temappoint);
                                Func::sendAppointDefMail($this->luser,[
                                    'subject'=>$this->config_basic['name']."-预约/报备-提交",
                                    'name'=>"各位站点管理员及区域管理员",
                                    'utype'=>'用户',
                                    'des'=>"提交了一条预约/报备，请相关管理员尽快处理！",
                                    'aid'=>$aid,
                                ],Station::getAdminBy($station),$this->luser->uemail);
                                $this->successMsg="申请预约/报备成功，联系管理员可快速通过申请！";
                            }else{
                                $this->errMsg="申请该预约/报备失败！";
                            }
                        }else{
                            $this->errMsg="该时间预约/报备已满，请重新选择提交！";
                        }
                    }elseif($appoint->astate==='s'){
                        $this->warnMsg="该预约/报备已申请，请不要重复提交申请！";
                    }elseif($appoint->astate==='f'){
                        $this->infoMsg="该预约/报备已完成，无需申请！";
                    }else{
                        $this->errMsg="该预约/报备已删除，无法申请！";
                    }
                }else{
                    $this->errMsg="您不是该预约/报备的创建者，无法申请！";
                }
            }else{
                $this->errMsg="该预约/报备不存在！";
            }
        }else{
            $this->errMsg="您没有权限申请该预约/报备！";
        }
        $this->getResult();
        return $this->result->toJson();
    }


    //预约/报备修改
    public function alter(Request $request,$aid){
        if($this->luser!==null){
            if(!$this->checkAprocess($this->luser->uid)){
                return $this->result->toJson();
            }
            $appoint=Appoint::where('aid',$aid)->first();
            if($appoint!==null){
                if($appoint->uid===$this->luser->uid){
                    if($appoint->astate==='n'||$appoint->astate==='r'){
                        if(!$this->checklim($appoint)){
                            return $this->result->toJson();
                        }
                        $sid=$appoint->sid;
                        $atype=$request->post("atype",null);
                        $atime=$request->post("atime",null);
                        $ainfo=$request->post('ainfo','{"msg":""}');
                        if(!json_decode($ainfo)){
                            $this->errMsg="信息有误！";
                            $this->getResult();
                            return $this->result->toJson();
                        }else{
                            $ainfo=json_decode($ainfo);
                            if(!$this->checkreport($atype,$ainfo)){
                                return $this->result->toJson();
                            }
                        }
                        if(!$this->check($sid,$atype,$ainfo->msg,$atime)){
                            return $this->result->toJson();
                        }
                        $appoint->uid=$this->luser->uid;
                        $appoint->ainfo=json_encode($ainfo,JSON_UNESCAPED_UNICODE);
                        $appoint->atype=$atype;
                        $appoint->atime=$atime;
                        $appoint->astate='n';
                        if($appoint->update()>0){
                            $this->insertAprocess($appoint->aid,$this->luser->uid,'a',$appoint);
                            Func::sendAppointMail($this->luser,[
                                'subject'=>$this->config_basic['name']."-预约/报备-修改",
                                'name'=>$this->luser->uname,
                                'utype'=>'用户',
                                'des'=>"修改了一条预约/报备！",
                                'aid'=>$aid,
                            ],$this->luser->uemail);
                            $this->successMsg="预约/报备修改成功！";
                        }else{
                            $this->errMsg='预约/报备修改失败！';
                        }
                    }elseif($appoint->astate==='s'){
                        $this->warnMsg="该预约/报备已申请，如需修改，请撤销后再次申请！";
                    }elseif($appoint->astate==='f'){
                        $this->errMsg="该预约/报备已完成，无法修改！";
                    }else{
                        $this->errMsg="该预约/报备已删除，无法修改！";
                    }
                }else{
                    $this->errMsg="您不是该预约/报备的创建者，无法修改！";
                }
            }else{
                $this->errMsg="该预约/报备不存在！";
            }
        }else{
            $this->errMsg="您没有权限修改预约/报备，请重新登录用户！";
        }
        $this->getResult();
        return $this->result->toJson();
    }

    //插入新的预约/报备
    public function insert(Request $request){
        if($this->luser===null){
            $this->errMsg="您没有权限预约/报备，请重新登录用户！";
        }else{
            if(!$this->checkAprocess($this->luser->uid)){
                return $this->result->toJson();
            }
            if(Aprocess::counttotal($appoint->uid)>$this->config_appoint['totallim']){
                $this->errMsg="您今日操作已超出限制！";
                return $this->result->toJson();
            }
            $sid=$request->post("sid",null);
            $atype=$request->post("atype",null);
            $atime=$request->post("atime",null);
            $ainfo=$request->post('ainfo','{"msg":""}');
            if(!json_decode($ainfo)){
                $this->errMsg="信息有误！";
                $this->getResult();
                return $this->result->toJson();
            }else{
                $ainfo=json_decode($ainfo);
                if(!$this->checkreport($atype,$ainfo)){
                    return $this->result->toJson();
                }
            }
            if(!$this->check($sid,$atype,$ainfo->msg,$atime)){
                return $this->result->toJson();
            }
            $appoint=new Appoint();
            $appoint->uid=$this->luser->uid;
            $appoint->sid=$sid;
            $appoint->ainfo=json_encode($ainfo,JSON_UNESCAPED_UNICODE);
            $appoint->atype=$atype;
            $appoint->atime=$atime;
            $appoint->astate='n';
            if($appoint->save()){
                $this->insertAprocess($appoint->aid,$this->luser->uid,'n',$appoint);
                Func::sendAppointMail($this->luser,[
                    'subject'=>$this->config_basic['name']."-预约/报备-添加",
                    'name'=>$this->luser->uname,
                    'utype'=>'用户',
                    'des'=>"新增了一条预约/报备，请尽快提交至管理员审核！",
                    'aid'=>$aid,
                ],$this->luser->uemail);
                $this->successMsg="预约/报备创建成功，请在预约/报备时限前提交申请！";
            }else{
                $this->errMsg='预约/报备创建失败！';
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    private function insertAprocess($aid,$uid,$type,$msg){
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
