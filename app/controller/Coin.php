<?php
namespace app\controller;

use think\Request;
use app\BaseController;
use app\model\Coin as CoinModel;
use app\util\Res;

class Coin extends BaseController
{
    private $result;

    public function __construct()
    {
        $this->result = new Res();
    }

    public function add(Request $request)
    {
        $type = $request->post("type");
        $time = date("Y-m-d H:i:s");

        $c = CoinModel::where("type", $type)->find();

        if ($c) {
            return $this->result->error("币种已存在");
        }

        $coin = new CoinModel([
            "type" => $type,
            "add_time" => $time
        ]);

        try {
            $res = $coin->save();
        } catch (\Exception $e) {
            return $this->result->error("新增数据失败: " . $e->getMessage());
        }

        if (!$res) {
            return $this->result->error("新增数据失败");
        }
        return $this->result->success("新增数据成功", $res);
    }

    function page(Request $request)
    {
        $page = $request->param("page", 1);
        $pageSize = $request->param("pageSize", 10);
        $type = $request->param("type");

        $list = CoinModel::where("type", "like", "%{$type}%")->paginate([
            "page" => $page,
            "pageSize" => $pageSize
        ]);
        return $this->result->success("获取数据成功", $list);
    }

    function deleteById($id)
    {
        $res = CoinModel::where("id", $id)->delete();

        if($res){
            return $this->result->success("删除成功",$res);
        }
        return $this->result->error("删除失败");

    }

}