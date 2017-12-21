 # 微信查询教务处成绩、用户信息


===============

基于ThinkPHP5 基础上开发的微信查成绩、发红包等

> ThinkPHP5的运行环境要求PHP5.4以上。

详细开发文档参考 [ThinkPHP5完全开发手册](http://www.kancloud.cn/manual/thinkphp5)

## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─common             公共模块目录（可以更改）
│  ├─index              程序目录
│  │  ├─controller      控制器目录
│  │  │     ├─Base.php  微信网页授权文件
|  |  |     ├─Bind.php  学号绑定入口，提供公共绑定函数
|  |  |     |-Factory.php 工厂
|  |  |     |-Index.php   测试使用
|  |  |     ├─Jssdk.php   微信网页jssdk文件
|  |  |     |-Public.php  成功，错误模板文件
|  |  |     |-Red.php     微信红包 
|  |        ├─Score.php   获取成绩文件
|  |  |     |-Server.php  对接微信服务器文件
|  |  |     |-Wechat.php  封装微信接口文件
|  |  |     |-Cet.php     四六级绑定 
|  |  |     |-Center.php  个人中心
│  │  ├─model           模型目录
|  |      ├─model     
│  │  ├─view            视图目录
│  │  └─ ...            更多类库目录
│  │
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~

- 模拟登陆教务处，回复文字即可查询成绩
- 微信查成绩
- 查成绩发红包
- 发送卡券等

### **效果图如下**
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/11.png)
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/22.png)
------------

### **如何使用**

1. 把代码上传到服务器，导入代码中的sql.sql文件，配置文件中的/application/database.php文件，如下图。
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/3.png)
1. 配置缓存方式，现有的缓存是缓存Redis中，如果不了解Redis的话，请修改缓存配置为文件缓存，代码路径/application/config.php如下图。
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/4.png)
1. 预先载入配置信息，公众号配置信息我这边是预先存入Redis中的，直接读取的Redis，如果使用文件缓存或Redis，请预先把公众号信息存入，格式为数组方式，数据为edu.config中的信息，key为wx_config。

1. 配置完成后，就可以愉快的玩耍了，访问路径是http://xxxx.com/public/index.php/index/index， 如果会写伪静态的话，可以隐藏index.php，不会的话就按照上面的弄。

1. 配置公众号。登录微信公众号平台，如果没有，可以申请一个测试公众号，申请方法请自行百度。申请好后，配置如下图，如果出现超时，token错误，请检查代码server.php
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/5.png)

### **代码解读**
请自行看代码，框架使用的是ThinkPHP5.0，这里就不解读了，代码都有注释，看不懂的请加群吧。
交流QQ群：684555720

### **微信发红包**
为了回馈粉丝，做了一个查成绩发红包的功能，具体功能请查看Red.php这个文件，红包控制在Server.php中。

### **四六级准考证绑定**
新增四六级准考证号绑定功能。

### **个人中心**
新增个人中心功能，集成所有功能到个人中心。

### **增加个人中心成绩查询**
个人中心成绩查询

### **增加个人中心绩点查询**
增加个人中心绩点查询