<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Controllers\Controller;
use App\Library\Func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class TagController extends Controller
{
    //
    public function get(Request $request,$tid){
        $tid=$request->get('tid','');
        $tname=$request->get('tname','');
        if($tid!=''){
            $tag=Tag::where('tid',$tid)->first();
        }elseif($tname!=''){
            $tag=Tag::where('tname',$tname)->first();
        }
        return json_encode($tag,JSON_UNESCAPED_UNICODE);
    }
    public function getlist(Request $request){
        $params=$request->all();

        $sql=Tag::distinct()->select("tid","tname","tdes","tnum","tlnum");
        $where=[];
        foreach ($params as $key=>$v){
            if($v!==""&&$v!==null){
                if($key==="tdes"||$key==="tname"){
                    $where[]=[$key,'like','%'.$v.'%'];
                }elseif($key==="tid"){
                    $where[]=['tid','=',$v];
                }
            }
        }
        $sql=$sql->where($where);

        $orderPara = $params['order']??"";
        if($orderPara==="tnum"||$orderPara==="tlnum"){
            $sql=$sql->orderByDesc($orderPara)->orderBy('tid');
        }else{
            $sql=$sql->orderBy('tid');
        }
        $tags=$sql->get();
        if($this->luser){
             $tids=Tag::getTidsByUid($this->luser->uid);
            foreach($tags as $tag){
                if(in_array($tag->tid,$tids)){
                    $tag->like=true;
                }else{
                    $tag->like=false;
                }
            }
        }
        $this->result->tags=$tags;
        $this->getResult();
        return $this->result->toJson();
    }

    public function insert(Request $request){
        if ($this->ladmin===null){
            $this->errMsg="您不是管理员，没有权限添加标签！";
            $this->getResult();
            return $this->result->toJson();
        }
        $tname=$request->post('tname',null);
        if(Tag::where('tname',$tname)->first()!==null){
            $this->errMsg="该标签已添加！";
            $this->getResult();
            return $this->result->toJson();
        }
        $tdes=$request->post('tdes',null);
        if($tname===null||Func::Length($tname)>20){
            $this->errMsg="标签名称格式不合规范【长度不得大于20】!";
        }elseif($tdes===null||Func::Length($tdes)>100){
            $this->errMsg="标签描述格式不合规范【长度不得大于100】!";
        }else{
            $tag = new Tag();
            $tag->tname=$tname;
            $tag->tdes=$tdes;
            if($tag->save()){
                $this->successMsg="添加标签成功！";
                $this->result->tid=$tag->tid;
            }else{
                $this->errMsg='添加标签失败！';
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

    public function alter(Request $request,$tid){
        if ($this->ladmin===null){
            $this->errMsg="您不是管理员，没有权限修改标签！";
            $this->getResult();
            return $this->result->toJson();
        }
        $tag=Tag::where('tid',$tid)->first();
        if($tag===null){
            $this->errMsg="该标签不存在！";
            $this->getResult();
            return $this->result->toJson();
        }
        $tname=$request->post('tname',null);
        if(Tag::where('tname','=',$tname)->where('tid','!=',$tid)->exists()){
            $this->errMsg="该标签名称已添加！";
            $this->getResult();
            return $this->result->toJson();
        }
        $tdes=$request->post('tdes',null);
        if($tname===null||Func::Length($tname)>20){
            $this->errMsg="标签名称格式不合规范【长度不得大于20】!";
        }elseif($tdes===null||Func::Length($tdes)>100){
            $this->errMsg="标签描述格式不合规范【长度不得大于100】!";
        }else{
            $tag->tname=$tname;
            $tag->tdes=$tdes;
            if($tag->update()){
                $this->successMsg="修改标签成功！";
            }else{
                $this->errMsg='修改标签失败！';
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

    public function del($tid){
        if($this->ladmin===null){
            $this->errMsg="您没有权限删除该标签！";
        }else{
            $tag=Tag::where('tid',$tid)->first();
            if($tag!==null){
                DB::table('problem_tag')->where('tid',$tid)->delete();
                DB::table('user_tag')->where('tid',$tid)->delete();
                if(Tag::where('tid',$tid)->delete()){
                    $this->successMsg="删除该标签成功！";
                }else{
                    $this->errMsg="删除该标签失败！";
                }
            }else{
                $this->errMsg="该标签不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function like($tid){
        if($this->luser===null){
            $this->errMsg="您没有权限收藏该标签！";
        }else{
            $tag=Tag::where('tid',$tid)->first();
            if($tag!==null){
                if(DB::table('user_tag')->where(['uid'=>$this->luser->uid,'tid'=>$tid])->first()===null){
                    if(DB::table('user_tag')->insert(['uid'=>$this->luser->uid,'tid'=>$tid])){
                        $this->successMsg="收藏该标签成功！";
                    }else{
                        $this->errMsg="收藏该标签失败！";
                    }
                }else{
                    $this->warnMsg="该标签已收藏，无需再次收藏！";
                }
            }else{
                $this->errMsg="该标签不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function dellike($tid){
        if($this->luser===null){
            $this->errMsg="您没有权限取消收藏该标签！";
        }else{
            $tag=Tag::where('tid',$tid)->first();
            if($tag!==null){
                if(DB::table('user_tag')->where(['uid'=>$this->luser->uid,'tid'=>$tid])->first()!==null){
                    if(DB::table('user_tag')->where(['uid'=>$this->luser->uid,'tid'=>$tid])->delete()){
                        $this->successMsg="取消收藏该标签成功！";
                    }else{
                        $this->errMsg="取消收藏该标签失败！";
                    }
                }else{
                    $this->warnMsg="该标签未收藏，无需取消收藏！";
                }
            }else{
                $this->errMsg="该标签不存在！";
            }
        }
        $this->getResult();
        return $this->result->toJson();
    }

}
