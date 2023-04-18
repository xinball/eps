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
            $data = Continents::getlist($id);
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
    public function ping(Request $request){
        return count(Redis::keys("token_*"));
    }
}
