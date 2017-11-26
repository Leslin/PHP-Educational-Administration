<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use app\index\controller\Jssdk;
use app\index\controller\Base;

/**
 * 微信相关
 */
class Index extends Base
{
    public function index()
    {	
    	$res = Cache::get(Base::$prefix.session('openid'));
    	dump($res);
    }
}
