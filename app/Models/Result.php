<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Models;
use App\Models\Tag;

class Result{
    public $status=null;
    public $message=null;
    public $url=null;
    public $data=null;

    public function toJson(){
        setcookie("result",json_encode([
            'status'=>$this->status,
            'message'=>$this->message
        ],JSON_UNESCAPED_UNICODE),time()+2,'/');
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }
}
