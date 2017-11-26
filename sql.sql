SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS  `edu_config`;
CREATE TABLE `edu_config` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `appid` varchar(20) NOT NULL,
  `appsecret` varchar(64) NOT NULL,
  `token` varchar(32) DEFAULT NULL,
  `access_token` varchar(100) DEFAULT NULL,
  `token_expire_time` int(20) DEFAULT NULL,
  `ticket` varchar(100) DEFAULT NULL,
  `ticket_expire_time` int(20) DEFAULT NULL,
  `intime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='公账号配置信息';

DROP TABLE IF EXISTS  `edu_redlog`;
CREATE TABLE `edu_redlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(32) DEFAULT NULL,
  `jwid` int(11) DEFAULT NULL,
  `amount` decimal(10,0) DEFAULT NULL,
  `intime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `edu_serverlog`;
CREATE TABLE `edu_serverlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tousername` varchar(32) DEFAULT NULL,
  `fromusername` varchar(32) DEFAULT NULL,
  `createtime` varchar(32) DEFAULT NULL,
  `msgtype` varchar(32) DEFAULT NULL,
  `content` varchar(200) DEFAULT NULL,
  `msgid` varchar(32) DEFAULT NULL,
  `intime` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `edu_user`;
CREATE TABLE `edu_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `jwid` varchar(200) NOT NULL,
  `jwpwd` varchar(200) NOT NULL,
  `openid` varchar(200) NOT NULL,
  `time` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_openid` (`openid`(32),`jwid`(12)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=654 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;

