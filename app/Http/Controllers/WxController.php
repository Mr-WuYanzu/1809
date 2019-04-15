<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\WxUser;
use GuzzleHttp\Client;
class WxController extends Controller
{
    //微信接口第一次访问
    public function Valid(){
    	echo "echostr";
    }
    //微信推送数据
    public function WxEvent(){

    	$data=file_get_contents("php://input");
    	$str=date('Ymd h:i:s').$data."\n";
    	is_dir('logs') or mkdir('logs',0777,true);
    	file_put_contents('logs/wx_event.log',$str,FILE_APPEND);
    	$obj=simplexml_load_string($data);
    	$openid=$obj->FromUserName;
    	$event=$obj->Event;
    	if($event=='subscribe'){
    		$res=WxUser::where('openid',$openid)->first();
    		if(!$res){
    			$data=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->getAccessToken()."&openid=".$openid."&lang=zh_CN");
    			$data=json_decode($data,true);
    			dd($data);
    			$info=[
    				'openid'=>$openid,
    				'nickname'=>$data['nickname'],
    				'sex'=>$data['sex'],
    				'city'=>$data['city'],
    				'province'=>$data['province'],
    				'country'=>$data['country'],
    				'headimgurl'=>$data['headimgurl'],
    				'subscribe_time'=>$data['subscribe_time'],
    				'subscribe_scene'=>$data['subscribe_scene']
    			];
    			$res=WxUser::insert($info);
    		}
    	}
    	
    }
    //获取access_token
    public function getAccessToken(){
    	$str=Redis::get('token');
    	if(!$str){
	    	$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxe5ff29e2590e9cef&secret=02f770d9872fdf95de605f22c783fe46";
	    	$token=file_get_contents($url);
	    	$arr=json_decode($token);
	    	$str=$arr->access_token;
	    	Redis::set('token',$str);
	    	Redis::expire('token',3600);
    	}
    	return $str;
    }
    //创建菜单
    public function create_menu(){
    	$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->getAccessToken();
    	$data=[
    		'button'=>[
    			[
    				'type'=>'click',
    				'name'=>'点一下',
    				'key'=>'hhhhhhh'
    			],
    			[
    				'name'=>'菜单',
    				'sub_button'=>[
    					[
    						'type'=>'view',
    						'name'=>'搜狗',
    						'url'=>'http://www.sougou.com'
    					],
    					[
    						'name'=>'发送位置',
    						'type'=>'location_select',
    						'key'=>'rselfmenu_2_0',
    					],
    				],
    				'name'=>'发图',
    				'sub_button'=>[
    					'type'=>'pic_sysphoto',
    					'name'=>'拍照发图',
    					'key'=>'rselfmenu_1_0',
    					'sub_button'=>[ ],
    				],
    			],
    		],
    	];
    	$arr=json_encode($data,JSON_UNESCAPED_UNICODE);
    	$client=new Client();
    	$res=$client->respons('POST',$url,[
    		'body'=>$arr
    	]);
    	$arr=$res->getBody();
    	$arr=json_decode($arr,true);
    	if($arr['errcode']>0){
    		echo "创建菜单成功";
    	}else{
    		echo "创建菜单失败";
    	}
    }
}
