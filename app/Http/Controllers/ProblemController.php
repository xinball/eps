<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Contest;
use App\Models\Status;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use App\Library\Func;
use App\Library\Pager;
use Chumper\Zipper\Zipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Mews\Purifier\Facades\Purifier;

class ProblemController extends Controller
{
    public function listview(Request $request){
        return view('problem.list')->with('pactive',true);
    }
    public function indexview(Request $request,$pid,$cid=null){
        if($cid===null){
            if($this->ladmin!==null){
                $result=$this->aget($request,$pid);
            }else{
                $result=$this->get($request,$pid);
            }
        }else{
            $result=$this->cget($request,$cid,$pid);
        }
        return view('problem.index')->with('pactive',true)->with('result',$result);
    }

    public function getlist(Request $request){
        $params=$request->all();

        //$orwhere=array();
        $sql=Problem::distinct()->select("pid","ptitle","pdes","pacrate","pac","pce","pwa","pre","ptl","pml","pse","ptype","psubmit");
        $sql=$sql->where('ptype','=','o');
        $where[]=['ptype','!=','h'];
        $where[]=['ptype','!=','d'];
        $where[]=['ptype','!=','m'];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                // echo "{".$key.":".$v."}".var_dump($v)."...";
                if($key==="pdes"||$key==="ptitle"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }elseif($key==="type"){
                    $where[]=['ptype','=',$v];
                }
            }
        }
        $sql=$sql->where($where);
        // if($this->luser){
        //     $sql=$sql->orWhere(function ($query) use ($where){
        //         $query->where('puid','=',$this->luser->uid)->where('ptype','=','m')->where($where);
        //     });
        // }
        /*->where(function ($query) use ($orwhere){
            foreach ($orwhere as $item){
                $query->orWhere($item[0],$item[1],$item[2]);
            }
        })*/
        $tids=array_unique(json_decode($request->get('tids')??'[]',true),SORT_NUMERIC);
        if($tids!==null&&count($tids)>0){
            foreach ($tids as $tid){
                if(Func::isNum($tid,1,20)){
                    $sql=$sql->whereExists(function ($query) use($tid){
                        $query->select('pid')
                            ->from('problem_tag')
                            ->whereColumn('problem_tag.pid','problem.pid')
                            ->where('tid',$tid);
                    });
                }
            }
        }

        $orderPara = $params['order']??"";
        if($orderPara==="psubmit"||$orderPara==="pac"||$orderPara==="pacrate"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('problem.pid');
        }else{
            $sql=$sql->orderByDesc('problem.pid');
        }
        //echo $sql->toSql();

        $problems=$sql->paginate($this->config_problem['listnum'])->withQueryString();
        $this->listMsg($problems);
        foreach ($problems as $problem){
            $problem->tids=json_encode(Tag::getTidsByPid($problem['pid']),JSON_UNESCAPED_UNICODE);
        }
        
        $this->result->data=[
            'problems'=>$problems,
        ];
        $this->result->tags=Tag::all();
        
        $this->getResult();
        return $this->result->toJson();

    }
    public function igetlist(Request $request){
        if($this->luser===null){
            $this->errMsg="??????????????????????????????????????????";
            $this->getResult();
            return $this->result->toJson();
        }
        $params=$request->all();

        $sql=Problem::distinct();
        $where=[];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="pdes"||$key==="ptitle"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }
            }
        }
        $sql=$sql->where('puid','=',$this->luser->uid)->where($where);
        $tids=array_unique(json_decode($request->get('tids')??'[]',true),SORT_NUMERIC);
        if($tids!==null&&count($tids)>0){
            foreach ($tids as $tid){
                if(Func::isNum($tid,1,20)){
                    $sql=$sql->whereExists(function ($query) use($tid){
                        $query->select('pid')
                            ->from('problem_tag')
                            ->whereColumn('problem_tag.pid','problem.pid')
                            ->where('tid',$tid);
                    });
                }
            }
        }

        $orderPara = $params['order']??"";
        if($orderPara==="psubmit"||$orderPara==="pac"||$orderPara==="pacrate"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('problem.pid');
        }else{
            $sql=$sql->orderByDesc('problem.pid');
        }
        // echo $sql->toSql();

        $problems=$sql->paginate($this->config_problem['listnum'])->withQueryString();
        $this->listMsg($problems);
        foreach ($problems as $problem){
            $problem->tids=json_encode(Tag::getTidsByPid($problem['pid']),JSON_UNESCAPED_UNICODE);
        }
        
        $this->result->data=[
            'problems'=>$problems,
        ];
        $this->result->tags=Tag::all();
        
        $this->getResult();
        return $this->result->toJson();
    }
    public function agetlist(Request $request){
        if($this->ladmin===null){
            $this->errMsg="????????????????????????????????????";
            $this->getResult();
            return $this->result->toJson();
        }
        
        $params=$request->all();

        //$orwhere=array();
        $sql=Problem::distinct()->select("pid","ptitle","pdes","pacrate","pac","pce","pwa","pre","ptl","pml","pse","ptype","psubmit");

        $where=[];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="pdes"||$key==="ptitle"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }elseif($key==="type"&&($v==='h'||$v==='o'||$v==='m')){
                    $where[]=['ptype','=',$v];
                }elseif($key==="puid"){
                    $where[]=[$key,'=',$v];
                }
            /*if($key==="page"||$key==="order"||$key==="tid"||trim($v)==="")
                continue;
            *//*else{
                $where[$key]=$v;
            }*/
            }
        }
        $sql=$sql->where($where);
        
        $tids=array_unique(json_decode($request->get('tids')??'[]',true),SORT_NUMERIC);
        if($tids!==null&&count($tids)>0){
            foreach ($tids as $tid){
                if(Func::isNum($tid,1,20)){
                    $sql=$sql->whereExists(function ($query) use($tid){
                        $query->select('pid')
                            ->from('problem_tag')
                            ->whereColumn('problem_tag.pid','problem.pid')
                            ->where('tid',$tid);
                    });
                }
            }
        }

        $orderPara = $params['order']??"";
        if($orderPara==="psubmit"||$orderPara==="pac"||$orderPara==="pacrate"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('problem.pid');
        }else{
            $sql=$sql->orderByDesc('problem.pid');
        }
        //echo $sql->toSql();

        $problems=$sql->paginate($this->config_problem['listnum'])->withQueryString();
        foreach ($problems as $problem){
            $problem->tids=json_encode(Tag::getTidsByPid($problem['pid']),JSON_UNESCAPED_UNICODE);
        }
        $this->listMsg($problems);
        $this->result->data=[
            'problems'=>$problems,
        ];
        $this->result->tags=Tag::all();
        $this->getResult();
        return $this->result->toJson();

    }
    public function get(Request $request,$pid){
        $problem=Problem::select("pid","problem.puid","user.uname","ptitle","pdes","pacrate","pac","pce","pwa","pre","ptl","pml","pse","ptype","psubmit","pinfo","poption","pcases")->where('pid',$pid)->join('user','user.uid','=','problem.puid')->first();
        $this->url="/problem";
        if($problem!==null){
            //($problem->ptype!=='d'&&$this->luser&&$problem->puid===$this->luser->uid)
            if($problem->ptype==='o'||($this->luser!==null&&$problem->ptype==='m'&&$problem->puid===$this->luser->uid)){
                $this->successMsg="";
                $this->url=null;
                // $problem->pinfo=Purifier::clean($problem->pinfo);
                $problem->tids=Tag::getTidsByPid($problem->pid);
                if($this->luser)
                $problem->usnums=$this->getSnums(Status::getNumByUidAndPid($this->luser->uid,$pid));
                // $problem->user=User::where('uid',$problem->puid)->first();
                $this->result->data=[
                    'problem'=>$problem,
                ];
                $this->result->tags=Tag::all();
            }elseif($problem->ptype==='d'){
                $this->errMsg="?????????????????????";
            }else{
                $this->errMsg="?????????????????????????????????";
            }
        }else{
            $this->errMsg="?????????????????????";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function cget(Request $request,$cid,$pid){
        $this->url="/contest";
        $contest=Contest::where('cid',$cid)->first();
        if($contest===null){
            $this->errMsg="?????????????????????";
            $this->getResult();
            return $this->result->toJson();
        }
        if($contest->ctype==='d'){
            $this->errMsg="?????????????????????";
            $this->getResult();
            return $this->result->toJson();
        }
        $this->getContest($contest);
        $problem=Problem::select("pid","problem.puid","user.uname","ptitle","pdes","pacrate","pac","pce","pwa","pre","ptl","pml","pse","ptype","psubmit","pinfo","poption","pcases")->where('pid',$pid)->join('user','user.uid','=','problem.puid')->first();
        $this->url="/contest/".$cid;
        if($problem===null){
            $this->errMsg="?????????????????????";
        }elseif($problem->ptype!=='m'||!DB::table('contest_problem')->where('pid',$pid)->where('cid',$cid)->exists()){
            $this->errMsg="??????????????????????????????????????????????????????";
        }elseif($this->ladmin!==null){
            $this->successMsg="??????????????????????????????????????????????????????????????????";
            $this->url=null;
        }elseif ($this->luser===null){
            $this->errMsg="???????????????????????????????????????????????????????????????";
        }elseif(DB::table('admin_contest')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()){
            $this->successMsg="??????????????????????????????????????????????????????????????????";
            $this->url=null;
        }elseif(strtotime($contest->cstart)>time()||strtotime($contest->cend)<time()){
            $this->errMsg="??????????????????????????????????????????????????????";
        }elseif(DB::table('contest_user')->where('cid','=',$contest->cid)->where('uid','=',$this->luser->uid)->exists()){
            $this->successMsg="?????????????????????????????????";
            $this->url=null;
            $this->setContestPrivate($contest,true);
        }else{
            $this->errMsg="???????????????????????????????????????";
        }
        if($this->errMsg===null){
            $contest->snums=$this->getSnums(Status::getNumByCid($contest->cid));
            $contest->pids=Problem::getPidsByCid($cid);
            $problem->tids=Tag::getTidsByPid($problem->pid);
            $problem->snums=$this->getSnums(Status::getNumByCidAndPid($cid,$pid));
            if($this->luser!==null)
                $problem->usnums=$this->getSnums(Status::getNumByUidAndPid($this->luser->uid,$pid,null));
            $this->result->data=[
                'problem'=>$problem,
                'contest'=>$contest,
            ];
            $this->result->tags=Tag::all();
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function aget(Request $request,$pid){
        $this->url="/problem";
        if($this->ladmin===null){
            $this->errMsg="???????????????????????????????????????";
        }else{
            $problem=Problem::select("pid","problem.puid","user.uname","ptitle","pdes","pacrate","pac","pce","pwa","pre","ptl","pml","pse","ptype","psubmit","pinfo","poption","pcases")->where('pid',$pid)->join('user','user.uid','=','problem.puid')->first();
            if($problem!==null){
                $this->successMsg="";
                $this->url=null;
                // $problem->pinfo=Purifier::clean($problem->pinfo);
                $problem->tids=Tag::getTidsByPid($problem->pid);
                $problem->usnums=$this->getSnums(Status::getNumByUidAndPid($this->ladmin->uid,$pid));
                $problem->asnums=$this->getSnums(Status::getNumByUidAndPid($this->ladmin->uid,$pid,null));
                $this->result->data=[
                    'problem'=>$problem,
                ];
                $this->result->tags=Tag::all();
                $this->url=null;
            }else{
                $this->errMsg="?????????????????????";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function check($ptitle,$pdes,$pinfo,$pcases,$in,$out,$cases,$tids,$timelimit,$spacelimit,$tip,$source,$pcasesfile){
        if($ptitle===null||Func::Length($ptitle)>50){
            $this->errMsg="???????????????????????????????????????????????????50??????";
        }elseif($pdes===null||Func::Length($pdes)>100){
            $this->errMsg="???????????????????????????????????????????????????100??????";
        }elseif($pinfo===""||Func::Length($pinfo)>100000){
            $this->errMsg="?????????????????????????????????????????????????????????100000??????";
        }elseif($pcases!=="[]"&&!json_decode($pcases,true)){
            $this->errMsg="???????????????????????????????????????";
        }elseif($tip===""||Func::Length($tip)>200){
            $this->errMsg="?????????????????????????????????????????????200??????";
        }elseif($source===""||Func::Length($source)>200){
            $this->errMsg="?????????????????????????????????????????????200??????";
        }elseif($in===""||Func::Length($in)>1000){
            $this->errMsg="?????????????????????????????????????????????????????????1000??????";
        }elseif($out===""||Func::Length($out)>1000){
            $this->errMsg="?????????????????????????????????????????????????????????1000??????";
        }elseif($cases!=="[]"&&!json_decode($cases,true)){
            $this->errMsg="???????????????????????????";
        }elseif($tids!=="[]"&&!json_decode($tids,true)){
            $this->errMsg="???????????????????????????";
        }elseif(!Func::isNum($timelimit,1)||$timelimit<1||$timelimit>3000){
            $this->errMsg="????????????????????????3000ms???";
        }elseif(!Func::isNum($spacelimit,1)||$spacelimit<1||$spacelimit>100000){
            $this->errMsg="????????????????????????100000KB???";
        }
        if($this->errMsg!==null){
            return false;
        }
        $cases=json_decode($cases,true);
        foreach($cases as $case){
            if(!isset($case['in'])||!isset($case['out'])){
                $this->errMsg="???????????????????????????";
                return false;
            }
        }
        if($pcasesfile!==null){
            $pcases=json_decode($pcases,true);
            foreach($pcases as $i=>$pcase){

                // $this->errMsg=(isset($pcase['in'])===false)."|".!isset($pcase['out'])."|".($pcase['in']!==$i.'.in')."|".($pcase['out']!==$i.'.out')."|".(!isset($pcase['score']))."|".(!Func::isNum($pcase['score'],1));
                if(!isset($pcase['in'])||!isset($pcase['out'])||$pcase['in']!==($i+1).'.in'||$pcase['out']!==($i+1).'.out'||!isset($pcase['score'])||!Func::isNum($pcase['score'],1)){
                    $this->errMsg.="???????????????????????????!";
                    return false;
                }
            }
        }
        return true;
    }
    public function insert(Request $request){
        $utype=$request->post("utype",null);
        $ptype=$request->post("ptype",null);
        $pinfo=$request->post('pinfo',null);
        $in=$request->post('in',null);
        $out=$request->post('out',null);
        $problem = new Problem();
        if($utype==='a'){
            if($this->ladmin===null){
                $this->errMsg="????????????????????????????????????????????????";
            }elseif(!in_array($ptype,$this->config_problem['typekey']['a'])){
                $this->errMsg="??????????????????????????????????????????";
            }else{
                $problem->puid=$this->ladmin->uid;
            }
        }else{
            if($this->luser===null){
                $this->errMsg="????????????????????????????????????????????????";
            }elseif($ptype!=="m"){
                $this->errMsg="?????????????????????????????????????????????????????????????????????";
            }else{
                $problem->puid=$this->luser->uid;
                $pinfo=Purifier::clean($pinfo);
                $in=Purifier::clean($in);
                $out=Purifier::clean($out);
            }
        }
        if($this->errMsg!==null){
            $this->getResult();
            return $this->result->toJson();
        }
        $ptitle=$request->post('ptitle',null);
        $pdes=$request->post('pdes',null);
        $pcases="[]";
        $pcasesfile=null;
        if($request->hasFile('pcasesfile')){
            $pcasesfile=$request->file('pcasesfile');
            if($pcasesfile!==null&&$pcasesfile->isValid()){
                $pcases=$request->post('pcases',"[]");
            }
        }else{
            $problem->pcases="[]";
        }


        $tip=$request->post('tip',null);
        $source=$request->post('source',null);
        $cases=$request->post('cases',null);
        $tids=$request->post('tids',null);
        $timelimit=$request->post('timelimit',null);
        $spacelimit=$request->post('spacelimit',null);
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
                $this->successMsg="?????????????????????";
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
                $this->errMsg='?????????????????????';
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
                    $this->errMsg="????????????????????????????????????????????????";
                }elseif($problem->ptype==='d'){
                    $this->errMsg="??????????????????????????????????????????";
                }
            }else{
                if($this->luser===null){
                    $this->errMsg="????????????????????????????????????????????????";
                }elseif($problem->ptype==='d'){
                    $this->errMsg="??????????????????????????????????????????";
                }elseif($problem->puid!==$this->luser->uid){
                    $this->errMsg="??????????????????????????????????????????????????????";
                }
            }
            if($this->errMsg!==null){
                $this->getResult();
                return $this->result->toJson();
            }
            if(DB::table('contest_problem')->where('pid','=',$pid)->first()){
                $this->errMsg="????????????????????????????????????????????????";
            }else{
                $problem->ptype='d';
                if($problem->update()>0){
                    $this->successMsg="????????????????????????";
                }else{
                    $this->errMsg="????????????????????????";
                }
            }
        }else{
            $this->errMsg="?????????????????????";
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
                    $this->errMsg="????????????????????????????????????????????????";
                }elseif($problem->ptype==='d'){
                    $this->errMsg="????????????????????????????????????";
                }
            }else{
                if($this->luser===null){
                    $this->errMsg="????????????????????????????????????????????????";
                }elseif($problem->ptype==='d'){
                    $this->errMsg="????????????????????????????????????";
                }elseif($problem->puid!==$this->luser->uid){
                    $this->errMsg="??????????????????????????????????????????????????????";
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
                $this->errMsg="??????????????????????????????????????????????????????";
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
                        $this->successMsg="?????????????????????";
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
                        $this->errMsg='?????????????????????';
                    }
                }
            }
        }else{
            $this->errMsg="?????????????????????";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function setcases($pcasesfile,$pcases,&$problem){
        if($pcasesfile!==null){
            $url='/judge/tests/test_case/';
            $filepath=storage_path('/app'.$url.$problem->pid);
            if(!is_dir($filepath))
                mkdir($filepath);
            $pcasesfile->storeAs($url,$problem->pid.'.zip');
            $zipper=new Zipper();
            $zipper->make(storage_path('/app/'.$url.$problem->pid.'.zip'))->extractTo($filepath);
            $zipper->close();

            $pcasesarr=json_decode($pcases,true);
            $len=count($pcasesarr);
            $info=[
                'test_case_number'=>$len,
                'spj'=>false,
                'test_cases'=>(object)[]
            ];
            $files=array();
            $head=opendir($filepath);
            while(($file=readdir($head))!==false){
                if($file!=='.'&&$file!=='..'){
                    if(!is_dir($filepath.'/'.$file)){
                        $files[]=$file;
                    }
                }
            }
            $i=1;
            foreach($pcasesarr as $pcase){
                if(in_array($pcase['in'],$files)&&in_array($pcase['out'],$files)){
                    $in=$filepath.'/'.$pcase['in'];
                    $out=$filepath.'/'.$pcase['out'];
                    $info['test_cases']->$i=[
                        'input_name'=>$pcase['in'],
                        'output_name'=>$pcase['out'],
                        'input_size'=>filesize($in),
                        'output_size'=>filesize($out),
                        'output_md5'=>md5(utf8_encode(file_get_contents($out))),
                        'stripped_output_md5'=>md5(trim(utf8_encode(file_get_contents($out)))),
                    ];
                    $i++;
                }else{
                    $problem->pcases='[]';
                    break;
                }
            }
            if($i>=$len){
                $problem->pcases=$pcases;
            }
            $fp=fopen($filepath."/info",'w');
            fwrite($fp,json_encode($info));
            fclose($fp);
        }
    }
    public function recover(Request $request,$pid){
        $utype=$request->get("utype",'');
        $problem=Problem::where('pid',$pid)->first();
        if($problem!==null){
            if($utype==='a'){
                if($this->ladmin===null){
                    $this->errMsg="????????????????????????????????????????????????";
                }elseif($problem->ptype!=='d'){
                    $this->errMsg="????????????????????????????????????";
                }else{
                    $problem->ptype='h';
                }
            }else{
                if($this->luser===null){
                    $this->errMsg="????????????????????????????????????????????????";
                }elseif($problem->ptype!=='d'){
                    $this->errMsg="????????????????????????????????????";
                }elseif($problem->puid!==$this->luser->uid){
                    $this->errMsg="??????????????????????????????????????????????????????";
                }else{
                    $problem->ptype='m';
                }
            }
            if($this->errMsg!==null){
                $this->getResult();
                return $this->result->toJson();
            }
            if($problem->update()>0){
                $this->successMsg="????????????????????????";
            }else{
                $this->errMsg="????????????????????????";
            }
        }else{
            $this->errMsg="?????????????????????";
        }
        $this->getResult();
        return $this->result->toJson();
    }
}
