<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Library\Func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class AreaController extends Controller{
    public function get(Request $request){
        $areas=[];
        $params = $request->all();
        $rids=[];
        $cids=[];
        $sids=[];
        $ruids=[];
        $cuids=[];
        $suids=[];
        $uids=[
            'state_id'=>&$suids,
            'city_id'=>&$cuids,
            'region_id'=>&$ruids,
        ];
        $ids=[
            'state_id'=>&$sids,
            'city_id'=>&$cids,
            'region_id'=>&$rids,
        ];
        $result=[
            'state_id'=>[
                'id'=>$params['state_id']??null,
                'type'=>'s',
                'table'=>'states',
            ],
            'city_id'=>[
                'id'=>$params['city_id']??null,
                'type'=>'c',
                'table'=>'cities',
            ],
            'region_id'=>[
                'id'=>$params['region_id']??null,
                'type'=>'r',
                'table'=>'regions',
            ],
        ];
        if($this->checkauth('s')){
            foreach($result as $index=>$where){
                $sql=DB::table('admin_area')->selectRaw($index.",cname,JSON_ARRAYAGG(uid) as uids")->where('type',$where['type']);
                if(!$this->checkauth('x')){
                    $sql=$sql->where('uid',$this->ladmin->uid);
                }elseif(isset($params['uid'])){
                    $sql=$sql->where('uid',$params['uid']);
                }
                if($where['id']!==null){
                    $sql = $sql->where($index,$where['id']);
                }
                if($index==='city_id'){
                    // echo json_encode($ids['state_id']);
                    $sql->whereNotIn('admin_area.state_id',$ids['state_id']);
                }
                if($index==='region_id'){
                    $sql->whereNotIn('state_id',$ids['state_id'])->whereNotIn('admin_area.city_id',$ids['city_id']);
                }
                $res = $sql->join($where['table'],'id',$index)->groupBy($index,"cname")->get();
                $item=&$uids[$index];
                foreach($res as $val){
                    $ids[$index][]=$val->{$index};
                    if($index==='state_id'){
                        $item[]=[
                            $index=>$val->{$index},
                            'cname'=>$val->cname,
                            'uids'=>json_decode($val->uids),
                            'cities'=>[],
                        ];
                        $citem=&$item[count($item)-1]['cities'];
                        $get=DB::table('admin_area')->selectRaw("city_id,cname,JSON_ARRAYAGG(uid) as uids")->where('type','c')->where('admin_area.state_id',$val->{$index})->join('cities','id','city_id')->groupBy('city_id','cname')->get();
                        foreach($get as $tmp){
                            $citem[]=[
                                'city_id'=>$tmp->city_id,
                                'cname'=>$tmp->cname,
                                'uids'=>json_decode($tmp->uids),
                                'regions'=>[],
                            ];
                            $ritem=&$citem[count($citem)-1]['regions'];
                            $get1=DB::table('admin_area')->selectRaw("region_id,cname,JSON_ARRAYAGG(uid) as uids")->where('type','r')->where('admin_area.city_id',$tmp->city_id)->join('regions','id','region_id')->groupBy('region_id','cname')->get();
                            foreach($get1 as $tmp1){
                                $ritem[]=[
                                    'region_id'=>$tmp1->region_id,
                                    'cname'=>$tmp1->cname,
                                    'uids'=>json_decode($tmp1->uids),
                                ];
                            }
                        }
                    }elseif($index==='city_id'){
                        $item[]=[
                            $index=>$val->{$index},
                            'cname'=>$val->cname,
                            'uids'=>json_decode($val->uids),
                            'regions'=>[],
                        ];
                        $ritem=&$item[count($item)-1]['regions'];
                        $get1=DB::table('admin_area')->selectRaw("region_id,cname,JSON_ARRAYAGG(uid) as uids")->where('type','r')->where('admin_area.city_id',$val->{$index})->join('regions','id','region_id')->groupBy('region_id','cname')->get();
                        foreach($get1 as $tmp1){
                            $ritem[]=[
                                'region_id'=>$tmp1->region_id,
                                'cname'=>$tmp1->cname,
                                'uids'=>json_decode($tmp1->uids),
                            ];
                        }
                    }
                }

            }
            $this->result->data=[
                'state'=>$suids,
                'city'=>$cuids,
                'region'=>$ruids,
            ];
            $this->successMsg="获取区域管理员信息成功！";
        }else{
            $this->errMsg="您不是管理员，没有权限查询您管理的区域信息！";
        }
        $this->getResult();
        return $this->result->toJson();
    }

    public function insert(Request $request){
        if (!$this->checkauth('s')){
            $this->errMsg="您不是管理员，没有权限查询您管理的区域信息！";
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

    public function del(Request $request){
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
}
