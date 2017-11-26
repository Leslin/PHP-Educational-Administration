<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::rule('Server/index','server/index','GET');
Route::rule('Bind/index','bind/index','GET');
Route::rule('Public/success','public/success','GET');
Route::rule('Public/fial','public/fial','GET');
// Route::miss('Public/fial');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
];
