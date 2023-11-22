<?php
namespace app\controller;

use app\BaseController;
use think\Request;
use app\util\Res;
use app\model\User as UserModel;
use think\facade\Db;

class User extends BaseController
{
    private $result;
    function __construct()
    {
        $this->result = new Res();
    }

    function add(Request $request)
    {
        $postData = $request->post();

        $u = UserModel::where("username", $postData["username"])->find();

        if ($u) {
            return $this->result->error("添加失败,该用户已存在");
        }

        $user = new UserModel([
            'username' => $postData['username'],
            'password' => password_hash($postData['password'], PASSWORD_DEFAULT),
            'create_time' => date("Y-m-d H:i:s"),
            'update_time' => date("Y-m-d H:i:s"),
            'balance' => $postData['balance']
        ]);
        $res = $user->save();
        if (!$res) {
            return $this->result->error("添加数据失败");
        }
        return $this->result->success("添加数据成功", $res);
    }

    function login(Request $request)
    {
        $username = $request->post("username");
        $password = $request->post("password");

        $user = UserModel::where("username", $username)->find();

        if (!$user) {
            return $this->result->error("用户不存在");
        }

        if (password_verify($password, $user->password)) {
            return $this->result->success("登录成功", $user);
        }
        return $this->result->error("用户名或密码错误");
    }

    function update(Request $request)
    {
        $id = $request->post("id");
        $user = UserModel::where("id", $id)->find();
        $update_time = date("Y:m:d H:i:s");
        $balance = $request->post("balance");

        $res = $user->save(['update_time' => $update_time, "balance" => $balance]);

        if (!$res) {
            return $this->result->error("更新失败");
        }
        return $this->result->success("更新成功", $user);

    }

    function delete($id)
    {
        $res = UserModel::where("id", $id)->delete();
        if ($res) {
            return $this->result->success("删除数据成功", null);
        }
        return $this->result->error("删除数据失败");
    }

    function page(Request $request)
    {
        $page = $request->param("page", 1);
        $pageSize = $request->param("pageSize", 10);
        $username = $request->param("username");

        $list = UserModel::where("username", "like", "%$username%")->paginate([
            "page" => $page,
            "list_rows" => $pageSize
        ]);
        return $this->result->success("获取数据成功", $list);
    }

    function transfer(Request $request)
    {
        $from_id = $request->post("from_id");
        $to_id = $request->post("to_id");
        $amount = (float) $request->post("amount");

        Db::startTrans();
        try {
            $from_user = UserModel::where("id", $from_id)->find();
            $from_balance = (float) $from_user->balance;

            $to_user = UserModel::where("id", $to_id)->find();
            $to_balance = (float) $to_user->balance;

            if ($from_balance < $amount) {
                return $this->result->error("余额不足");
            }

            $from_user->save(["balance" => $from_balance - $amount]);
            $to_user->save(["balance" => $to_balance + $amount]);

            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->result->error("转账失败");
            //throw $th;
        }

        return $this->result->success("转账成功", null);
    }

}