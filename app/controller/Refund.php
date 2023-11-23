<?php
namespace app\controller;

use app\BaseController;
use think\Request;
use app\model\Refund as RefundModel;
use app\util\Res;

class Refund extends BaseController{
    private $result;
    public function __construct(){
        $this->result = new Res();
    }

    function add(Request $request){
        $postData = $request->post();
        $refund = new RefundModel([
            "refund_time"=>date("Y-m-d H:i:s"),
            "money"=>$postData["money"],
            "account"=>$postData["account"],
            "state"=>$postData["1"]
        ]);
        $res = $refund->save();

        if(!$res){
            return $this->result->error("添加数据失败");
        }
        return $this->result->success("添加数据成功",$res);
    }

    function deleteById($id){
        $res = RefundModel::where("id",$id)->delete();
        if(!$res){
            return $this->result->error("删除失败");
        }
        return $this->result->success("删除成功",$res);
    }

    function page(Request $request){
        $page = $request->param("page",1);
        $pageSize = $request->param("pageSize",10);
        $account = $request->param("account");

        $list = RefundModel::where("accound","like","%{$account}%")->paginate([
            "page"=>$page,
            "list_rows"=>$pageSize
        ]);

        return $this->result->success("获取数据成功",$list);
    }

}