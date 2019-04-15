<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/phpinfo', function () {
    phpinfo();
});
//第一次调用接口
Route::get('/weixin/valid',"WxController@Valid");
//微信推送消息
Route::post('/weixin/valid',"WxController@WxEvent");
//获取access_token
Route::get('/weixin/token',"WxController@getAccessToken");
//创建菜单
Route::get('/weixin/create_menu',"WxController@create_menu");