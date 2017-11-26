<?php
namespace app\index\model;

use think\Cache;
use think\Db;
use think\Controller;
use think\Log;
use think\Model;
use app\index\controller\Jssdk;
use app\index\controller\Factory;

/**
* 用户模型
*/
class User extends Model
{	
	/**
	 * 新增用户
	 */
	public function userAdd($openid = '',$name = '',$jwid = '',$jwpwd = '')
	{
		$user['openid']  = isset($openid) ? $openid : session('openid');
		$user['name']    = $name;
 		$user['jwid']    = $jwid;
		$user['jwpwd']   = $jwpwd;
		$user['time']    = date("Y-m-d H:i:s");
		$add = Db::name('user')->insert($user);
		if($add){
			return true;
		}else{
			return false;
		}
	}
}