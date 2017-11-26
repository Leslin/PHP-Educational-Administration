<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use app\index\controller\Jssdk;

/**
 * Base class for achievement
 * dalin
 * 2017-11-23
 */
class Score extends Controller
{
	
	/**
	 * @param $type 查询类型
	 * @param $jwid id
	 * @param $jwpwd password
	 * @return $score
	 */
	public static function getScore($type = '',$jwid = '',$jwpwd = '')
	{
		$cookie_file = tempnam('/www/wwwroot/temp', 'cookie');
		//模拟登陆并保存cookie
		$login_url="http://221.233.24.27:8080/login.aspx";

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$login_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$str=curl_exec($ch);
		curl_close($ch);
		$getView = '/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*)" \/>/i';
		$getEvent = '<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*)" \/>';
		preg_match($getView, $str, $viewArr);
		preg_match($getEvent, $str, $eventArr);
		$view=urlencode($viewArr[1]);
		$event=urlencode($eventArr[1]);

		$login="__VIEWSTATE={$view}&__EVENTVALIDATION={$event}&txtUid={$jwid}&txtPwd={$jwpwd}&selKind=1&btLogin=%B5%C7%C2%BD";
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$login_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $login);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_exec($ch);
		curl_close($ch);


		$ch=curl_init("http://221.233.24.27:8080/cjcx.aspx");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		$str=curl_exec($ch);
		curl_close($ch);
		$str=mb_convert_encoding($str, "utf-8", "gb2312");
		$getView = '/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*)" \/>/i';
		$getEvent = '<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*)" \/>';
		preg_match($getView, $str, $viewArr);
		preg_match($getEvent, $str, $eventArr);
		$view=urlencode($viewArr[1]);
		$event=urlencode($eventArr[1]);

		if($type=='bxq'){

			$cj="__EVENTTARGET=btXqcj&__EVENTARGUMENT=&__VIEWSTATE={$view}&__EVENTVALIDATION={$event}&selYear=2016&selTerm=2";

		}elseif($type=='all'){

			$cj="__EVENTTARGET=btAllcj&__EVENTARGUMENT=&__VIEWSTATE={$view}&__EVENTVALIDATION={$event}&selYear=2014&selTerm=1";

		}else{

			$cj="__EVENTTARGET=btXqcj&__EVENTARGUMENT=&__VIEWSTATE={$view}&__EVENTVALIDATION={$event}&selYear=2016&selTerm=2";
		}
		
		$ch=curl_init("http://221.233.24.27:8080/cjcx.aspx");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $cj);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		$str=curl_exec($ch);
		curl_close($ch);
		$str=mb_convert_encoding($str, "utf-8", "gb2312");
		$td = self::get_td_array($str);
		array_shift($td);
		$grade = '';
		foreach($td as $k=>$v){
			$grade .= trim($v[0])." : {$v[1]}\n";
		}

		return $grade;
	}

	public static function get_td_array($table) {
		$table = preg_replace("/<table[^>]*?>/is","",$table);
		$table = preg_replace("/<tr[^>]*?>/si","",$table);
		$table = preg_replace("/<td[^>]*?>/si","",$table);
		$table = str_replace("</tr>","{tr}",$table);
		$table = str_replace("</td>","{td}",$table);
		//去掉 HTML 标记
		$table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);
		//去掉空白字符
		$table = preg_replace("'([rn])[s]+'","",$table);
		$table = str_replace(" ","",$table);
		$table = str_replace(" ","",$table);
	
		$table = explode('{tr}', $table);
		array_pop($table);
		foreach ($table as $key=>$tr) {
			$td = explode('{td}', $tr);
			$td = explode('{td}', $tr);
			array_pop($td);
			$td_array[] = $td;
		}
		return $td_array;
	}
}