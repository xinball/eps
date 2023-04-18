<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\Problem;
use App\Models\Status;
use App\Http\Controllers\Controller;
use App\Library\Func;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Library\JudgeClient;
use Illuminate\Support\Facades\Date;

class StatusController extends Controller
{
    //

    public function indexview(Request $request,$sid){
        if($this->ladmin!==null){
            return view('status.index')->with('stactive',true)->with('result',$this->aget($request,$sid));
        }
        return view('status.index')->with('stactive',true)->with('result',$this->get($request,$sid));
    }
    public function listview(Request $request){
        // if($this->ladmin){
        //     return view('contest.list')->with('cactive',true)->with('result',$this->agetlist($request));
        // }
        return view('status.list')->with('stactive',true);
    }
    public function getlist(Request $request){
        if($this->luser===null){
            $this->errMsg="您没有权限获取提交状态列表！";
            $this->url="/notice";
            $this->getResult();
            return $this->result->toJson();
        }
        $params=$request->all();

        $sql=Status::distinct()->select("sid","scid","status.spid","status.suid","problem.ptitle","user.uname","stype","sresult","screate","slen","slang","stime","sspace","score");
        $sql=$sql->where('stype','=','u');
        $where=[];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="spid"||$key==="scid"){
                    $where[]=[$key,'=',$v];
                }elseif($key==="type"&&in_array($v,$this->config_status['resultkey']['all'])){
                    $where[]=['sresult','=',$v];
                }elseif($key==="slang"&&in_array($v,$this->config_status['langkey'])){
                    $where[]=['slang','=',$v];
                }elseif($key==="stype"&&in_array($v,["a","u"])){
                    $where[]=['stype','=',$v];
                }elseif($key==="suid"){
                    $user=$this->getUserBy($v);
                    if($user!==null)
                        $where[]=['suid','=',$user->uid];
                }elseif($key==="sstart"&&strtotime($v)){
                    $where[]=['screate','>=',$v];
                }elseif($key==="send"&&strtotime($v)){
                    $where[]=['screate','<=',$v];
                }
            }
        }
        $ucids=Contest::getCidsByUid($this->luser->uid,true);
        $sql=$sql->where($where)->orWhere(function ($query) use ($where,$ucids){
            $query->whereIn('scid',$ucids)->where('stype','=','u')->where($where);
        });

        $pids=[];
        $cids=[];
        $pids=Problem::getPidsByUid($this->luser->uid);
        $cids=Contest::getCidsByAuid($this->luser->uid);
        $sql=$sql->orWhere(function ($query) use ($where){
            $query->where('suid','=',$this->luser->uid)->where($where);
        })->orWhere(function ($query) use ($where,$pids){
            $query->whereIn('spid',$pids)->where($where);
        })->orWhere(function ($query) use ($where,$cids){
            $query->whereIn('scid',$cids)->where($where);
        })->join('problem','problem.pid','=','status.spid')->join('user','user.uid','=','status.suid');
        $orderPara = $params['order']??"";
        if($orderPara==="stime"||$orderPara==="screate"||$orderPara==="sspace"||$orderPara==="slen"){
            $sql=$sql->orderBy($orderPara)->orderByDesc('status.sid');
        }elseif($orderPara==="score"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('status.sid');
        }else{
            $sql=$sql->orderByDesc('status.sid');
        }

        $statuses=$sql->paginate($this->config_status['listnum'])->withQueryString();
        $this->listMsg($statuses);
        
        $this->result->data=[
            'statuses'=>$statuses,
            'pids'=>$pids,
            'cids'=>$cids,
            'num'=>$this->getSnums(Status::getNumByUid($this->luser->uid,null)),
            'usnums'=>$this->getSnums(Status::getNumByUid($this->luser->uid,'u')),
            'asnums'=>$this->getSnums(Status::getNumByUid($this->luser->uid,'a')),
        ];
        $this->getResult();
        return $this->result->toJson();

    }
    public function agetlist(Request $request){
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取提交状态列表！";
            $this->url="/notice";
            $this->getResult();
            return $this->result->toJson();
        }
        $params=$request->all();

        $sql=Status::distinct()->select("sid","scid","spid","uname","ptitle","suid","stype","sresult","screate","slen","slang","stime","sspace","score");
        $where=[];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="spid"||$key==="scid"){
                    $where[]=[$key,'=',$v];
                }elseif($key==="type"&&in_array($v,$this->config_status['resultkey']['all'])){
                    $where[]=['sresult','=',$v];
                }elseif($key==="slang"&&in_array($v,$this->config_status['langkey'])){
                    $where[]=['slang','=',$v];
                }elseif($key==="stype"&&in_array($v,["a","u"])){
                    $where[]=['stype','=',$v];
                }elseif($key==="suid"){
                    $user=$this->getUserBy($v);
                    if($user!==null)
                        $where[]=['suid','=',$user->uid];
                }elseif($key==="sstart"&&strtotime($v)){
                    $where[]=['screate','>=',$v];
                }elseif($key==="send"&&strtotime($v)){
                    $where[]=['screate','<=',$v];
                }
            }
        }
        $sql=$sql->where($where)->join('problem','problem.pid','=','status.spid')->join('user','user.uid','=','status.suid');
        $orderPara = $params['order']??"";
        if($orderPara==="stime"||$orderPara==="screate"||$orderPara==="sspace"||$orderPara==="slen"){
            $sql=$sql->orderBy($orderPara)->orderByDesc('status.sid');
        }elseif($orderPara==="score"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('status.sid');
        }else{
            $sql=$sql->orderByDesc('status.sid');
        }

        $statuses=$sql->paginate($this->config_status['listnum'])->withQueryString();
        $this->listMsg($statuses);
        
        $this->result->data=[
            'statuses'=>$statuses,
            'num'=>$this->getSnums(Status::getNum(null)),
            'asnums'=>$this->getSnums(Status::getNum('a')),
            'usnums'=>$this->getSnums(Status::getNum('u')),
        ];
        $this->getResult();
        return $this->result->toJson();
    }
    public function get(Request $request,$sid){
        if (!$this->luser){
            $this->errMsg="您没有权限获取提交状态，请重新登录！";
            $this->url="/notice";
            $this->getResult();
            return $this->result->toJson();
        }
        
        $status=Status::where('sid','=',$sid)->join('user','user.uid','=','status.suid')->leftjoin('contest','contest.cid','=','status.scid',"score")->join('problem','problem.pid','=','status.spid')->first();
        if($status!==null){
            if($status->suid!==$this->luser->uid){
                $problem=Problem::where('pid',$status->spid)->first();
                if($problem->puid!==$this->luser->uid){
                    if($status->scid===null||!in_array($status->scid,array(Contest::getCidsByAuid($this->luser->uid)))){
                        if($status->stype==='u'){
                            $this->successMsg="您是普通用户，无法查看他人编写的代码与样例详细信息";
                            unset($status->scode);
                            unset($status->sinfo);
                            unset($status->coption);
                        }else{
                            $this->url="/status";
                            $this->errMsg="您没有权限查看该状态！";
                            $this->getResult();
                            return $this->result->toJson();
                        }
                    }else{
                        $this->successMsg="您是比赛组织者或问题创建者，有权查看他人提交代码";
                    }
                }else{
                    $this->successMsg="正在查看您提交的代码";
                }
            }
            if($status->scid!==null){
                $status->pids=Problem::getPidsByCid($status->scid);
            }
            if(isset($status->sinfo))
                $status->sinfo=json_decode($status->sinfo,true);
            $this->result->data=[
                'status'=>$status,
            ];
        }else{
            $this->url="/status";
            $this->errMsg="该提交状态不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function aget(Request $request,$sid){
        if ($this->ladmin===null){
            $this->errMsg="您没有权限获取提交状态，请重新登录！";
            $this->url="/notice";
            $this->getResult();
            return $this->result->toJson();
        }        
        $status=Status::where('sid',$sid)->join('user','user.uid','=','status.suid')->leftjoin('contest','contest.cid','=','status.scid')->join('problem','problem.pid','=','status.spid')->first();
        if($status!==null){
            $this->successMsg="您是管理员，可直接查看他人提交代码";
            if($status->scid!==null){
                $status->pids=Problem::getPidsByCid($status->scid);
            }
            $status->sinfo=json_decode($status->sinfo);
            $this->result->data=[
                'status'=>$status,
            ];
        }else{
            $this->url="/status";
            $this->errMsg="该提交状态不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }

    public function insert(Request $request){
        $pid=$request->post('pid',null);
        $problem=Problem::where('pid',$pid)->first();
        $cid=$request->post('cid',null);
        $scode=$request->post('scode',null);
        $slang=$request->post('slang',null);
        if(!in_array($slang,$this->config_status['langkey'])){
            $this->errMsg="编译语言选择有误！";
        }elseif($scode===null|Func::Length($scode)>50000){
            $this->errMsg="代码为空或长度过大！";
        }elseif($problem===null){
            $this->errMsg="该问题不存在！";
        }elseif($problem->ptype==='d'){
            $this->errMsg="该问题已被删除！";
        }
        if($this->errMsg!==null){
            $this->getResult();
            return $this->result->toJson();
        }
        $status=new Status();
        $status->screate=date('Y-m-d H:i:s');
        $status->spid=$pid;
        $status->sinfo='[]';
        $status->sresult='p';
        $status->scode=$scode;
        $status->slang=$slang;
        $status->stime=5000;
        $status->sspace=256*1024*1024;
        $status->spid=$pid;
        $status->score=0;
        $status->scid=$cid;
        $status->slen=Func::Length($scode);
        $contest=Contest::where('cid',$cid)->first();
        if($contest!==null){
            $this->getContest($contest);
        }
        if($this->luser){
            $pnums=$this->getSnums(Status::getNumByUidAndPid($this->luser->uid,$pid));
            $cnums=$this->getSnums(Status::getNumByUidAndPidAndCid($this->luser->uid,$pid,$cid));
        }
        if($this->ladmin!==null){
            $this->successMsg="您已登录管理员身份，将以管理员身份进行测试";
            $status->stype='a';
            $status->suid=$this->ladmin->uid;
            $status->scid=null;
        }elseif ($this->luser===null){
            $this->errMsg="您没有登录用户或管理员，无权进行评测！";
        }elseif ($problem->ptype==='h'){
            $this->errMsg="该问题已隐藏，普通用户无法进行评测！";
        }elseif($problem->puid===$this->luser->uid){
            $this->successMsg="您是该问题的创建者，可直接进行测试";
            $status->stype='a';
            $status->suid=$this->luser->uid;
            $status->scid=null;
        }elseif(isset($pnums)&&$pnums['sum']>$this->config_problem['numlimit']){
            $this->errMsg="您的提交次数已超限制，无法进行评测！";
        }elseif($problem->ptype==='o'){
            $this->successMsg="正在进行评测";
            $status->stype='u';
            $status->suid=$this->luser->uid;
            $status->scid=null;
        }elseif($contest===null){
            $this->errMsg="问题所属比赛不存在！";
        }elseif($contest->ctype==='d'){
            $this->errMsg="问题所属比赛已删除！";
        }elseif(DB::table('admin_contest')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()){
            $this->successMsg="您是该比赛的管理员，可直接进行测试";
            $status->stype='a';
            $status->suid=$this->luser->uid;
        }elseif(strtotime($contest->cstart)>time()||strtotime($contest->cend)<time()){
            $this->errMsg="该比赛不在进行中，无法查看比赛问题！";
        }elseif(isset($cnums)&&$contest->coption->numlimit>0&&$cnums['sum']>$contest->coption->numlimit){
            $this->errMsg="您的提交次数已超该比赛限制提交次数，无法进行评测！";
        }elseif(DB::table('contest_user')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()){
            $this->successMsg="正在进行评测";
            $status->stype='u';
            $status->suid=$this->luser->uid;
        }else{
            $this->errMsg="您没有权限评测该比赛问题！";
        }
        if($this->errMsg===null){
            if($status->save()){
                $format="";
                // switch($status->slang){
                //     case 'c':$format=".c";break;
                //     case 'd':$format=".cpp";break;
                //     case 'p':$format=".py";break;
                //     case 'q':$format=".py";break;
                //     case 'j':$format=".java";break;
                //     case 'k':$format=".js";break;
                //     case 'h':$format=".php";break;
                // }
                $fp=fopen(storage_path('/app/status/'.$status->sid.$format),'w');
                fwrite($fp,$status->scode);
                fclose($fp);
                Redis::LPUSH('judge1',$status->sid);
                file_get_contents(config('var.jj').'1/'.'0');
                // app()->call('App\Http\Controllers\JudgeController@judge');
            }
            sleep(1);
            $this->result->data=[
                'status'=>Status::where('sid',$status->sid)->first(),
            ];
        }
        $this->getResult();
        return $this->result->toJson();
    }
}
