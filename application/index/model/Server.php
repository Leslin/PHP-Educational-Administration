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
* server模型
*/
class server extends Model
{
	/**
	 * server端查询日志
	 */
	public function serverLog($data = [])
	{ 
		$log['tousername'] = $data['ToUserName'];
		$log['fromusername'] = $data['FromUserName'];
		$log['createtime'] = $data['CreateTime'];
		$log['msgtype'] = $data['MsgType'];
		$log['content'] = $data['Content'];
		$log['msgid'] = $data['MsgId'];
		$log['intime'] = date("Y-m-d H:i:s",time());
		return Db::name('serverlog')->insert($log);
	}

	/**
	 * 红包记录
	 */
	public function redLog($data = [])
	{	
		$userInfo = self::getJwid($data['re_openid']);
		$log['openid'] = $data['re_openid'];
		$log['jwid']   = $userInfo['jwid'];
		$log['amount'] = $data['total_amount']/100;
		$log['intime'] = date("Y-m-d H:i:s",time());
		return Db::name('redlog')->insert($log);
	}

	/**
	 * 获取照片
	 */
	public static function getPhoto()
	{
		$list = Db::query("SELECT * FROM `edu_bangding`  AS t1 JOIN (SELECT ROUND(RAND() * (SELECT MAX(id) FROM `edu_bangding`)) AS id) AS t2 WHERE t1.id >= t2.id  LIMIT 1");
		return $list[0];
	}

	/**
	 * 获取学号密码
	 */
	public static function getJwid($openid = '')
	{
		$jwInfo = Db::name('user')->where('openid',$openid)->find();
		return $jwInfo;
	}

	/**
	 * 根据openid或学号查找是否发放过红包
	 */
	public static function getRedLog($openid = '',$jwid = '')
	{
		$checkOpenid = Db::name('redlog')->where('openid',$openid)->find();
		if(!empty($checkOpenid)){
			return false;
		}

		$checkJwid = Db::name('redlog')->where('jwid',$jwid)->find();
		if(!empty($checkJwid)){
			return false;
		}

		return true;
	}

	/**
	 * 重置学号
	 */
	public static function reset($openid = '')
	{
		$delete = Db::name('user')->where('openid',$openid)->delete();
		if($delete != 0){
			return true;
		}else{
			return false;
		}
	}
}