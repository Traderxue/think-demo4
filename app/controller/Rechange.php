<?php
namespace app\controller;

use app\BaseController;
use think\Request;
use app\model\Rechange as RechangeModel;
use app\model\User as UserModel;
use app\util\Res;
use think\facade\Db;

// 充值
class Rechange extends BaseController
{
    protected $result;
    public function __construct()
    {
        $this->result = new Res();
    }

    function add(Request $request)
    {
        $postData = $request->post();
        $rechange = new RechangeModel([
            "rechange_time" => date("Y-m-d H:i:s"),
            "money" => $postData["money"],
            "channel" => $postData["channel"],
            "u_id" => $postData["u_id"]
        ]);

        Db::startTrans();
        try {
            $user = UserModel::where("id", $postData["u_id"])->find();

            $balance = (float) $user->balance;

            $user->save(['balance' => $balance + (float) $postData["money"]]);

            $rechange->save();

            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->result->error("充值失败" . $th);
        }
        return $this->result->success("充值成功", null);
    }

    function page(Request $request){
        $page = $request->param("page",1);
        $pageSize = $request->param("pageSize",10);
        $u_id = $request->param("u_id");

        $list = RechangeModel::where("u_id",$u_id)->paginate([
            "page"=>$page,
            "list_rows"=>$pageSize
        ]);

        return $this->result->success("获取数据成功",$list);
    }

    function getByUserId($u_id){
        $rechange = RechangeModel::where("u_id",$u_id)->select();
        return $this->result->success("获取数据成功",$rechange);
    }

    function deleteById($id){
        $res = RechangeModel::destroy($id);
        return $this->result->success("删除数据成功",$res);
    }

}