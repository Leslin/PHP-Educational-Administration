<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Log;
use think\Request;

/**
* 个人中心
*/
class Center extends Base
{
	
	public function index()
	{	
		$userInfo = Cache::get('user_'.session('openid'));
		$this->assign('userinfo',$userInfo);
		return view();
	}
}