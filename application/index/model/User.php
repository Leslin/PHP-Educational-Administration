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
	public function userAdd($openid = '',$name = '',$jwid = '',$jwpwd = '',$realname = '',$class = '')
	{
		$user['openid']      = isset($openid) ? $openid : session('openid');
		$user['name']        = $name;
		$user['realname']    = $realname;
		$user['class']       = $class;
 		$user['jwid']        = $jwid;
		$user['jwpwd']       = md5($jwpwd.time());
		$user['time']        = date("Y-m-d H:i:s");
		$check = Db::name('user')->where('openid',$openid)->find();
		if(empty($check)){
			$add = Db::name('user')->insert($user);
			if($add){
				return true;
			}else{
				return false;
			}
		}else{
			$update = Db::name('user')->where('openid',$openid)->update($user);
			if($update != 0){
				return true;
			}else{
				return false;
			}
		}
		
	}

	public static function message($message = '')
	{
		$data['openid'] = session('openid');
		$data['content'] = $message;
		$data['time'] = date("Y-m-d H:i:s");
		$insert = Db::name('message')->insert($data);
		if($insert){
			return true;
		}else{
			return false;
		}
	}
}