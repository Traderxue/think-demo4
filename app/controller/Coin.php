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

        $coin = new CoinModel([
            "type"=>$type,
            "time"=>$time
        ]);
        $res = $coin->save();

        if(!$res){
            return $this->result->error("新增数据失败");
        }
        return $this->result->success("新增数据成功",$res);
    }

    function page(Request $request){
        $page = $request->param("page",1);
        $pageSize = $request->param("pageSize",10);
        $type = $request->param("type");

        $list = CoinModel::where("type","like","%{$type}%")->paginate([
            "page"=>$page,
            "pageSize"=>$pageSize
        ]);
        return $this->result->success("获取数据成功",$list);
    }

}