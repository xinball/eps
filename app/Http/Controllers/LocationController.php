<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    //
    public function listview(Request $request){
        return view('location.list')->with('lactive',true);
    }
    public function indexview(Request $request,$sid){
        if($this->ladmin!==null){
            $result=$this->aget($request,$lid);
        }else{
            $result=$this->get($request,$lid);
        }
        return view('location.index')->with('lactive',true)->with('result',$result);
    }

}
