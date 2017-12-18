<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Request;
use app\index\controller\Jssdk;

/**
* 表白墙
*/
class Love extends Base
{	
	public static $baseurl = 'http://cd.cdhand.com/public/index.php/index/love/index';

	public function index()
	{
		return view();
	}

	public function getmore()
	{	
		$page = input('page');
		$size = input('size');
		$list = Db::table('view_love_list')->page($page,$size)->order("id desc")->select();
		$this->assign('list',$list);
		return $this->fetch();
	}

	public function my()
	{
		$list = Db::table('view_love_list')->where('openid',session('openid'))->select();
		$this->assign('list',$list);
		return $this->fetch();
	}

	public function send()
	{	
		$isBind = Db::name('user')->where('openid',session('openid'))->find();
		$this->assign('isBind',$isBind);
		return $this->fetch();
	}

	public function delete()
	{
		$delete = Db::name('love_list')->where('id',input('id'))->delete();
		if($delete != 0){
			return ['status'=>1,'msg'=>'删除成功'];
		}else{
			return ['status'=>0,'msg'=>'删除失败'];
		}
	}

	//Cache::set(self::$prefix.session('openid'),$userInfo); //获取用户信息存入redis中
	public function sendlove()
	{
		if (Request::instance()->isAjax()){
			$userInfo = Cache::get('user_'.session('openid'));
			$love['openid'] = session('openid');
			$love['headimage'] = $userInfo['headimgurl'];
			$love['toname'] = input('toname');
			$love['jwid'] = input('jwid');
			$love['content'] = input('content');
			$love['is_hide'] = input('is_hide');
			$love['is_send'] = input('is_send');
			$love['is_receive'] = input('is_receive');
			$love['intime'] = time();
			$addLove = Db::name('love_list')->insert($love);
			if($addLove){
				self::matching($love['jwid'],$love['content'],$love['is_receive'],$love['is_send']);
				return ['status'=>1,'msg'=>'发送成功'];
			}else{
				return ['status'=>0,'msg'=>'发送失败'];
			}

		}
	}

	/**
	 * 恋爱匹配查询
	 */
	public static function matching($jwid = '',$content = '',$is_receive = '',$is_send = '',$is_hide = '')
	{	
		$myinfo = Db::name('user')->where('openid',session('openid'))->find();  //我的信息
		//优先匹配学号，学号不为空，发送提醒
		$checkjwid = Db::name('user')->where('jwid',$jwid)->find();
		if(!empty($checkjwid)){

			$getJwid = Db::table('view_love_list')->where('jwid',$myinfo['jwid'])->where('is_ok',0)->select();  //发送一条表白，先搜索有没有其他人对我表白过

			$toJwid = Db::table('view_love_list')->where('jwid',$jwid)->where('is_ok',0)->select();//表白对象的id,可能多个

			if(!empty($getJwid)){   //有人对我表白过了
				$up['is_ok'] = 1;
				Db::name('love_list')->where('openid',session('openid'))->where('jwid',$jwid)->update($up);
				if($is_receive == 1){   //接受消息提醒，先给我自己推送别人给我表白的信息
					foreach ($getJwid as $k => $v) {
						if($getJwid[$k]['is_ok'] != 1){
							Template::sendTemplate(session('openid'),config('template.loveok'),self::$baseurl,$myinfo['name'].'你好，恭喜您暗恋表白匹配成功，有'.count($getJwid).'人对你暗恋表白过','TO'.$myinfo['name'].'：'.$getJwid[$k]['content'],['与 '.$getJwid[$k]['name'].' 暗恋表白匹配成功','成功  '.$getJwid[$k]['time']]);//发送给我自己
						}
						
					}
				}
				if($is_hide == 1){ //匿名
					$myinfo['name'] = '匿名用户';
				}
				//给我表白的人推送我这条表白信息
				foreach ($toJwid as $key => $value) {
					if($toJwid[$key]['is_receive'] == 1){
						if($toJwid[$key]['is_ok'] != 1){
							Template::sendTemplate($toJwid[$key]['openid'],config('template.loveok'),self::$baseurl,$toJwid[$key]['name'].'你好，恭喜您暗恋表白匹配成功，'.$myinfo['name'].'对你暗恋表白啦','TO'.$toJwid[$key]['name'].'：'.$content,['暗恋表白匹配成功','成功  '.date('Y-m-d H:i:s',time())]);//发送给对我表白的人
							Db::name('love_list')->where('id',$toJwid[$key]['id'])->update($up);
						}
					}
				}

			}
		}
	}
}