<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Log;
use think\Request;
use app\index\controller\Jssdk;
use app\index\controller\Factory;
use app\index\controller\Wechat;
use app\index\model\Cet as CetModel;

/**
* 四六级准考证绑定
*/
class Cet extends Base
{

	public function bind()
	{	
		$cet = new CetModel();
		if (Request::instance()->isAjax()){

			if($cet->cetBind(input(''))){
				return ['status'=>1,'msg'=>'绑定成功'];
			}else{
				return ['status'=>0,'msg'=>'绑定或更新失败，请重试'];
			}
		}
		$userInfo = $cet->getUserInfo();
		$this->assign('userinfo',$userInfo);
		return $this->fetch();
	}

}