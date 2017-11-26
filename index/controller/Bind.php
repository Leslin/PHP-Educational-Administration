<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Request;
use app\index\controller\Jssdk;

/**
* Bind
*/
class Bind extends Base
{	
	public function index()
	{
		if (Request::instance()->isAjax()){
			$name = input('post.name');
			$jwid = input('post.xuehao');
			$jwpwd = input('post.mima');
			if($this->userBind(session('openid'),$name,$jwid,$jwpwd)){
				return ['status'=>1,'msg'=>'绑定成功，回复【成绩】查询'];
			}else{
				return ['status'=>0,'msg'=>'账号密码错误或教务处异常'];
			}
		}
		$Server = Factory::getInstance(\app\index\model\Server::class);
		$userJwid = $Server->getJwid(session('openid'));
		if(!empty($userJwid)){
			$this->success('你已绑定学号：'.$userJwid['jwid']);
		}
		$getSignPackage = Jssdk::getSignPackage();  //分享所需的jssdk签名包
		$this->assign('getSignPackage',$getSignPackage);
		return $this->fetch();
	}

	public function userBind($openid = '',$name = '',$jwid = '',$jwpwd = '')
	{	

		$result = self::cookie($jwid,$jwpwd);
		if($result){
			$User = Factory::getInstance(\app\index\model\User::class);
			$userAdd = $User->userAdd($openid,$name,$jwid,$jwpwd);
			if($userAdd){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * 获取cookie
	 */
	public static function cookie($jwid = '',$jwpwd = ''){

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
		$str=curl_exec($ch);
		curl_close($ch);
		$str=mb_convert_encoding($str, "utf-8", "gb2312");
		if(preg_match("/当前用户/",$str)){
			return true;
		}else{
			return false;
		}
	}
}