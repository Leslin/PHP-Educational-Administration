<?php
namespace app\index\controller;

use think\Db;
use think\Cache;
use think\Controller;
/**
 *  微信自定义分享等
 *  存储所有token以及ticket
 *  所有信息存入Redis中
 */
class Jssdk extends Controller
{	
	public static $Access_token_prefix = 'access_token_';
	public static $Access_ticket_prefix = 'access_ticket_';
    public static $expire_time = 7000;
	
	/**
	 * 获取分享jssdk所有的签名包
	 */
	public static function getSignPackage()
	{	
		$wxConfig = Cache::get('wx_config');
		$jsapiTicket = self::getJsApiTicket();
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$timestamp = time();
		$nonceStr = self::createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
		  "appId"     => $wxConfig['appid'],
		  "nonceStr"  => $nonceStr,
		  "timestamp" => $timestamp,
		  "url"       => $url,
		  "signature" => $signature,
		  "rawString" => $string
		);

		return $signPackage; 
	}

	public static function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
		  $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	  }

	/**
	 * 获取ticket
	 */
	public static function getJsApiTicket() { 

		$wxConfig = Cache::get('wx_config');
		$CacheAccessTicket = Cache::get(self::$Access_ticket_prefix.$wxConfig['appid']);
        if($CacheAccessTicket == false){

        	$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".self::getAccessToken();
        	$return = json_decode(file_get_contents($url),true);
        	if(!empty($return['ticket'])){
        		$Cache = Cache::set(self::$Access_ticket_prefix.$wxConfig['appid'],$return['ticket'],self::$expire_time);  //存入redis中
        		if($Cache){
        			return $return['ticket'];
        		}else{
        			return false;
        		}
        	}
        }else{
        	return $CacheAccessTicket;
        }
	}

	/**
	 * 获取accesstoken
	 */
	public static function getAccessToken(){
		$wxConfig = Cache::get('wx_config');
    	$CacheAccessToken = Cache::get(self::$Access_token_prefix.$wxConfig['appid']);
    	if($CacheAccessToken == false){     //不存在

    		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wxConfig['appid']."&secret=".$wxConfig['appsecret'];
        	$return = json_decode(file_get_contents($url),true);
        	if(!empty($return['access_token'])){
        		$Cache = Cache::set(self::$Access_token_prefix.$wxConfig['appid'],$return['access_token'],self::$expire_time);  //存入redis中
        		if($Cache){
        			return $return['access_token'];
        		}else{
        			return false;
        		}
        	}
    	}else{

    		return $CacheAccessToken;
    	}
    }
}