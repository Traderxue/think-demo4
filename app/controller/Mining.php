<?php
namespace app\controller;

use app\BaseController;
use think\Request;
use app\model\Mining as MiningModel;
use app\util\Res;

class Mining extends BaseController{
    private $result;

    function __construct(){
        $this->result = new Res();
    }

    function add(Request $request){
        $postData = $request->post();
        $mining = new MiningModel([
            "amount"=>$postData["amount"],
            "update_time"=>date("Y-m-d H:i:s"),
            "time"=>$postData["time"],
            "rate"=>$postData["rate"],
            "type"=>$postData["type"]
        ]);

        $res = $mining->save();
        if($res){
            return $this->result->success("添加数据成功",$res);
        }
        return $this->result->error("添加数据失败");
    } 

    function edit(Request $reqeust){
        $postData = $reqeust->post();
        $mining = MiningModel::where("id",$postData["id"])->find();
        $res = $mining->save([
             "amount"=>$postData["amount"],
             "update_time" => date("Y-m-d H:i:s"),
             "time"=>$postData["time"],
             "rate"=>$postData["rate"],
             "type"=>$postData["type"]
        ]);
        if($res){
            return $this->result->success("编辑成功",$res);
        }
        return $this->result->error("编辑失败");
    }

    function deleteById($id){
        $res = MiningModel::where("id",$id)->delete();
        if(!$res){
            return $this->result->error("删除数据失败");
        }
        return $this->result->success("删除数据成功",null);
    }

    function page(Request $request){
        $page = $request->param("page",1);
        $pageSize = $request->param("pageSize",10);
        $type = $request->param("type");

        $list = MiningModel::where("type","like","%{$type}%")->paginate([
            "page"=>$page,
            "list_rows"=>$pageSize
        ]);

        return $this->result->success("获取数据成功",$list);
    }

    function list(){
        $list = MiningModel::select();
        return $this->result->success("获取数据成功",$list);
    }
}
