<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Log;
use think\Request;
use app\index\model\Server as ServerModel;

/**
* 个人中心
*/
class Center extends Base
{
	public function _initialize() {

		parent::_initialize();
		$this->user = ServerModel::getJwid(session('openid'));
		if(empty($this->user)){
			$this->redirect('Bind/index');
		}
	}
	public function index()
	{	
		$userInfo = Cache::get('user_'.session('openid'));
		$this->assign('userinfo',$userInfo);
		$this->assign('user',$this->user);
		return view();
	}

	public function bxq_score()
	{	
		
		$this->assign('score',self::getScore('bxq'));
		return view();
	}

	public function all_score()
	{	
		$this->assign('score',self::getScore('all'));
		return view();
	}

	public function photo()
	{	
		$this->assign('user',$this->user);
		return view();
	}

	public static function getScore($type = '')
	{	
		$userInfo = ServerModel::getJwid(session('openid'));
		$score = Scorecenter::getScore($type,$userInfo['jwid'],$userInfo['jwpwd']);
		$keys = array_keys($score);
		$temp = [];
		foreach ($keys as $key => $value) {
			$temp[$key]['team'] = $value;
			$temp[$key]['info'] = $score[$value];
		}
		return $temp;
	}
}