<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Continents;
use App\Models\Countries;
use App\Models\States;
use App\Models\Cities;
use App\Models\Regions;
use App\Models\Operation;
class SystemController extends Controller
{
    //
    public function getaddr(Request $request){
        $type=$request->get("type",null);
        $id=$request->get("id",null);
        $code=$request->get("code",null);
        $cname=$request->get("cname",null);
        $name=$request->get("name",null);
        $fcname=$request->get("fcname",null);
        $fname=$request->get("fname",null);
        if($type==='z'){
            $data = Continents::getInfo($id,$name,$cname);//lname cname
        }elseif($type==='g'){
            $data = Countries::getInfo($id,$name,$cname,$fname,$fcname,$code);//name cname lname fcname code
        }elseif($type==='s'){
            $data = States::getInfo($id,$name,$cname,$code);//lname cname code
        }elseif($type==='c'){
            $data = Cities::getInfo($id,$name,$cname,$code);//lname cname code_full
        }elseif($type==='r'){
            $data = Regions::getInfo($id,$name,$cname,$code);//lname cname code_full
        }else{
            $data = "{}";
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    public function getaddrlist(Request $request){
        $type=$request->get("type",null);
        $id=$request->get("id");
        if($type==='z'){
            $data = Continents::getContinents();
        }elseif($type==='g'){
            $data = Countries::getCountriesByZid($id);
        }elseif($type==='s'){
            $data = States::getStatesByGid($id);
        }elseif($type==='c'){
            $data = Cities::getCitiesBySid($id);
        }elseif($type==='r'){
            $data = Regions::getRegionsByCid($id);
        }else{
            $data = "[]";
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    public function getOperation(Request $request){
        $params=$request->all();
        if ((isset($params['uid'])&&$this->luser&&$this->luser->uid.''===$params['uid'])||$this->ladmin){
            $sql=Operation::select("oid","user.uid","user.uname","otype","oip","orequest","oresult","otime","oinfo");
            $where=[];
            foreach ($params as $key=>$v){
                if($v!==""&&$v!==null){
                    if($key==="oid"||$key==="oip"){
                        $where[]=[$key,'=',$v];
                    }elseif($key==="type"&&isset($this->config_operation['type'][$v])){
                        $where[]=['otype','=',$v];
                    }elseif($key==="uid"){
                        if($this->ladmin){
                            $where[]=["operation.uid",'=',$v];
                        }else{
                            $where[]=["operation.uid",'=',$this->luser->uid];
                        }
                    }elseif($key==="status"&&($v>=1&&$v<=4)){
                        $where[]=['oresult->status','=',$v];
                    }elseif($key==="device"||$key==="browser"||$key==="platform"||$key==="browserv"||$key==="platformv"){
                        $where[]=['oinfo->'.$key,'like','%'.$v.'%'];
                    }elseif($key==="isDesktop"||$key==="isTablet"||$key==="isPhone"){
                        $where[]=['oinfo->'.$key,'=',$v];
                    }elseif($key==="ostart"&&strtotime($v)){
                        $where[]=['otime','>=',$v];
                    }elseif($key==="oend"&&strtotime($v)){
                        $where[]=['otime','<=',$v];
                    }
                }
            }
            $sql=$sql->where($where)->join('user','user.uid','operation.uid');
            // echo $sql->toSql();
            $orderPara = $params['order']??"oid";
            $desc = $params['desc']??"0";
            if($desc==='1'){
                $sql=$sql->orderByDesc('oid');
            }else{
                $sql=$sql->orderBy('oid');
            }
            $operations=$sql->paginate($this->config_operation['listnum'])->withQueryString();
            $this->listMsg($operations);
            $this->result->data=[
                'operations'=>$operations,
            ];
        }else{
            $this->errMsg="您没有权限查看操作日志，请重新登录！";
        }
        $this->getResult();
        return $this->result->toJson();
    }
    public function ping(Request $request){
        return count(Redis::keys("token_*"));
    }
}
