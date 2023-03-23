<?php
/*
 * @Author: your name
 * @Date: 2022-03-02 08:40:39
 * @LastEditTime: 2022-05-29 21:46:45
 * @LastEditors: XinBall
 * @Description: 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 * @FilePath: /oj/app/Http/Controllers/Service/ContestController.php
 */

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\Problem;
use App\Models\Status;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Library\CropAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Library\Func;
use DB;
use Mews\Purifier\Facades\Purifier;

class ContestController extends Controller
{
    public function indexview(Request $request,$cid){
        if($this->ladmin!==null){
            return view('contest.index')->with('cactive',true)->with('result',$this->aget($cid));
        }
        return view('contest.index')->with('cactive',true)->with('result',$this->get($cid));
    }
    public function listview(Request $request){
        // if($this->ladmin){
        //     return view('contest.list')->with('cactive',true)->with('result',$this->agetlist($request));
        // }
        return view('contest.list')->with('cactive',true);
    }
    public function get($cid){
        $this->url="/contest";
        if (!$this->luser){
            $this->errMsg="您没有权限获取比赛信息，请重新登录！";
            $this->getResult();
            return $this->result->toJson();
        }
        $contest=Contest::where('cid',$cid)->first();
        if($contest===null){
            $this->errMsg="该比赛不存在！";
            $this->getResult();
            return $this->result->toJson();
        }
        if($contest->ctype==='d'){
            $this->errMsg="该比赛已删除！";
            $this->getResult();
            return $this->result->toJson();
        }
        $self=DB::table('admin_contest')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists();
        $this->getContest($contest,!$self);
        if($self||DB::table('contest_user')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()||($contest->ctype==='o'&&(!isset($contest->coption->pwd)||$contest->coption->pwd===null))){
            $contest->self=$self;
            $contest->snums=$this->getSnums(Status::getNumByCid($contest->cid));
            $contest->pids=Problem::getPidsByCid($cid);
            $problems=Problem::getPtitleByCid($cid);
            if($this->luser){
                foreach($problems as $v=>$problem){
                    $problem->status=Status::getSolve($this->luser->uid,$contest->pids[$v],$cid);
                }
            }
            $contest->problems=$problems;
            $contest->auids=User::getAuidsByCid($cid);
            $contest->ausers=User::getAunameByCid($cid);
            $this->successMsg="";
            $this->result->data=[
                'contest'=>$contest,
            ];
            $this->url=null;
        }else{
            $this->errMsg="您没有权限查看该比赛！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function aget($cid){
        $this->url="/contest";
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取该比赛信息！";
        }else{
            $contest=Contest::where('cid',$cid)->first();
            if($contest!==null){
                if($contest->ptype!=='d'){
                    $this->getContest($contest);
                    $contest->snums=$this->getSnums(Status::getNumByCid($contest->cid));
                    $contest->pids=Problem::getPidsByCid($cid);
                    $contest->problems=Problem::getPtitleByCid($cid);
                    $contest->auids=User::getAuidsByCid($cid);
                    $contest->ausers=User::getAunameByCid($cid);
                    
                    $this->successMsg="";
                    $this->result->data=[
                        'contest'=>$contest
                    ];
                    $this->url=null;
                }else{
                    $this->errMsg="该比赛已删除！";
                }
            }else{
                $this->errMsg="该比赛不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function getlist(Request $request){
        $params=$request->all();

        $sql=Contest::distinct()->select("cid","ctitle","cdes","ctype","cstart","cend","cnum","coption");

        //条件筛选
        $where[]=['ctype','!=','d'];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="cdes"||$key==="ctitle"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }elseif($key==="cstart"&&strtotime($v)){
                    $where[]=['cstart','>=',$v];
                }elseif($key==="cend"&&strtotime($v)){
                    $where[]=['cend','<=',$v];
                }elseif($key==="type"&&in_array($v,$this->config_contest['typekey']['all'])){
                    $where[]=['ctype','=',$v];
                }elseif($key==="rule"&&in_array($v,['a','i'])){
                    $where[]=['coption->rule','=',$v];
                }elseif(($key==="pwd"||$key==="rtrank"||$key==="numlimit"||$key==="punish")&&in_array($v,['true','false'])){
                    $where[]=['coption->'.$key,$v==='true'?'!=':'=',null];
                }
            }
        }
        $uflag=null;
        if(isset($params['u'])&&($params['u']==='j'||$params['u']==='i'))
            $uflag=$params['u'];
        if($uflag===null||$this->luser===null)
            $sql=$sql->where('ctype','=','o')->where($where);

        //用户登录则筛选用户参加和管理的比赛
        if($this->luser!==null) {
            if($uflag===null){
                $sql = $sql->orWhereExists(function ($query) use ($where) {
                    $query->select('cid')
                        ->from('contest_user')
                        ->whereColumn('contest_user.cid', 'contest.cid')
                        ->where('uid', $this->luser->uid)
                        ->where('ctype', 's')
                        ->where($where);
                })->orWhereExists(function ($query) use ($where) {
                    $query->select('cid')
                        ->from('admin_contest')
                        ->whereColumn('admin_contest.cid', 'contest.cid')
                        ->where('uid', $this->luser->uid)
                        ->where($where);
                });
            }elseif($uflag==='j'){
                $sql = $sql->WhereExists(function ($query) use ($where) {
                    $query->select('cid')
                        ->from('contest_user')
                        ->whereColumn('contest_user.cid', 'contest.cid')
                        ->where('uid', $this->luser->uid)
                        ->whereIn('ctype', $this->config_contest['typekey']['a'])
                        ->where($where);
                });
                $this->result->data=['num'=>$this->getTypeNum("j")];
            }elseif($uflag==='i'){
                $sql = $sql->WhereExists(function ($query) use ($where) {
                    $query->select('cid')
                        ->from('admin_contest')
                        ->whereColumn('admin_contest.cid', 'contest.cid')
                        ->where('uid', $this->luser->uid)
                        ->where($where);
                });
                $this->result->data=['num'=>$this->getTypeNum("i")];
            }
        }
        // echo $sql->toSql();

        //排序 开始时间倒序 编号倒序
        $orderPara = $params['order']??"";
        if($orderPara==="cstart"||$orderPara==="cnum"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('contest.cid');
        }else{
            $sql=$sql->orderByDesc('contest.cid');
        }

        //分页
        $contests=$sql->paginate($this->config_contest['listnum'])->withQueryString();
        $this->listMsg($contests);
        //获取比赛图片
        foreach ($contests as $contest){
            $this->getContest($contest,true);
        }
        //获取每种比赛的数量
        $this->result->data['contests']=$contests;
        
        
        $this->getResult();
        return $this->result->toJson();
    }
    public function agetlist(Request $request){
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取比赛列表！";
            $this->getResult();
            return $this->result->toJson();
        }
        
        $params=$request->all();

        $sql=Contest::distinct()->select("cid","ctitle","cdes","ctype","cstart","cend","cnum","coption");
        //条件筛选
        $where=[];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                // if($key==="page"||$key==="order"||trim($v)==="")
                //     continue;
                if($key==="cdes"||$key==="ctitle"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }elseif($key==="cstart"&&strtotime($v)){
                    $where[]=['cstart','>=',$v];
                }elseif($key==="cend"&&strtotime($v)){
                    $where[]=['cend','<=',$v];
                }elseif($key==="type"&&in_array($v,$this->config_contest['typekey']['total'])){
                    $where[]=['ctype','=',$v];
                }elseif($key==="rule"&&in_array($v,['a','i'])){
                    $where[]=['coption->rule','=',$v];
                }elseif(($key==="pwd"||$key==="rtrank"||$key==="numlimit"||$key==="punish")&&in_array($v,['true','false'])){
                    $where[]=['coption->'.$key,$v==='true'?'!=':'=',null];
                }
                // else{
                //     $where[$key]=$v;
                // }
                // view()->share($key,$v);
            }
        }
        $sql=$sql->where($where);

        //排序 开始时间倒序 编号倒序
        $orderPara = $params['order']??"";
        if($orderPara==="cstart"||$orderPara==="cnum"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('contest.cid');
        }else{
            $sql=$sql->orderByDesc('contest.cid');
        }

        //分页
        $contests=$sql->paginate($this->config_contest['listnum'])->withQueryString();
        $this->listMsg($contests);
        //获取比赛图片
        foreach ($contests as $contest){
            $this->getContest($contest);
        }
        //获取每种比赛的数量
        $this->result->data=[
            'contests'=>$contests,
            'num'=>$this->getTypeNum('a'),
        ];
        view()->share("contests",$contests);
        $this->getResult();
        return $this->result->toJson();
    }
    public function join(Request $request,$cid){
        $this->url="/contest";
        $contest=Contest::where('cid',$cid)->first();
        if($contest===null){
            $this->errMsg="该比赛不存在！";
            $this->getResult();
            return $this->result->toJson();
        }
        if($contest->ctype==='d'){
            $this->errMsg="该比赛已删除！";
            $this->getResult();
            return $this->result->toJson();
        }
        if($this->ladmin!==null){
            $this->successMsg="您已登录管理员身份，可查看比赛或进行测试";
            $this->url="/contest/".$cid;
            $this->getResult();
            return $this->result->toJson();
        }
        if ($this->luser===null){
            $this->errMsg="您没有登录用户或管理员，无权加入比赛！";
            $this->getResult();
            return $this->result->toJson();
        }
        $this->getContest($contest);
        if(DB::table('admin_contest')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()){
            $this->successMsg="您是该比赛的管理员，可查看比赛或进行测试";
            $this->url="/contest/".$cid;
        }elseif(DB::table('contest_user')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()){
            $this->successMsg="您已参加该比赛！";
            $this->url="/contest/".$cid;
        }elseif($contest->ctype==='o'&&(!isset($contest->coption->pwd)||$contest->coption->pwd===false)){
            $this->successMsg="您已参加该公开比赛！";
            $this->url="/contest/".$cid;
            DB::table('contest_user')->insert(['uid'=>$this->luser->uid,'cid'=>$cid,'cutype'=>'r']);
        }elseif($contest->ctype==='o'){
            if($contest->coption->pwd===($request->get('pwd',""))){
                $this->successMsg="您已参加该公开比赛！";
                $this->url="/contest/".$cid;
                DB::table('contest_user')->insert(['uid'=>$this->luser->uid,'cid'=>$cid,'cutype'=>'r']);
            }else{
                $this->errMsg="参加比赛失败，比赛密码错误！";
            }
        }elseif($contest->ctype==='s'){
            $this->errMsg="您不可参加私有比赛！";
        }else{
            $this->errMsg="您没有权限查看该比赛！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function insert(Request $request){
        $utype=$request->post("utype",null);
        $ctype=$request->post("ctype",null);
        $cinfo=$request->post('cinfo',null);
        $uid="";
        if($utype==="a"){
            if ($this->ladmin===null){
                $this->errMsg="您不是管理员，没有权限添加比赛！请以普通用户身份创建并申请比赛";
            }elseif(!in_array($ctype,$this->config_contest['typekey']['a'])){
                $this->errMsg="比赛类型有误！请重新申请";
            }else{
                $uid=$this->ladmin->uid;
            }
        }else{
            if($this->luser===null){
                $this->errMsg="您没有权限创建比赛，请重新登录！";
            }elseif(!in_array($ctype,$this->config_contest['typekey']['u'])&&!in_array($ctype,$this->config_contest['typekey']['b'])){
                $this->errMsg="比赛类型有误！普通用户无法上线比赛！";
            }else{
                $cinfo=Purifier::clean($cinfo);
                $uid=$this->luser->uid;
            }
        }
        if($this->errMsg!==null){
            $this->getResult();
            return $this->result->toJson();
        }
        
        $coption=null;
        $ctitle=$request->post('ctitle',null);
        $cdes=$request->post('cdes',null);
        $cstart=$request->post('cstart',null);
        $cstartval=strtotime($cstart);
        $cend=$request->post('cend',null);
        $cendval=strtotime($cend);
        $ips=$request->post('ips',null);
        $pwd=$request->post('pwd',null);
        $rule=$request->post('rule',null);
        $rtrank=$request->post('rtrank',null);
        $punish=$request->post('punish',null);
        $numlimit=$request->post('numlimit',null);
        $userlist=[];
        if($this->check($ctitle,$cdes,$cinfo,$cstartval,$cendval,$ips,$pwd,$rule,$rtrank,$punish,$numlimit)){
            if(in_array($ctype,$this->config_contest['typekey']['s'])){
                $usersource=$request->post('usersource','');
                if($usersource==='c'&&$utype==='a'){
                    $prefix=$request->post('prefix','');
                    $startnum=$request->post('startnum','');
                    $endnum=$request->post('endnum','');
                    if(!Func::isPrefix($prefix)){
                        $this->errMsg="用户前缀格式不合规范【3-6位英文或数字】！";
                    }elseif(!Func::isNum($startnum,1,9-Func::Length($prefix))||!Func::isNum($endnum,1,9-Func::Length($prefix))){
                        $this->errMsg="用户起始或结束编号格式不合规范！";
                    }elseif($startnum>$endnum){
                        $this->errMsg="用户起始编号不得大于结束编号！";
                    }else{
                        // TODO: 批量创建用户
                    }
                }elseif($usersource==='f'){
                    $userlist=json_decode($request->post('userlist','[]'),true);
                }else{
                    $this->errMsg="普通用户创建的比赛不支持批量创建用户哟！";
                }
                $coption['pwd']=null;
            }else{
                $coption['pwd']=$pwd;

            }
        }
        
        if($this->errMsg!==null){
            $this->getResult();
            return $this->result->toJson();
        }

        $coption['ips']=json_decode($ips,true);
        $coption['rule']=$rule;
        if($rule==='a'){
            $coption['punish']=$punish;
        }else{
            $coption['punish']=null;
        }
        $coption['rtrank']=$rtrank;
        $coption['numlimit']=$numlimit;

        $contest=new Contest();
        $contest->ctitle=$ctitle;
        $contest->cdes=$cdes;
        $contest->ctype=$ctype;
        $contest->cstart=$cstart;
        $contest->cend=$cend;
        $contest->cinfo=$cinfo;
        $contest->coption=json_encode($coption,JSON_UNESCAPED_UNICODE);
        if($contest->save()){
            $this->successMsg="添加比赛成功！";
            DB::table('admin_contest')->insert(['uid'=>$uid,'cid'=>$contest->cid]);
            foreach($userlist as $uid){
                $user=User::where('uid',$uid)->first();
                if($user!==null&&$user->utype!=='s'&&!DB::table('admin_contest')->where(['uid'=>$uid,'cid'=>$contest->cid])->exists()&&!DB::table('contest_user')->where(['uid'=>$uid,'cid'=>$contest->cid])->exists()){
                    DB::table('contest_user')->insert(['uid'=>$uid,'cid'=>$contest->cid,'cutype'=>'p']);
                }
            }
            $this->setproblem($request,$contest->cid);
            // $this->setadmin($request,$contest->cid);
        }else{
            $this->errMsg='添加比赛失败！';
        }
        
        $this->getResult();
        return $this->result->toJson();
    }

    public function getTypeNum($utype="a"){
        if($utype=="a"){
            $sql = Contest::select('ctype',DB::raw("count('cid') as num"))->groupBy('ctype');
            $typekey='total';
        }elseif($utype=="i"){
            $sql=Contest::select('ctype',DB::raw("count('cid') as num"))->WhereExists(
            function ($query) {
                $query->select('cid')
                    ->from('admin_contest')
                    ->whereColumn('admin_contest.cid', 'contest.cid')
                    ->where('uid','=', $this->luser->uid);
            })->groupBy('ctype');
            $typekey='all';
        }elseif($utype=="j"){
            $sql = Contest::select('ctype',DB::raw("count('cid') as num"))->WhereExists(
            function ($query) {
                $query->select('cid')
                    ->from('contest_user')
                    ->whereColumn('contest_user.cid', 'contest.cid')
                    ->where('uid','=', $this->luser->uid)
                    ->whereIn('ctype', $this->config_contest['typekey']['a']);
            })->groupBy('ctype');
            $typekey='a';
        }
        // echo $sql->toSql();
        $nums = $sql->get();
        $typenum=[
            'sum'=>0,
        ];
        foreach($nums as $num){
            $typenum[$num->ctype]=$num->num;
        }
        foreach($this->config_contest['typekey'][$typekey] as $type){
            if(!isset($typenum[$type])){
                $typenum[$type]=0;
            }
            $typenum['sum']+=$typenum[$type];
        }
        return $typenum;
    }
    public function check($ctitle,$cdes,$cinfo,$cstartval,$cendval,$ips,$pwd,$rule,$rtrank,$punish,$numlimit){
        if($ctitle===null||Func::Length($ctitle)>50){
            $this->errMsg="比赛标题格式不合规范【长度不得大于50】！";
        }elseif($cdes===null||Func::Length($cdes)>50){
            $this->errMsg="比赛描述格式不合规范【长度不得大于100】！";
        }elseif($cinfo===null||Func::Length($cinfo)>100000){
            $this->errMsg="比赛详细描述格式不合规范【长度不得大于100000】！";
        }elseif($pwd!==null&&!Func::isPwd($pwd)){
            $this->errMsg="比赛密码格式不合规范！";
        }elseif(!in_array($rule,['a','i'])){
            $this->errMsg="比赛规则选择有误！";
        }elseif($rule==='a'&&$punish!==null&&!Func::isNum($punish,1)){
            $this->errMsg="比赛罚时格式有误！";
        }elseif($numlimit!==null&&!Func::isNum($numlimit,1)){
            $this->errMsg="比赛提交次数限制格式有误！";
        }elseif($rtrank===true||$rtrank===false){
            $this->errMsg="比赛提是否显示实时排名格式有误！";
        }elseif($cstartval===null||$cendval===null){
            $this->errMsg="比赛时间格式有误！";
        }elseif($cendval-$cstartval<60){
            $this->errMsg="比赛时间最短不低于1分钟！";
        }elseif($ips!=='[]'&&!json_decode($ips,true)){
            $this->errMsg="IP地址格式不合规范！";
        }
        $ips=json_decode($ips,true);
        foreach($ips as $ip){
            if(!isset($ip['start'])||!isset($ip['end'])){
                $this->errMsg="IP地址格式不合规范！";
                break;
            }
        }
        if($this->errMsg!==null){
            return false;
        }
        return true;
    }

    public function del($cid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限删除该比赛！";
        }else{
            $contest=Contest::where('cid',$cid)->first();
            if($contest!==null){
                if(in_array($contest->ctype,$this->config_contest['typekey']['a'])){
                    $contest->ctype='d';
                    if($contest->update()>0){
                        $this->successMsg="删除该比赛成功！";
                    }else{
                        $this->errMsg="删除该比赛失败！";
                    }
                }elseif($contest->ctype==='d'){
                    $this->errMsg="该比赛已删除，无需再次删除！";
                }else{
                    $this->errMsg="该比赛未通过申请，无法删除！";
                }
            }else{
                $this->errMsg="该比赛不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function recover($cid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限恢复该比赛！";
        }else{
            $contest=Contest::where('cid',$cid)->first();
            if($contest!==null){
                if($contest->ctype==='d'){
                    $contest->ctype='c';
                    if($contest->update()>0){
                        $this->successMsg="恢复该比赛成功！";
                    }else{
                        $this->errMsg="恢复该比赛失败！";
                    }
                }else{
                    $this->errMsg="该比赛未被删除，无需恢复！";
                }
            }else{
                $this->errMsg="该比赛不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    
    public function refuse($cid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限拒绝通过或下线该比赛！";
        }else{
            $contest=Contest::where('cid',$cid)->first();
            if($contest!==null){
                if(in_array($contest->ctype,$this->config_contest['typekey']['b'])||in_array($contest->ctype,$this->config_contest['typekey']['a'])){
                    $msg=in_array($contest->ctype,$this->config_contest['typekey']['b'])?"拒绝通过":"下线";
                    $contest->ctype=(in_array($contest->ctype,$this->config_contest['typekey']['o'])?'c':'e');
                    if($contest->update()>0){
                        $this->successMsg=$msg."该比赛成功！";
                    }else{
                        $this->errMsg=$msg."该比赛失败！";
                    }
                }elseif($contest->ctype==='d'){
                    $this->errMsg="该比赛已删除，无法拒绝通过或下线！";
                }else{
                    $this->errMsg="该比赛未申请，无法拒绝通过或下线！";
                }
            }else{
                $this->errMsg="该比赛不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function approve($cid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限通过该比赛！";
        }else{
            $contest=Contest::where('cid',$cid)->first();
            if($contest!==null){
                if(in_array($contest->ctype,$this->config_contest['typekey']['b'])){
                    $contest->ctype=($contest->ctype==='a'?'o':'s');
                    if($contest->update()>0){
                        $this->successMsg="通过该比赛成功！";
                    }else{
                        $this->errMsg="通过该比赛失败！";
                    }
                }elseif(in_array($contest->ctype,$this->config_contest['typekey']['a'])){
                    $this->errMsg="该比赛已申请已通过，无需再次通过！";
                }elseif($contest->ctype==='d'){
                    $this->errMsg="该比赛已删除，无法通过！";
                }else{
                    $this->errMsg="该比赛未申请，无法通过！";
                }
            }else{
                $this->errMsg="该比赛不存在！";
            }
        }

        $this->getResult();
        return $this->result->toJson();
    }

    public function cancel($cid){
        $contest=Contest::where('cid',$cid)->first();
        if($contest!==null){
            if($this->luser!==null&&DB::table('admin_contest')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()){
                if(in_array($contest->ctype,$this->config_contest['typekey']['b'])||in_array($contest->ctype,$this->config_contest['typekey']['a'])){
                    $contest->ctype=(($contest->ctype==='o'||$contest->ctype==='a')?'c':'e');
                    if($contest->update()>0){
                        $this->successMsg="撤销该比赛成功！";
                    }else{
                        $this->errMsg="撤销该比赛失败！";
                    }
                }elseif($contest->ctype==='d'){
                    $this->errMsg="该比赛已删除，无法撤销！";
                }else{
                    $this->errMsg="该比赛未申请，无需撤销！";
                }
            }else{
                $this->errMsg="您没有权限撤销该比赛！";
            }
        }else{
            $this->errMsg="该比赛不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function apply($cid){
        $contest=Contest::where('cid',$cid)->first();
        if($contest!==null){
            if($this->luser!==null&&DB::table('admin_contest')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()){
                if(in_array($contest->ctype,$this->config_contest['typekey']['u'])){
                    $contest->ctype=($contest->ctype==='c'?'a':'b');
                    if($contest->update()>0){
                        $this->successMsg="申请比赛成功，联系管理员可快速通过申请！";
                    }else{
                        $this->errMsg="申请该比赛失败！";
                    }
                }elseif(in_array($contest->ctype,$this->config_contest['typekey']['b'])){
                    $this->errMsg="已申请该比赛，请不要重复提交申请；如需修改比赛信息，请撤销后再次申请！";
                }elseif(in_array($contest->ctype,$this->config_contest['typekey']['a'])){
                    $this->errMsg="该比赛已通过申请，无需再次申请；如需修改比赛信息，请撤销后再次申请！";
                }else{
                    $this->errMsg="该比赛已删除，无法申请，如需恢复该比赛，请联系管理员！";
                }
            }else{
                $this->errMsg="您没有权限申请该比赛！";
            }
        }else{
            $this->errMsg="该比赛不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function alter(Request $request,$cid){
        $utype=$request->post("utype",'');
        $ctype=$request->post("ctype",'');
        $cinfo=$request->post('cinfo','');
        $contest=Contest::where('cid',$cid)->first();
        if($contest!==null){
            if($utype==="a"){
                if ($this->ladmin===null){
                    $this->errMsg="您不是管理员，没有权限修改比赛！";
                }elseif($contest->ctype==="d"){
                    $this->errMsg="该比赛已删除，请恢复后修改信息！";
                }
            }else{
                if($this->luser===null||!DB::table('admin_contest')->where('cid','=',$cid)->where('uid','=',$this->luser->uid)->exists()){
                    $this->errMsg="您没有权限修改该比赛信息！";
                }elseif(!in_array($contest->ctype,$this->config_contest['typekey']['u'])){
                    $this->errMsg="比赛信息在未申请状态时才可修改！";
                }elseif(!in_array($ctype,$this->config_contest['typekey']['u'])&&!in_array($ctype,$this->config_contest['typekey']['b'])){
                    $this->errMsg="比赛类型有误！普通用户无法上线比赛";
                }else{
                    $cinfo=Purifier::clean($cinfo);
                }
            }

            if($this->errMsg!==null){
                $this->getResult();
                return $this->result->toJson();
            }

            $coption=null;
            $userlist=[];
            $ctitle=$request->post('ctitle',null);
            $cdes=$request->post('cdes',null);
            $cstart=$request->post('cstart',null);
            $cstartval=strtotime($cstart);
            $cend=$request->post('cend',null);
            $cendval=strtotime($cend);
            $ips=$request->post('ips',null);
            $pwd=$request->post('pwd',null);
            $rule=$request->post('rule',null);
            $rtrank=$request->post('rtrank',null);
            $punish=$request->post('punish',null);
            $numlimit=$request->post('numlimit',null);
            if($this->check($ctitle,$cdes,$cinfo,$cstartval,$cendval,$ips,$pwd,$rule,$rtrank,$punish,$numlimit)){
                if(in_array($ctype,$this->config_contest['typekey']['s'])){
                    $usersource=$request->post('usersource','');
                    if($usersource==='c'&&$utype==='a'){
                        $prefix=$request->post('prefix','');
                        $startnum=$request->post('startnum','');
                        $endnum=$request->post('endnum','');
                        if(!Func::isPrefix($prefix)){
                            $this->errMsg="用户前缀格式不合规范【3-6位英文或数字】！";
                        }elseif(!Func::isNum($startnum,1,9-Func::Length($prefix))||!Func::isNum($endnum,1,9-Func::Length($prefix))){
                            $this->errMsg="用户起始或结束编号格式不合规范！";
                        }elseif($startnum>$endnum){
                            $this->errMsg="用户起始编号不得大于结束编号！";
                        }else{
                            // TODO: 批量创建用户
                        }
                    }elseif($usersource==='f'){
                        $userlist=json_decode($request->post('userlist','[]'),true);
                    }else{
                        $this->errMsg="普通用户创建的比赛不支持批量创建用户哟！";
                    }
                    $coption['pwd']=null;
                }else{
                    $coption['pwd']=$pwd;
                }
            }else{
                $this->getResult();
                return $this->result->toJson();
            }
    
            $coption['ips']=json_decode($ips,true);
            $coption['rule']=$rule;
            if($rule==='a'){
                $coption['punish']=$punish;
            }else{
                $coption['punish']=null;
            }
            $coption['rtrank']=$rtrank;
            $coption['numlimit']=$numlimit;
            $contest->ctitle=$ctitle;
            $contest->cdes=$cdes;
            $contest->ctype=$ctype;
            $contest->cstart=$cstart;
            $contest->cend=$cend;
            $contest->cinfo=$cinfo;
            $contest->coption=json_encode($coption,JSON_UNESCAPED_UNICODE);
            if($contest->update()){
                $this->successMsg="修改比赛成功！";
                if($userlist!==null){
                    foreach($userlist as $uid){
                        $user=User::where('uid',$uid)->first();
                        if($user!==null&&$user->utype!=='s'&&!DB::table('admin_contest')->where(['uid'=>$uid,'cid'=>$contest->cid])->exists()&&!DB::table('contest_user')->where(['uid'=>$uid,'cid'=>$contest->cid])->exists()){
                            DB::table('contest_user')->insert(['uid'=>$uid,'cid'=>$contest->cid,'cutype'=>'p']);
                        }
                    }
                }
                $this->setproblem($request,$contest->cid);
                $this->setadmin($request,$contest->cid);
            }else{
                $this->errMsg='修改比赛失败！';
            }
            

        }else{
            $this->errMsg="该比赛不存在！";
        }
        $this->getResult();
        return $this->result->toJson();

        
    }
    public function setproblem(Request $request,$cid){
        $pids=$request->post("pids",'');
        if($pids!=="[]"&&!json_decode($pids,true)){
            $this->errMsg="添加的问题数据格式不合规范！";
        }else{
            $pids=array_unique(json_decode($pids,true),SORT_NUMERIC);
            $contest=Contest::where('cid',$cid)->first();
            $utype=$request->post("utype",'');
            if($contest!==null){
                $prepids=Problem::getPidsByCid($cid);
                if($utype==="a"){
                    if ($this->ladmin===null){
                        $this->errMsg="您不是管理员，没有权限设置比赛问题！";
                    }elseif($contest->ctype==="d"){
                        $this->errMsg="该比赛已删除，请恢复后设置问题！";
                    }else{
                        foreach($pids as $key=>$pid){
                            $problem=Problem::where('pid',$pid)->first();
                            if(in_array($pid,$prepids)){
                                DB::table('contest_problem')->where('pid','=',$pid)->update(['ordernum'=>$key]);
                                array_splice($prepids,array_search($pid,$prepids),1);
                            }elseif($problem!==null&&$problem->ptype==='m'){
                                DB::table('contest_problem')->insert(['pid'=>$pid,'cid'=>$cid,'ordernum'=>$key]);
                            }
                        }
                        foreach($prepids as $pid){
                            DB::table('contest_problem')->where('cid','=',$cid)->where('pid','=',$pid)->delete();
                        }
                    }
                }else{
                    if($this->luser===null||!DB::table('admin_contest')->where('cid','=',$cid)->where('uid','=',$this->luser->uid)->exists()){
                        $this->errMsg="您没有权限为该比赛设置问题！";
                    }elseif(!in_array($contest->ctype,$this->config_contest['typekey']['u'])){
                        $this->errMsg="比赛信息在未申请状态时才可设置题目！";
                    }else{
                        foreach($pids as $key=>$pid){
                            $problem=Problem::where('pid',$pid)->first();
                            if($problem===null||$problem->ptype!=='m')
                                continue;
                            if(in_array($pid,$prepids)){
                                DB::table('contest_problem')->where('pid','=',$pid)->update(['ordernum'=>$key]);
                                array_splice($prepids,array_search($pid,$prepids),1);
                            }elseif($problem->puid!==$this->luser->uid){
                                $this->errMsg="普通用户只能为比赛添加本人创建的问题哟！";
                            }else{
                                DB::table('contest_problem')->insert(['pid'=>$pid,'cid'=>$cid,'ordernum'=>$key]);
                            }
                        }
                        foreach($prepids as $pid){
                            DB::table('contest_problem')->where('cid','=',$cid)->where('pid','=',$pid)->delete();
                        }
                    }
                }
            }else{
                $this->errMsg="该比赛不存在！";
            }
        }
    }
    
    public function setadmin(Request $request,$cid){
        $auids=$request->post("auids",'');
        if($auids!=="[]"&&!json_decode($auids,true)){
            $this->errMsg="添加的管理员数据格式不合规范！";
        }else{
            $contest=Contest::where('cid',$cid)->first();
            $utype=$request->post("utype",'');
            if($contest!==null){
                $preuids=User::getAuidsByCid($cid);
                if($contest->ctype==="d"){
                    $this->errMsg="该比赛已删除，请恢复后设置管理员！";
                }elseif (($utype==='a'&&$this->ladmin===null)||($utype!=='a'&&($this->luser===null||!DB::table('admin_contest')->where('cid','=',$cid)->where('uid','=',$this->luser->uid)->exists()))){
                    $this->errMsg="您没有权限设置比赛管理员！";
                }else{
                    $auids=json_decode($auids,true);
                    array_push($auids,($utype==='a'?$this->ladmin->uid:$this->luser->uid));
                    $auids=array_unique($auids,SORT_NUMERIC);
                    foreach($auids as $auid){
                        $user=User::where('uid',$auid)->first();
                        if(!in_array($auid,$preuids)&&$user!==null){
                            DB::table('admin_contest')->insert(['uid'=>$auid,'cid'=>$cid]);
                        }else{
                            array_splice($preuids,array_search($auid,$preuids),1);
                        }
                    }
                    foreach($preuids as $uid){
                        DB::table('admin_contest')->where('cid','=',$cid)->where('uid','=',$uid)->delete();
                    }
                }
            }else{
                $this->errMsg="该比赛不存在！";
            }
        }
    }
    
    public function uploadavatar(Request $request,$cid){
        if ($this->ladmin===null&&($this->luser===null||DB::table('admin_contest')->where('cid','=',$cid)->where('uid','=',$this->luser->uid)->exists())){
            $response = array(
                'state'  => 200,
                'status' => 4,
                'imgurl' => $this->config_basic['defaultavatar'],
                'message' => "您没有权限修改比赛头像，请重新登录！"
            );
        }else{
            $dstwidth=$this->config_basic["contestwidth"];
            $crop = new CropAvatar($request->post('avatar_src'), $request->post('avatar_data'), $_FILES['avatar_file'], $this->config_basic["contestavatar"].$cid,$dstwidth,$dstwidth);
            $response = array(
                'state'  => 200,
                'status' => $crop -> getResult()!==null?1:4,
                'imgurl' => $crop -> getResult()."?".filectime(public_path($crop -> getResult())),
                'message' => ($crop -> getMsg()!==null?$crop -> getMsg():"上传比赛成功！")
            );
            //'result' => $crop -> getMsg()
        }
        echo json_encode($response);
    }

    public function insertuser(){

    }
    public function insertuserlist(){

    }
    public function deluser(){

    }
    public function clearuser(Request $request,$cid){
        $utype=$request->post("utype",'');
        $contest=Contest::where('cid',$cid)->first();
        if($contest!==null){
            if($utype==="a"){
                if ($this->ladmin===null){
                    $this->errMsg="您不是管理员，没有权限清空参加比赛用户！";
                }elseif($contest->ctype==="d"){
                    $this->errMsg="该比赛已删除，请恢复后修改信息！";
                }
            }else{
                if($this->luser===null||!DB::table('admin_contest')->where('cid','=',$cid)->where('uid','=',$this->luser->uid)->exists()){
                    $this->errMsg="您不是该比赛管理员，没有权限清空参加比赛用户！";
                }
            }
            if($this->errMsg===null){
                $this->successMsg="清空该比赛参加用户成功！";
                DB::table('contest_user')->where('cid',$contest->cid)->delete();
            }
        }else{
            $this->errMsg="该比赛不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
}
