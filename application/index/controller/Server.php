<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Log;
use app\index\controller\Jssdk;
use app\index\controller\Factory;
use app\index\controller\Wechat;

/**
* 微信服务器端验证码
*/
class Server extends Controller
{	
	public function __construct()
    {
        $this->Wechat = Factory::getInstance(\app\index\controller\Wechat::class);
    }

	public function index()
	{   
		if(!empty(input('echostr'))){     //只有验证开发者模式url时候，才会带上这个参数，如果是正常交互，则这个参数不存在，
		    $this->Wechat->valid();
		}

		$type = $this->Wechat->getRev()->getRevType();    //接受用户发送过来的消息类型
		Log::info(json_encode($type));
		self::serverLog($type);    //日志存入数据
		switch ($type['MsgType']) {

			case Wechat::MSGTYPE_TEXT:     //文本类型
				
				switch (trim($type['Content'])) {

					case '成绩1':
						$this->getScore($type['FromUserName'],'bxq');
						$this->sendRed($type['FromUserName']);  //发送红包
						break;
					case '全部成绩1':
						$this->getScore($type['FromUserName'],'all');
						break;
					case '绩点':
						# code...
						break;
					case '重置':
						# code...
						break;
					case '登记照':
						# code...
						break;
					default:
						# code...
						break;
				}
				break;
			
			default:   //非文本类型
				# code...
				break;
		}

		echo "string";
	}

	/**
	 * 获取成绩
	 */
	public function getScore($openid = '',$type = '')
	{	
		$Server = Factory::getInstance(\app\index\model\Server::class);
		$userJwid = $Server->getJwid($openid);
		if(empty($userJwid)){
			$this->Wechat->news(self::newsTemplate('绑定学号','点击绑定学号密码查询升级',''))->reply();
			exit();
		}else{
			$Score = Factory::getInstance(\app\index\controller\Score::class);
			$result = $Score->getScore($type,$userJwid['jwid'],$userJwid['jwpwd']);
			$this->Wechat->news(self::newsTemplate('你的本学期成绩如下【点击查看所有成绩】',$result."\n".'成绩更新时间:'.date("Y-m-d H:i:s",time())."\n".'回复【全部成绩】查看所有成绩'."\n".'回复【重置】重新绑定',''))->reply();
		}
	}

	/**
	 * 发送红包
	 */
	public function sendRed($openid = '')
	{
		$config = config('red.is_open');
		if($config){     //开启红包
			$Server = Factory::getInstance(\app\index\model\Server::class);
			$userJwid = $Server->getJwid($openid);
			if(empty($userJwid)){     //学号为空
				return false;
			}

			$red = Factory::getInstance(\app\index\controller\Red::class);
		    $res = $red->RedBag($openid,120,'感谢一直对长大校园助手的关注，小小红包，表示心意！');
		    //发送成功
		    if($res['return_code'] == 'SUCCESS' && $res['result_code'] == 'SUCCESS' && !empty($res['send_listid'])){
				$Server->redLog($res);
				return true;
		    }

		}else{
			return true;
		}
	}

	/**
	 * 单图文模板组合，
	 */
	public static function newsTemplate($title='',$des = '',$url = '')
	{	
		$array = [
			"0"=>[

		   		'Title'=>$title,
		   		'Description'=>$des,
		   		'PicUrl'=>'',
		   		'Url'=> $url
	   		]
		];
		return $array;
	}

	public static function serverLog($data = [])
	{
		$Server = Factory::getInstance(\app\index\model\Server::class);
		$Server->serverLog($data);
	}
}