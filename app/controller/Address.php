<?php

namespace app\controller;

use app\BaseController;
use think\Request;
use app\model\Address as AddressModel;
use app\util\Res;

class Address extends BaseController{
    private $result;

    function __construct(){
        $this->result = new Res();
    }

    function add(Request $request){
        $address = $request->post("address");
        $time = date("Y-m-d H:i:s");
        $type = $request->post("type");

        $address = new AddressModel([
            "address"=> $address,
            "add_time"=>$time,
            "type"=>$type            
        ]);

        $address->save();

        return $this->result->success("添加数据成功",null);
    }
}