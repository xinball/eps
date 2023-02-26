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

class AddressController extends Controller
{
    //
    public function get(Request $request){
        $type=$request->get("type",null);
        $id=$request->get("id",null);
        $code=$request->get("code",null);
        $cname=$request->get("cname",null);
        $name=$request->get("name",null);
        $fcname=$request->get("fcname",null);
        $fname=$request->get("fname",null);
        if($type==='z'){
            return Continents::getInfo($id,$name,$cname);//lname cname
        }elseif($type==='g'){
            return Countries::getInfo($id,$name,$cname,$fname,$fcname,$code);//name cname lname fcname code
        }elseif($type==='s'){
            return States::getInfo($id,$name,$cname,$code);//lname cname code
        }elseif($type==='c'){
            return Cities::getInfo($id,$name,$cname,$code);//lname cname code_full
        }elseif($type==='r'){
            return Regions::getInfo($id,$name,$cname,$code);//lname cname code_full
        }
        return "";
    }
    public function getlist(Request $request){
        $type=$request->get("type",null);
        $id=$request->get("id");
        if($type==='z'){
            return Continents::getlist($id);
        }elseif($type==='g'){
            return Countries::getCountriesByZid($id);
        }elseif($type==='s'){
            return States::getStatesByGid($id);
        }elseif($type==='c'){
            return Cities::getCitiesBySid($id);
        }elseif($type==='r'){
            return Regions::getRegionsByCid($id);
        }
        return "";
    }
}
