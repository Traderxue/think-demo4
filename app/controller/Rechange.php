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
            $user->save(["balance"->$balance + (float) $postData["money"]]);
            $user->update(['balance' => $user->balance + (float) $postData["money"]]);

            $rechange->save();

            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->result->error("添加失败" . $th);
        }
        return $this->result->success("添加数据成功", null);
    }

}