<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Log;
use think\Request;
use app\index\model\Server as ServerModel;
use app\index\model\User as UserModel;

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
		if (Request::instance()->isAjax()){
			$photo = ServerModel::getPhoto();
			if(!empty($photo)){
				$jwid = $photo['jwid'];
				$url = "http://jwc2.yangtzeu.edu.cn:8080/photo/".substr($jwid,0,4)."/".$jwid.".JPG";
				return ['status'=>1,'msg'=>$url];
			}else{
				return ['status'=>0,'msg'=>'绩点为空'];
			}
		}
		return view();
	}

	/**
	 * 绩点
	 */
	public function jd()
	{	
		$user = $this->user;
		if (Request::instance()->isAjax()){
			$jd = Score::getJd($user['jwid'],$user['jwpwd']);
			if(!empty($jd)){
				return ['status'=>1,'msg'=>$jd];
			}else{
				return ['status'=>0,'msg'=>'绩点为空'];
			}
		}
		return view();
	}

	/**
	 * 选修课
	 */
	public function xuanxiu()
	{	
		$user = $this->user;
		$this->assign('info',Score::getXuanxiu($user['jwid'],$user['jwpwd']));
		return view();
	}

	/**
	 * 留言
	 */
	public function message()
	{
		$user = $this->user;
		if (Request::instance()->isAjax()){
			if(UserModel::message(input('message'))){
				Template::sendTemplate('oTdU_wh_nCmPYFmcsYYEta5duC_k',config('template.message'),url('center/messagereply','openid='.$user['openid'],'',true),'收到一条新的留言',input('message'),[$user['name'],date("Y-m-d H:i:s")]);
				return ['status'=>1,'msg'=>'提交成功'];
			}else{
				return ['status'=>0,'msg'=>'提交失败'];
			}
		}
		return view();
	}

	//回复留言
	public function messagereply()
	{
		$openid = input('openid');
		$userinfo = ServerModel::getJwid($openid);
		$list = Db::name('message')->where('openid',$openid)->select();
		if (Request::instance()->isAjax()){
			Template::sendTemplate(input('openid'),config('template.messagereply'),url('center/message','','',true),'收到一条新的回复',input('message'),['长大校园助手plus',date("Y-m-d H:i:s"),'如下']);
				return ['status'=>1,'msg'=>'提交成功'];
		}
		$this->assign('list',$list);
		$this->assign('userinfo',$userinfo);
		return view();
	}

	/**
	 * 用户中心 ：201562398
	 */
	public function user()
	{
		$user = $this->user;
		if (Request::instance()->isAjax()){
			if(ServerModel::reset(session('openid'))){
				return ['status'=>1,'msg'=>'解绑成功'];
			}else{
				return ['status'=>0,'msg'=>'解绑失败'];
			}
		}
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