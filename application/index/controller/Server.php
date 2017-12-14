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

					case '成绩':
						$this->getScore($type['FromUserName'],'bxq');
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
			$this->Wechat->news(self::newsTemplate('绑定学号','点击绑定学号密码查询成绩','http://cd.cdhand.com/public/index.php/index/bind/index'))->reply();
			exit();
		}else{
			$Score = Factory::getInstance(\app\index\controller\Score::class);
			$result = $Score->getScore($type,$userJwid['jwid'],$userJwid['jwpwd']);
			$this->Wechat->news(self::newsTemplate('你的本学期成绩如下【点击查看所有成绩】',$result."\n".'成绩更新时间:'.date("Y-m-d H:i:s",time())."\n".'回复【全部成绩】查看所有成绩'."\n".'回复【重置】重新绑定','http://cd.cdhand.com/jf/api.php/All'))->reply();
			$this->sendRed($userJwid['openid']);  //发送红包
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
				$this->Wechat->news(self::newsTemplate('绑定学号','点击绑定学号密码查询成绩','http://cd.cdhand.com/public/index.php/index/bind/index'))->reply();
				exit();
			}

			$isCheckRed = $Server->getRedLog($openid,$userJwid['jwid']);

			if($isCheckRed){   //满足发红包要求
				$year = substr($userJwid['jwid'], 0,4);//取得学号
				$ischeck = self::get_rand($year);
				if($ischeck){
					$red = Factory::getInstance(\app\index\controller\Red::class);
				    $res = $red->RedBag($openid,rand(100,120),'感谢一直对长大校园助手的关注，小小红包，表示心意！');
				    //发送成功
				    if($res['return_code'] == 'SUCCESS' && $res['result_code'] == 'SUCCESS' && !empty($res['send_listid'])){
						$Server->redLog($res);
				    }
				}else{
					Template::sendCustomMessage($openid,'很遗憾，本次查询未获得现金红包。请再次查询成绩，领取现金红包哟');
				}
				
			}else{
				//Template::sendCustomMessage($openid,'红包领取失败,你已经领过红包或学号已被他人领取红包');
			}

		}else{
			
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

	/**
	 * 是否给改用户发送红包
	 */
	public function isSendRed($jwid = '')
	{
		$year = substr($jwid, 0,4);//取得学号

	}

	public static function get_rand($jwid = '') {
		
		if($jwid == 2017 ){  //2017级的直接送
	    	return true;
	    }
		$proArr = array('2016'=>50,'2015'=>20,'2014'=>10,'2013'=>10,'2012'=>5,'2011'=>5);
	    $result = ''; 
	    //概率数组的总概率精度
	    $proSum = array_sum($proArr); 

	    //概率数组循环
	    foreach ($proArr as $key => $proCur) { 
	        $randNum = mt_rand(1, $proSum); 
	        if ($randNum <= $proCur) { 
	            $result = $key; 
	            break; 
	        } else { 
	            $proSum -= $proCur; 
	        } 
	    } 
	    unset ($proArr);
	    if($jwid == $result){
	    	return true;
	    }else{
	    	return false;
	    }
	}
}