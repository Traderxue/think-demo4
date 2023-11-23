<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::group("/user",function(){

    Route::post("/add","user/add");

    Route::post("/login","user/login");

    Route::post("/update","user/update");

    Route::delete("/delete/:id","user/delete");

    Route::get("/page","user/page");

    Route::post("/transfer","user/transfer");

});

Route::group("/coin",function(){

    Route::post("/add","coin/add");

    Route::get("/page","coin/page");

    Route::delete("/delete/:id","coin/deleteById");

});

Route::group("/file",function(){
    Route::post("/upload","file/upload");
});

Route::group("/refund",function(){

    Route::post("/add","refund/add");

    Route::delete("/delete/:id","refund/deleteById");

    Route::get("/page","refund/page");

    Route::get("/list","refund/list");
});