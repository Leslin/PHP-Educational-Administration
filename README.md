 # 微信查询教务处成绩、用户信息

微信教务查询成绩
===============

基于ThinkPHP5 基础上开发的微信查成绩、发红包等

> ThinkPHP5的运行环境要求PHP5.4以上。

详细开发文档参考 [ThinkPHP5完全开发手册](http://www.kancloud.cn/manual/thinkphp5)

- 模拟登陆教务处，回复文字即可查询成绩
- 微信查成绩
- 查成绩发红包
- 发送卡券等

### **效果图如下**
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/1.png)
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/2.png)
------------

### **如何使用**

1. 把代码上传到服务器，导入代码中的sql.sql文件，配置文件中的/application/database.php文件，如下图。
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/3.jpeg)
1. 配置缓存方式，现有的缓存是缓存Redis中，如果不了解Redis的话，请修改缓存配置为文件缓存，代码路径/application/config.php如下图。
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/4.jpeg)
1. 预先载入配置信息，公众号配置信息我这边是预先存入Redis中的，直接读取的Redis，如果使用文件缓存或Redis，请预先把公众号信息存入，格式为数组方式，数据为edu.config中的信息，key为wx_config。

1. 配置完成后，就可以愉快的玩耍了，访问路径是http://xxxx.com/public/index.php/index/index， 如果会写伪静态的话，可以隐藏index.php，不会的话就按照上面的弄。

1. 配置公众号。登录微信公众号平台，如果没有，可以申请一个测试公众号，申请方法请自行百度。申请好后，配置如下图，如果出现超时，token错误，请检查代码server.php
![](https://github.com/Leslin/PHP-Educational-Administration/blob/master/screenshot/5.jpeg)

### **代码解读**
请自行看代码，框架使用的是ThinkPHP5.0，这里就不解读了，代码都有注释，看不懂的请加群吧。


