<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Request;
use app\index\model\Server as ServerModel;
/**
 * Base class for achievement
 * dalin
 * 2017-11-23
 */
class Clear extends Controller
{	
	/**
	 * 清空相关缓存
	 */
	public function clear()
	{	
		if (Request::instance()->isAjax()){
			$userInfo = ServerModel::getJwid(session('openid'));
			$res = Cache::rm($userInfo['jwid'].'_bxq');
			Cache::rm($userInfo['jwid'].'_all');
			Cache::rm($userInfo['jwid'].'_bxq_center');
			Cache::rm($userInfo['jwid'].'_all_center');
			return ['status'=>1,'msg'=>'清除成功'];
		}
		
	}
}