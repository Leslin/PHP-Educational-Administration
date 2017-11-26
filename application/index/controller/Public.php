<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Request;
use app\index\controller\Jssdk;

/**
* Bind
*/
class Public extends Controller
{	
	public function __construct()
    {
        $this->getSignPackage = Jssdk::getSignPackage()  //分享所需的jssdk签名包
        $this->assign('getSignPackage',$this->getSignPackage);
    }

	public function success()
	{	
		return $this->fetch();
	}

	public function fail()
	{
		return $this->fetch();
	}
}