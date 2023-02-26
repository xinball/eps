<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Mews\Purifier\Facades\Purifier;
use App\Library\Func;
use Illuminate\Support\Facades\DB;


class NoticeController extends Controller
{
    //
    public function indexview(Request $request,$nid){
        if($this->ladmin!==null){                                                  //管理员查看某公告
            return view('notice.index')->with('nactive',true)->with('result',$this->aget($request,$nid));
        }
        return view('notice.index')->with('nactive',true)->with('result',$this->get($request,$nid));// 用户查看某公告
    }
    public function listview(Request $request){         //显示消息列表
        // if($this->ladmin!==null){
        //     return view('notice.list')->with('nactive',true)->with('utype','a');
        // }
        return view('notice.list')->with('nactive',true);
    }


    //每个种类的公告有多少个
    public function getTypeNum(){
        $sql = Notice::select('ntype',DB::raw("count('nid') as num"))->groupBy('ntype');
        $nums = $sql->get();
        $typenum=[
            'sum'=>0,
        ];
        foreach($nums as $num){
            $typenum[$num->ntype]=$num->num;
        }
        foreach($this->config_notice['typekey']['total'] as $type){
            if(!isset($typenum[$type])){
                $typenum[$type]=0;
            }
            $typenum['sum']+=$typenum[$type];
        }
        return $typenum;
    }

    //用户查看某公告
    public function get(Request $request,$nid){
        $notice=Notice::where('nid',$nid)->first();
        $this->url="/notice";
        if($notice!==null){
            if($notice->ntype==='h'){
                $this->errMsg="您没有权限查看该公告！";
            }elseif(strtotime($notice->ntime)>time()){
                $this->errMsg="该公告尚未发布，您无权查看！";
            }elseif($notice->ntype!=='d'){
                $this->successMsg="";
                //$notice->ninfo=Purifier::clean($notice->ninfo);
                $this->result->data=[
                    'notice'=>$notice,
                ];
                $this->url=null;
            }else{
                $this->errMsg="该公告已删除！";
            }
        }else{
            $this->errMsg="该公告不存在！";
        }
        $this->getResult();
        return $this->result->toJson();
    }


    //用户得到消息列表
    public function getlist(Request $request){
        $params=$request->all();

        $sql=Notice::distinct()->select("nid","ntitle","ndes","ntime","nupdate","ntype");
        $where[]=['ntime','<=',date("Y-m-d H:i:s")];
        $where[]=['ntype','!=','h'];
        $where[]=['ntype','!=','d'];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="ndes"||$key==="ntitle"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }elseif($key==="type"&&in_array($v,$this->config_notice['typekey']['total'])){
                    $where[]=['ntype','=',$v];
                }elseif($key==="nstart"&&strtotime($v)){
                    $where[]=['ntime','>=',$v];
                }elseif($key==="nend"&&strtotime($v)){
                    $where[]=['ntime','<=',$v];
                }
            }
        }
        $sql=$sql->where($where);

        $orderPara = $params['order']??"ntime";
        if($orderPara==="ntime"||$orderPara==="nupdate"){
            $sql=$sql->orderByDesc($orderPara)->orderByDesc('nid');
        }else{
            $sql=$sql->orderByDesc('nid');
        }
        //echo $sql->toSql();

        $notices=$sql->paginate($this->config_notice['listnum'])->withQueryString();
        // $this->listMsg($notices);
        $this->result->data=[
            'notices'=>$notices,
        ];
        $this->getResult();
        return $this->result->toJson();
    }

    //管理员查找某信息
    public function aget(Request $request,$nid){
        $this->url="/admin/notice";
        if($this->ladmin===null){                      //检测管理员身份
            $this->errMsg="您没有权限获取该公告信息！";
        }else{
            $notice=Notice::where('nid',$nid)->first();
            if($notice!==null){
                if($notice->ntype!=='d'){
                    $this->successMsg="";
                    $this->result->data=[
                        'notice'=>$notice,
                    ];
                    $this->url=null;
                }else{
                    $this->errMsg="该公告已删除！";
                }
            }else{
                $this->errMsg="该公告不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

    //管理员获取消息列表
    public function agetlist(Request $request){
        if($this->ladmin===null){                           //检测管理员身份
            $this->errMsg="您没有权限获取比赛列表！";
        }else{
            $params=$request->all();
            //$orwhere=array();
            $sql=Notice::select("nid","ntitle","ndes","ntime","nupdate","ntype")->distinct();
            $where=[];
            foreach ($params as $key=>$v){
                if($v!==""&&$v!==null){
                    // echo "'".$v."'".var_dump($v);
                    if($key==="ndes"||$key==="ntitle"){
                        $where[]=[$key,'like','%'.$v.'%'];
                    }elseif($key==="type"&&in_array($v,$this->config_notice['typekey']['total'])){
                        $where[]=['ntype','=',$v];
                    }elseif($key==="nstart"&&strtotime($v)){
                        $where[]=['ntime','>=',$v];
                    }elseif($key==="nend"&&strtotime($v)){
                        $where[]=['ntime','<=',$v];
                    }
                }
            }
            $sql=$sql->where($where);
    
            $orderPara = $params['order']??"ntime";
            if($orderPara==="ntime"||$orderPara==="nupdate"){
                $sql=$sql->orderByDesc($orderPara)->orderByDesc('nid');
            }else{
                $sql=$sql->orderByDesc('nid');
            }
            //echo $sql->toSql();
    
            $notices=$sql->paginate($this->config_notice['listnum'])->withQueryString();
            $this->listMsg($notices);
            $this->result->data=[
                'notices'=>$notices,
                'num'=>$this->getTypeNum(),
            ];
        }

        $this->getResult();
        return $this->result->toJson();
    }


    //管理员插入新公告
    public function insert(Request $request){
        $ntype=$request->post("ntype",'');
        if ($this->ladmin===null){
            $this->errMsg="您不是管理员，没有权限发布公告！";
        }elseif(!in_array($ntype,$this->config_notice['typekey']['all'])){
            $this->errMsg="公告类型有误！";
        }else{
            $ntitle=$request->post('ntitle',null);
            $ndes=$request->post('ndes',null);
            $ninfo=$request->post('ninfo',null);

            //$ninfo=Purifier::clean($request->post('ninfo',''));
            //$noption=[];
            if($ntitle===null||Func::Length($ntitle)>50){                     //格式检查
                $this->errMsg="公告标题格式不合规范【长度不得大于50】!";
            }elseif($ndes===null||Func::Length($ndes)>100){
                $this->errMsg="公告描述格式不合规范【长度不得大于100】!";
            }elseif($ninfo===null||Func::Length($ninfo)>100000){
                $this->errMsg="公告详细内容格式不合规范【长度不得大于100000】!";
            }else{
                $sendtype=$request->post('sendtype');
                $notice = new Notice();
                $notice->nuid=$this->ladmin->uid;
                $notice->ntitle=$ntitle;
                $notice->ndes=$ndes;
                $notice->ntype=$ntype;
                $notice->ninfo=$ninfo;
                if($sendtype==='o'){
                    $nowtime=time();
                    $ntime=$request->post('ntime',"");
                    $ntimeval=strtotime($ntime);
                    if(!$ntimeval){
                        $this->errMsg="公告发布时间格式有误！";
                    }elseif($ntimeval<$nowtime){
                        $this->errMsg="公告发布时间不得晚于当前时间！";
                    }else{
                        $notice->ntime=$ntime;
                        $notice->nupdate=$ntime;
                    }
                }elseif($sendtype!=='n'){
                    $this->errMsg="公告发布方式选择有误！";
                }
                if($this->errMsg===null){
                    // $notice->noption=json_encode($noption,JSON_UNESCAPED_UNICODE);
                    if($notice->save()){
                        $this->successMsg="发布公告成功！";
                    }else{
                        $this->errMsg='发布公告失败！';
                    }
                }
            }
        }

        $this->getResult();
        return $this->result->toJson();
    }


    //管理员删除公告
    public function del($nid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限删除该公告！";
        }else{
            $notice=Notice::where('nid',$nid)->first();            //先找到要删除的公告
            if($notice!==null){
                if($notice->ntype!=='d'){
                    $notice->ntype='d';
                    if($notice->update()>0){             //更新过，说明修改类型成功了，删除成功
                        $this->successMsg="删除该公告成功！";
                    }else{
                        $this->errMsg="删除该公告失败！";
                    }
                }else{
                    $this->errMsg="该公告已删除，无需再次删除！";
                }
            }else{
                $this->errMsg="该公告不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }


    //
    public function recover($nid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限恢复该公告！";
        }else{
            $notice=Notice::where('nid',$nid)->first();          //先找到要恢复的公告
            if($notice!==null){
                if($notice->ntype==='d'){
                    $notice->ntype='h';
                    if($notice->update()>0){                     //更新过，说明修改类型成功了，恢复成功
                        $this->successMsg="恢复该公告成功！";
                    }else{
                        $this->errMsg="恢复该公告失败！";
                    }
                }else{
                    $this->errMsg="该公告未被删除，无需恢复！";
                }
            }else{
                $this->errMsg="该公告不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    

    public function alter(Request $request,$nid){
        $notice=Notice::where('nid',$nid)->first();                //先找到要更改的公告
        if($notice!==null){
            $ntype=$request->post("ntype",'');
            if ($this->ladmin===null){
                $this->errMsg="您不是管理员，没有权限发布公告！";
            }elseif(!in_array($ntype,$this->config_notice['typekey']['all'])){
                $this->errMsg="公告类型有误！";
            }else{
                $ntitle=$request->post('ntitle','');
                $ndes=$request->post('ndes',"");
                $ninfo=$request->post('ninfo','');

                //$ninfo=Purifier::clean($request->post('ninfo',''));
                //$noption=[];
                if($ntitle===""||Func::Length($ntitle)>50){
                    $this->errMsg="公告标题格式不合规范【长度不得大于50】!";
                }elseif($ndes===""||Func::Length($ndes)>100){
                    $this->errMsg="公告描述格式不合规范【长度不得大于100】!";
                }elseif($ninfo===""){
                    $this->errMsg="公告详细内容格式不合规范!";
                }else{
                    $sendtype=$request->post('sendtype');
                    $notice->nuid=$this->ladmin->uid;
                    $notice->ntitle=$ntitle;
                    $notice->ndes=$ndes;
                    $notice->ntype=$ntype;
                    $notice->ninfo=$ninfo;
                    if($sendtype==='o'){
                        $nowtime=time();
                        $ntime=$request->post('ntime',"");
                        $ntimeval=strtotime($ntime);
                        if($ntimeval===false){
                            $this->errMsg="公告发布时间格式有误！";
                        }elseif($ntimeval<$nowtime){
                            $this->errMsg="公告发布时间不得晚于当前时间！";
                        }else{
                            $notice->ntime=$ntime;
                            $notice->nupdate=$ntime;
                        }
                    }elseif($sendtype==='n'){
                        $date=date('Y-m-d H:i:s');
                        $notice->ntime=$date;
                        $notice->nupdate=$date;
                    }elseif($sendtype!=='l'){
                        $this->errMsg="公告发布方式选择有误！";
                    }
                    if($this->errMsg===null){
                        // $notice->noption=json_encode($noption,JSON_UNESCAPED_UNICODE);
                        if($notice->update()){
                            $this->successMsg="公告修改成功！";
                        }else{
                            $this->errMsg='修改公告失败！';
                        }
                    }
                }
            }
        }else{
            $this->errMsg="该公告不存在！";
        }

        $this->getResult();
        return $this->result->toJson();
    }

}
