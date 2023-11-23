<?php

namespace app\controller;

use app\BaseController;
use think\Request;
use app\model\Address as AddressModel;
use app\util\Res;

class Address extends BaseController
{
    private $result;

    function __construct()
    {
        $this->result = new Res();
    }

    function add(Request $request)
    {
        $address = $request->post("address");
        $time = date("Y-m-d H:i:s");
        $type = $request->post("type");

        $address = new AddressModel([
            "address" => $address,
            "update_time" => $time,
            "type" => $type
        ]);

        $address->save();

        return $this->result->success("添加数据成功", null);
    }

    function edit(Request $request)
    {
        $postData = $request->post();
        $address = AddressModel::where("type", $postData["type"])->find();
        $res = $address->save([
            "address" => $postData["address"],
            "add_time"=>date("Y-m-d H:i:s")
        ]);

        if(!$res){
            return $this->result->error("编辑数据失败");
        }
        return $this->result->success("编辑数据成功",$res);
    }

    function get(){
        $list = AddressModel::select();
        return $this->result->success("获取数据成功",$list);
    }
}