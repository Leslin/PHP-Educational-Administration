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
* cet
*/
class Cet extends Model
{	
	/**
	 * 绑定操作
	 */
	public function cetBind($data = [])
	{	

		$add['openid'] = session('openid');
		$add['name'] = $data['name'];
		$add['cetid'] = $data['cetid'];
		$add['type'] = $data['type'];
		$add['year'] = date('Y');
		$add['intime'] = date("Y-m-d H:i:s");
		$chcek = Db::name('cet')->where('openid',session('openid'))->where('year',$add['year'])->find();
		if(empty($chcek)){
			$insert = Db::name('cet')->insert($add);
			if($insert){
				return true;
			}else{
				return false;
			}
		}else{
			$update = Db::name('cet')->where('openid',session('openid'))->where('year',$add['year'])->update($add);
			if($update != 0 ){
				return true;
			}else{
				return false;
			}
		}

	}

	/**
	 * 获取用户信息
	 */
	public function getUserInfo()
	{
		return Db::name('cet')->where('openid',session('openid'))->where('year',date('Y'))->find();
	}
}