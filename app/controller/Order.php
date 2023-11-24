<?php
namespace app\controller;

use app\BaseController;
use think\Request;
use app\model\Order as OrderModel;
use app\model\User as UserModel;
use app\util\Res;
use think\facade\Db;

// 订单表
class Order extends BaseController
{
    protected $result;
    function __construct()
    {
        $this->result = new Res();
    }

    function add(Request $request)
    {
        $postData = $request->post();
        $order = new OrderModel([
            "time" => date("Y-m-d H:i:s"),
            "direction" => $postData["direction"],
            "amount" => $postData["amount"],
            "profit" => $postData["profit"],
            "result" => $postData["result"],
            "u_id" => $postData["u_id"]
        ]);

        Db::startTrans();
        try {
            $order->save();

            $user = UserModel::where("id", $postData["u_id"])->find();

            if ($postData["result"] == 1) {
                $user->save(["balance" => (float) $user->balance + (float) $postData["profit"]]);
            } else {
                $user->save(["balance" => (float) $user->balance - (float) $postData["profit"]]);
            }
            Db::commit();
        } catch (\Throwable $th) {
            //throw $th;
            Db::rollback();
            return $this->result->error("添加数据失败" . $th);
        }
        return $this->result->success("添加数据成功", null);
    }

    function edit(Request $request)
    {
        $postData = $request->post();
        $order = OrderModel::where("id", $postData["id"])->find();

        Db::startTrans();
        try {
            $order->save([
                "profit" => $postData["profit"],
                "result" => $postData["result"],
                "u_id" => $postData["u_id"]
            ]);

            $user = UserModel::where("id", $postData["u_id"])->find();

            if ($postData["result"] == 1) {
                $user->save(["balance" => (float) $user->balance + $postData["profit"]]);
            } else {
                $user->save(["balance" => (float) $user->balance - $postData["profit"]]);
            }

            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->result->error("编辑失败" . $th);
        }
        return $this->result->success("编辑成功", null);

    }

    function page(Request $request){
        $page = $request->param("page",1);
        $pageSize = $request->param("pageSize",10);
        $u_id  =$request->param("u_id");

        $list = OrderModel::where("u_id","like","%{$u_id}%")->paginate([
            "page"=>$page,
            "list_rows"=>$pageSize
        ]);
        return $this->result->success("获取数据成功",$list);
    }

    function getByuid($u_id){

        $list = OrderModel::where("u_id",$u_id)->select();

        return $this->result->success("获取数据成功",$list);
    }

    function deleteById(Request $request){
        $id = $request->param("id");

        $res = OrderModel::where("id",$id)->delete();

        if($res){
            return $this->result->success("删除成功",null);
        }
        return $this->result->error("删除失败");
    }


}