<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Contest;
use App\Models\Status;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use App\Library\Func;
use App\Library\Pager;
use App\Library\JudgeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Mews\Purifier\Facades\Purifier;
use Symfony\Component\Process\Process;

class JudgeController extends Controller
{
    public function heartbeat(Request $request){
        $arr=[
            'error'=>0,
            'data'=>json_encode($request->all()),
        ];
        echo json_encode( $arr);
    }
    public function ping(){
        if($this->ladmin===null){
            $this->errMsg="您没有权限获取状态机信息！";
            $this->url="/notice";
            $this->getResult();
            return $this->result->toJson();
        }
        $judgeClient = new JudgeClient($this->config_basic['token'], $this->config_basic['judgeurl']);
        $info=[
            'sysinfo'=>$judgeClient->ping(),
            'snums'=>$this->getSnums(Status::check(null)->where('screate','<',date('Y-m-d',strtotime("+1 day")))->where('screate','>',date('Y-m-d',strtotime("-10 day")))->groupBy('sresult')->get()),
            'asnums'=>$this->getSnums(Status::check('a')->where('screate','<',date('Y-m-d',strtotime("+1 day")))->where('screate','>',date('Y-m-d',strtotime("-10 day")))->groupBy('sresult')->get()),
            'usnums'=>$this->getSnums(Status::check('u')->where('screate','<',date('Y-m-d',strtotime("+1 day")))->where('screate','>',date('Y-m-d',strtotime("-10 day")))->groupBy('sresult')->get()),
        ];
        $this->result->data=[
            'info'=>$info,
        ];
        $this->successMsg="";
        $this->getResult();
        return $this->result->toJson();
    }
    public function test(){

$cSrc = <<<'CODE'
#include <stdio.h>
int main(){
    int a, b;
    scanf("%d%d", &a, &b);
    printf("%d\n", a+b);
    return 0;
}
CODE;
$cSpjSrc = <<<'CODE'
#include <stdio.h>
int main(){
    return 1;
}
CODE;

$cppSrc = <<<'CODE'
#include <iostream>

using namespace std;

int main()
{
    int a,b;
    cin >> a >> b;
    cout << a+b << endl;
    return 0;
}
CODE;

$javaSrc = <<<'CODE'
import java.util.Scanner;
public class Main{
    public static void main(String[] args){
        Scanner in=new Scanner(System.in);
        int a=in.nextInt();
        int b=in.nextInt();
        System.out.println(a + b);
    }
}
CODE;

$py2src = <<<'CODE'
s = raw_input()
s1 = s.split(" ")
print int(s1[0]) + int(s1[1])
CODE;

$py3src = <<<'CODE'
s = input()
s1 = s.split(" ")
print(int(s1[0]) + int(s1[1]))
CODE;

        $judgeClient = new JudgeClient($this->config_basic['token'], $this->config_basic['judgeurl']);
        
        // echo "\n\ncompile_spj:\n";
        // print_r($judgeClient->compileSpj($cSpjSrc, '2', JudgeClient::getLanguageConfigByKey('c_lang_spj_compile')));

        echo "\n\nc_judge:\n";
        print_r($judgeClient->judge($cSrc, 'c', 'normal', [
            'output' => true
        ]));

        echo "\n\njava_judge:\n";
        print_r($judgeClient->judge($javaSrc, 'j', 'normal', [
            'output' => true
        ]));

    }

    public function judge($id=1,$re=0){
        echo $id.$re;
        
        if(Redis::LINDEX('judging',$id)!=='0'){
            return;
        }
        Redis::LSET('judging',$id,1);
        $name='judge'.$id;
        while(Redis::LLEN($name)>0&&Redis::LINDEX('judging',$id)<5){
            $sid=Redis::RPOP($name);
            echo "sid=".$sid;
            $status=Status::where('sid',$sid)->first();
            //评测中和系统错误的可以进行评测
            if($status===null){
                continue;
            }
            if(($re)===0){
                if($status->sresult!=='p'&&$status->sresult!=='s'){
                    continue;
                }
            }
            $problem=Problem::where('pid',$status->spid)->first();
            if($problem===null){
                continue;
            }
            $problem->poption=json_decode($problem->poption,true);
            $problem->pcases=json_decode($problem->pcases,true);
            echo json_encode($problem->poption['timelimit']);
            $judgeClient = new JudgeClient($this->config_basic['token'], $this->config_basic['judgeurl']);
            $result=$judgeClient->judge($status->scode, $status->slang, $status->spid,[
                'max_cpu_time'=>intval($problem->poption['timelimit']),
                'max_memory'=>($status->slang==='j'?intval($problem->poption['spacelimit']):intval($problem->poption['spacelimit'])*1024)
                ]);
            echo json_encode($result);
            $stime=intval($problem->poption['timelimit']);
            $sspace=intval($problem->poption['spacelimit'])*1024;
            $score=0;
            echo 1;
            if(isset($result['err'])&&$result['err']==="CompileError"){
                $status->sresult='c';
                $status->sinfo=json_encode($result['data']);
            }elseif(isset($result['err'])&&$result['err']==="JudgeClientError"){
                $status->sresult='s';
                Redis::LPUSH($status->sid);
                $status->sinfo=json_encode($result['data']);
            }else{
                /*
                [{"error": 0, "memory": 499712, "output": null, "result": -1, "signal": 0, "cpu_time": 0, "exit_code": 0, "real_time": 2, "test_case": "1", "output_md5": "f502c69bec7c5a9f96a26641d09133e4"}, {"error": 0, "memory": 503808, "output": null, "result": -1, "signal": 0, "cpu_time": 0, "exit_code": 0, "real_time": 2, "test_case": "2", "output_md5": "f502c69bec7c5a9f96a26641d09133e4"}]
                */
                $wa=false;$tl=false;$ml=false;$re=false;$se=false;
                $stime=0;$sspace=0;
                $sinfo=[];
                foreach($result['data'] as $item){
                    echo $item['test_case'];
                    if($item['test_case']<=count($problem->pcases)){
                        $sinfo[$item['test_case']]=[
                            'space'=>isset($item['memory'])?$item['memory']:$sspace,
                            'time'=>isset($item['cpu_time'])?$item['cpu_time']:$stime,
                            'rtime'=>isset($item['real_time'])?$item['real_time']:$stime*2,
                            'result'=>isset($item['result'])?$this->config_status['resultcodes'][$item['result']]:"s",
                            'error'=>isset($item['error'])?$item['error']:-1,
                            'signal'=>isset($item['signal'])?$item['signal']:0,
                            'output_md5'=>isset($item['output_md5'])?$item['output_md5']:"",
                            'score'=>($item['result']===0?intval($problem->pcases[intval($item['test_case'])-1]['score']):0),
                        ];
                        echo print_r($sinfo);
                        switch($item['result']){
                            case '-1':$wa=true;break;
                            case '1':$tl=true;break;
                            case '2':$tl=true;break;
                            case '3':$ml=true;break;
                            case '4':$re=true;break;
                            case '5':$se=true;break;
                            case '0':$score+=$sinfo[(int)$item['test_case']]['score'];break;
                        }
                        $stime=($stime<$item['cpu_time']?$item['cpu_time']:$stime);$sspace=($sspace<$item['memory']?$item['memory']:$sspace);
                    }
                }
                if($wa){
                    $status->sresult='w';
                }elseif($tl){
                    $status->sresult='t';
                }elseif($ml){
                    $status->sresult='m';
                }elseif($re){
                    $status->sresult='r';
                }elseif($se){
                    $status->sresult='s';
                    Redis::LPUSH($status->sid);
                }else{
                    $status->sresult='a';
                }
                $status->sinfo=json_encode($sinfo);
            }
            if($status->sresult==='s'){
                Redis::LSET('judging',$id,intval(Redis::LINDEX('judging',$id))+1);
            }else{
                Redis::LSET('judging',$id,1);
            }
            $status->score=$score;
            $status->stime=$stime;
            $status->sspace=$sspace;
            $status->update();
        }
        Redis::LSET('judging',$id,0);
    }
    public function rejudge(Request $request,$id=1,$sid=null){
        if($sid===null&&$this->ladmin===null){
            $this->errMsg="您没有权限重判，请重新登录！";
            $this->url="/notice";
            $this->getResult();
            return $this->result->toJson();
        }
        if($sid===null){
            $pids=json_decode($request->post('pids','[]'),true);
            $sids=json_decode($request->post('sids','[]'),true);
            $uids=json_decode($request->post('uids','[]'),true);
            $cids=json_decode($request->post('cids','[]'),true);
            foreach($pids as $pid){
                $sids=array_unique(array_merge(Status::getSids($pid),$sids));
            }
            foreach($uids as $uid){
                $sids=array_unique(array_merge(Status::getSids(null,$uid),$sids));
            }
            foreach($cids as $cid){
                $sids=array_unique(array_merge(Status::getSids(null,null,$cid),$sids));
            }
        }else{
            $status=Status::where('sid',$sid)->first();
            if($status===null){
                $this->errMsg="提交不存在！";
                $this->url="/status";
                $this->getResult();
                return $this->result->toJson();
            }
            if($this->ladmin!==null){
                $this->successMsg="您已登录管理员身份，将以管理员身份进行重判";
            }elseif($this->luser===null){
                $this->errMsg="您没有登录用户，没有权限重判，请重新登录！";
                $this->url="/notice";
                $this->getResult();
                return $this->result->toJson();
            }elseif($status->suid!==$this->luser->sid){
                $this->errMsg="这不是提交的代码，您没有权限重判！";
                $this->url="/status";
                $this->getResult();
                return $this->result->toJson();
            }
            $sids=[$sid];
        }
        
        foreach($sids as $sid){
            Redis::LPUSH(('judge'.$id),$sid);
        }
        file_get_contents(config('var.jj').$id.'/1');
        $this->successMsg="正在重判".count($sids)."条提交，请等待...";
        $this->getResult();
        return $this->result->toJson();
    }
    public function urejudge(Request $request,$sid){
        $status=Status::where('sid',$sid)->first();
        if($status===null){
            $this->errMsg="提交不存在！";
            $this->url="/status";
            $this->getResult();
            return $this->result->toJson();
        }
        if($this->ladmin!==null){
            $this->successMsg="您已登录管理员身份，将以管理员身份进行重判";
            $this->rejudge($request,1,$sid);
            sleep(2);
            $this->result->data=[
                'status'=>Status::where('sid',$status->sid)->first(),
            ];
            $this->getResult();
            return $this->result->toJson();
        }elseif($this->luser===null){
            $this->errMsg="您没有登录用户，没有权限重判，请重新登录！";
            $this->url="/notice";
            $this->getResult();
            return $this->result->toJson();
        }elseif($status->suid!==$this->luser->sid){
            $this->errMsg="这不是提交的代码，您没有权限重判！";
            $this->url="/status";
            $this->getResult();
            return $this->result->toJson();
        }
        $this->rejudge($request,1,$sid);
        sleep(2);
        $this->result->data=[
            'status'=>Status::where('sid',$status->sid)->first(),
        ];
        $this->getResult();
        return $this->result->toJson();
    }

    public function check(Request $request){
        if($this->ladmin===null){
            $this->errMsg="您没有权限查重，请重新登录！";
            $this->url="/notice";
            $this->getResult();
            return $this->result->toJson();
        }
        
        $pids=json_decode($request->post('pids','[]'),true);
        $cids=json_decode($request->post('cids','[]'),true);
        $sids=[];
        foreach($pids as $pid){
            $sids=array_unique(array_merge(Status::getSids($pid),$sids));
        }
        foreach($cids as $cid){
            $sids=array_unique(array_merge(Status::getSids(null,null,$cid),$sids));
        }
        $sidlist=[];
        $statusinfo="";
        foreach($sids as $sid){
            $status=Status::where('sid',$sid)->first();
            if($status===null||$status->score===0)
                continue;
            $sidlist[$status->slang]=" ".$sid." ";
            $statusinfo.="\n".json_encode($status);
        }
        $command="cd ".storage_path('app/status');
        if(isset($sidlist['c'])){
            $command.=" && "."sim_c -p-e  ".$sidlist['c']." ";
        }
        if(isset($sidlist['d'])){
            $command.=" && "."sim_cpp -p-e  ".$sidlist['d']." ";
        }
        if(isset($sidlist['j'])){
            $command.=" && "."sim_java -p-e  ".$sidlist['j']." ";
        }
        // if(isset($sidlist['c'])){
        //     $command.="sim_c -p-e  ".$sidlist['c']." ";
        // }
        // if(isset($sidlist['c'])){
        //     $command.="sim_c -p-e  ".$sidlist['c']." ";
        // }
        $process=Process::fromShellCommandline($command);
        $process->run();
        $out = $process->getOutput();
        $fp=fopen(storage_path('/app/check/'.time()."txt"),'w');
        fwrite($fp,$statusinfo);
        fwrite($fp,$out);
        fclose($fp);
        $this->successMsg="正在进行查重，请等待...";
        $this->getResult();
        return $this->result->toJson();
    }
}

