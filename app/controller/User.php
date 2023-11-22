<?php
namespace app\controller;

use app\BaseController;
use think\Request;
use app\util\Res;
use app\model\User as UserModel;

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

        if($u){
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

    function login(Request $request){
        $username = $request->post("username");
        $pasword = $request->post("password");

        
    }

    function getList()
    {
        $list = UserModel::select();
        return $this->result->success("获取数据成功", $list);
    }

}