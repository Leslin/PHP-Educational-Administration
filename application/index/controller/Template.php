<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Request;
use app\index\controller\Jssdk;

/**
* 发送模板信息
*/
class Template extends Controller
{
	public static $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=';

	/**
	 * 通用模板信息发送
	 * 调用方式：self::sendTemplate('oTdU_wh_nCmPYFmcsYYEta5duC_k','SkKEJgMkKlB_BAVzlKdgLQUNzTYuLTtvRprDBY6wwJc','http://baidu.com','首条记录','备注',['我是第一','我的第二']);
	 */
	public static function sendTemplate($touser = '',$template_id = '',$url = '',$first = '',$remark = '',$keyword = [])
	{	
		$data = [
			'touser'      => $touser,
			'template_id' => $template_id,
			'url'         => $url,
			'topcolor'    => '#173177',
			'data'        => [
				'first' => [
					'value' => $first,
					'color' => '#173177'
				],
				'remark' => [
					'value' => $remark,
					'color' => '#EE3B3B'
				]
			]
		];
		$data['data'] = array_merge(self::assemble($keyword),$data['data']);//组装模板信息keywords
		return json_decode(self::sendTemplateMessage($data),true);
	}

	/**
	 * 拼接模板信息，含有多个keyword的情况
	 */
	public static function assemble($keyword = [])
	{	
		$array = [];  //外层数组
		foreach ($keyword as $k => $v) {
			$array['keyword'.($k+1)] = [
				'value' => $v,
				'color' => '#173177'
			];
		}
		return $array;
	}

	public static function sendTemplateMessage($data = '')
	{
		return self::http_post(self::$url.Jssdk::getAccessToken(),self::json_encode($data));
	}

	public static function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	/**
	 * 微信api不支持中文转义的json结构
	 * @param array $arr
	 */
    public static function json_encode($arr) {
		if (count($arr) == 0) return "[]";
		$parts = array ();
		$is_list = false;
		//Find out if the given array is a numerical array
		$keys = array_keys ( $arr );
		$max_length = count ( $arr ) - 1;
		if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
			$is_list = true;
			for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
				if ($i != $keys [$i]) { //A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}
		foreach ( $arr as $key => $value ) {
			if (is_array ( $value )) { //Custom handling for arrays
				if ($is_list)
					$parts [] = self::json_encode ( $value ); /* :RECURSION: */
				else
					$parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
			} else {
				$str = '';
				if (! $is_list)
					$str = '"' . $key . '":';
				//Custom handling for multiple data types
				if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
					$str .= $value; //Numbers
				elseif ($value === false)
				$str .= 'false'; //The booleans
				elseif ($value === true)
				$str .= 'true';
				else
					$str .= '"' . addslashes ( $value ) . '"'; //All other things
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)
				$parts [] = $str;
			}
		}
		$json = implode ( ',', $parts );
		if ($is_list)
			return '[' . $json . ']'; //Return numerical JSON
		return '{' . $json . '}'; //Return associative JSON
	}

}