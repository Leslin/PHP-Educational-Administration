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
	 * 获取学号密码
	 */
	public static function getJwid($openid = '')
	{
		$jwInfo = Db::name('user')->where('openid',$openid)->find();
		return $jwInfo;
	}
}