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

		
	}
}