INSERT IGNORE INTO `%DB_PREFIX%role_nav` VALUES ('11', '手机', '1', '8');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('220', '', '', '1', 'MConfig', '手机端管理', '11', '10', '1', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('221', 'index', '手机端配置', '1', 'MConfig', '手机端管理', '11', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('222', '', '', '1', 'MAdv', '手机端广告管理', '0', '10', '1', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('223', 'index', '广告列表', '1', 'MAdv', '手机端广告管理', '11', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('224', 'add', '添加广告', '1', 'MAdv', '手机端广告管理', '11', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('225', '', '', '1', 'MIndex', '手机端首页菜单管理', '0', '10', '1', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('226', 'index', '菜单列表', '1', 'MIndex', '手机端首页菜单管理', '11', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('227', 'add', '添加菜单', '1', 'MIndex', '手机端首页菜单管理', '11', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('228', '', '', '1', 'MSearchcate', '手机端搜索页配置', '0', '10', '1', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('229', 'index', '分类列表', '1', 'MSearchcate', '手机端搜索页配置', '11', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('230', 'add', '添加分类', '1', 'MSearchcate', '手机端搜索页配置', '11', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('231', '', '', '1', 'MApns', '信息推送', '0', '10', '1', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('232', 'index', '发送记录', '1', 'MApns', '信息推送', '11', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('233', 'add', '发送信息', '1', 'MApns', '信息推送', '11', '10', '0', '1');

INSERT IGNORE INTO `%DB_PREFIX%role_access` (`role_id`, `node_id`) VALUES (1, 231),
(1, 228),
(1, 225),
(1, 222),
(1, 220);

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%apns_devices` (
  `pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` int(11) NOT NULL DEFAULT '0',
  `appname` varchar(255) NOT NULL,
  `appversion` varchar(25) DEFAULT NULL,
  `deviceuid` char(40) NOT NULL,
  `devicetoken` char(64) NOT NULL,
  `devicename` varchar(255) NOT NULL,
  `devicemodel` varchar(100) NOT NULL,
  `deviceversion` varchar(25) NOT NULL,
  `pushbadge` enum('disabled','enabled') DEFAULT 'disabled',
  `pushalert` enum('disabled','enabled') DEFAULT 'disabled',
  `pushsound` enum('disabled','enabled') DEFAULT 'disabled',
  `development` enum('production','sandbox') CHARACTER SET latin1 NOT NULL DEFAULT 'production',
  `status` enum('active','uninstalled') NOT NULL DEFAULT 'active',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`),
  UNIQUE KEY `appname` (`appname`,`appversion`,`deviceuid`),
  KEY `clientid` (`clientid`),
  KEY `devicetoken` (`devicetoken`),
  KEY `devicename` (`devicename`),
  KEY `devicemodel` (`devicemodel`),
  KEY `deviceversion` (`deviceversion`),
  KEY `pushbadge` (`pushbadge`),
  KEY `pushalert` (`pushalert`),
  KEY `pushsound` (`pushsound`),
  KEY `development` (`development`),
  KEY `status` (`status`),
  KEY `created` (`created`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Store unique devices' AUTO_INCREMENT=30 ;




INSERT IGNORE INTO `%DB_PREFIX%apns_devices` (`pid`, `clientid`, `appname`, `appversion`, `deviceuid`, `devicetoken`, `devicename`, `devicemodel`, `deviceversion`, `pushbadge`, `pushalert`, `pushsound`, `development`, `status`, `created`, `modified`) VALUES
(21, 0, '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-16 19:39:41', '2012-01-29 11:57:45'),
(22, 1014, '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 09:17:58', '2012-01-24 19:30:31'),
(23, 0, '趣购街', '1.0', 'e338c2c919bed1a7d95321adf085fdc6', 'a95eded5331126113ead0c410a75f6d6a2050515778cbd1120f39305ef4a3614', '“Administrator”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 09:21:34', '2012-01-27 18:05:28'),
(24, 1014, '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 15:51:02', '2012-01-18 16:29:01'),
(25, 0, '趣购街', '1.0', '2d2257a1781dd9b5887741308822453f', '2b1d1c9327c7176d7cfeeec19401787ac09873ec97f729c595c26e76f0d8b9e3', 'Sophia-Phone', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 22:48:37', '2012-01-21 11:18:23'),
(26, 1234, '趣购街', '1.0', '799f010d1eca412c5a784c66dcb62375', 'f8eed9bb4508a3cafeb5084a7665552e5ff4d67a7defd3fd4a24fab1c6cb0636', 'Owen''s iPad', 'iPad', '4.3.3', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-19 07:11:20', '2012-01-26 10:27:00'),
(27, 1250, '趣购街', '1.0', '7078c1c90e37a31ba6a0d3d2fdb9552a', '28301094c2ab457ba0de0a71188df56de506e1a4e68b0de4bf34354388fb77ba', '老毛的 iPhone', 'iPhone', '5.0.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-26 16:59:36', '2012-01-26 17:11:20'),
(28, 0, '趣购街', '1.0', '8e9f26e78efea371ffb7e360cb401ee2', '8ff6bb487e0e97f1ebe5233988286f1d5adc9cb2435478179cdcfd1519f1253c', 'ML 的 iPad', 'iPad', '5.0.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-29 15:03:18', '2012-01-29 15:03:18'),
(29, 1255, '趣购街', '1.0', 'da18357842c560a916ef4a171533862c', 'bff9c06027f9760bf4aba4bda6259da1704568bca96a4697396d7decbc88555c', '“robb”的 iPhone', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-29 18:14:47', '2012-01-30 01:07:18');

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%apns_device_history` (
  `pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` varchar(64) NOT NULL,
  `appname` varchar(255) NOT NULL,
  `appversion` varchar(25) DEFAULT NULL,
  `deviceuid` char(40) NOT NULL,
  `devicetoken` char(64) NOT NULL,
  `devicename` varchar(255) NOT NULL,
  `devicemodel` varchar(100) NOT NULL,
  `deviceversion` varchar(25) NOT NULL,
  `pushbadge` enum('disabled','enabled') DEFAULT 'disabled',
  `pushalert` enum('disabled','enabled') DEFAULT 'disabled',
  `pushsound` enum('disabled','enabled') DEFAULT 'disabled',
  `development` enum('production','sandbox') CHARACTER SET latin1 NOT NULL DEFAULT 'production',
  `status` enum('active','uninstalled') NOT NULL DEFAULT 'active',
  `archived` datetime NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `clientid` (`clientid`),
  KEY `devicetoken` (`devicetoken`),
  KEY `devicename` (`devicename`),
  KEY `devicemodel` (`devicemodel`),
  KEY `deviceversion` (`deviceversion`),
  KEY `pushbadge` (`pushbadge`),
  KEY `pushalert` (`pushalert`),
  KEY `pushsound` (`pushsound`),
  KEY `development` (`development`),
  KEY `status` (`status`),
  KEY `appname` (`appname`),
  KEY `appversion` (`appversion`),
  KEY `deviceuid` (`deviceuid`),
  KEY `archived` (`archived`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Store unique device history' AUTO_INCREMENT=90 ;

INSERT IGNORE INTO `%DB_PREFIX%apns_device_history` (`pid`, `clientid`, `appname`, `appversion`, `deviceuid`, `devicetoken`, `devicename`, `devicemodel`, `deviceversion`, `pushbadge`, `pushalert`, `pushsound`, `development`, `status`, `archived`) VALUES
(1, '1014', '最惠365', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-11 17:47:35'),
(4, '1014', '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-12 17:43:28'),
(5, '1014', '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-12 17:50:16'),
(6, '0', '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-12 17:50:47'),
(7, '0', '趣购街', '1.0', '72e91211e3e67dbac17589193ad35975', '8b79619db5258882c161b0d8bb80a4677340b9febb2fa94247aeefbad1e49a94', 'lin の iPhone', 'iPhone', '4.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-12 18:12:26'),
(8, '1014', '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-12 18:18:21'),
(9, '138', '趣购街', '1.0', '72e91211e3e67dbac17589193ad35975', '8b79619db5258882c161b0d8bb80a4677340b9febb2fa94247aeefbad1e49a94', 'lin の iPhone', 'iPhone', '4.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-12 18:38:34'),
(10, '138', '趣购街', '1.0', '72e91211e3e67dbac17589193ad35975', '8b79619db5258882c161b0d8bb80a4677340b9febb2fa94247aeefbad1e49a94', 'lin の iPhone', 'iPhone', '4.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-12 18:46:46'),
(11, '0', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-12 20:35:29'),
(12, '0', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-12 20:39:57'),
(13, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-12 20:42:23'),
(14, '0', '趣购街', '1.0', '6d387a21536b1df449ac336cc9248f3a', 'c32dbf56e45ed913e8f4def5906acef05c279f41e6018896c6b23dc98d84413d', '“wumin”的 iPhone', 'iPhone', '4.2.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-12 23:14:31'),
(15, '138', '趣购街', '1.0', '72e91211e3e67dbac17589193ad35975', '8b79619db5258882c161b0d8bb80a4677340b9febb2fa94247aeefbad1e49a94', 'lin の iPhone', 'iPhone', '4.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 11:26:34'),
(16, '138', '趣购街', '1.0', '72e91211e3e67dbac17589193ad35975', '8b79619db5258882c161b0d8bb80a4677340b9febb2fa94247aeefbad1e49a94', 'lin の iPhone', 'iPhone', '4.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 11:26:43'),
(17, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 11:38:36'),
(18, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 11:40:37'),
(19, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 12:03:14'),
(20, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 12:10:16'),
(21, '0', '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-13 14:04:00'),
(22, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 14:04:13'),
(23, '985', '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-13 14:06:04'),
(24, '985', '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 14:07:59'),
(25, '985', '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 14:12:57'),
(26, '0', '趣购街', '1.0', '6d387a21536b1df449ac336cc9248f3a', 'c32dbf56e45ed913e8f4def5906acef05c279f41e6018896c6b23dc98d84413d', '“wumin”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 14:35:13'),
(27, '0', '趣购街', '1.0', '6d387a21536b1df449ac336cc9248f3a', 'c32dbf56e45ed913e8f4def5906acef05c279f41e6018896c6b23dc98d84413d', '“wumin”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 14:39:48'),
(28, '0', '趣购街', '1.0', '6d387a21536b1df449ac336cc9248f3a', 'c32dbf56e45ed913e8f4def5906acef05c279f41e6018896c6b23dc98d84413d', '“wumin”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 14:40:11'),
(29, '138', '趣购街', '1.0', '72e91211e3e67dbac17589193ad35975', '8b79619db5258882c161b0d8bb80a4677340b9febb2fa94247aeefbad1e49a94', 'lin の iPhone', 'iPhone', '4.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 15:03:15'),
(30, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-13 18:08:02'),
(31, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-14 10:28:52'),
(32, '1014', '趣购街', '1.0', '17135bc4cda5f24b887c474821158772', 'c7d58d88198b077731c580767b86a5a1279931186b8da825d902fe919d16745c', '“chigao”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-14 11:18:14'),
(33, '0', '趣购街', '1.0', '68332ca277be5dc26cbfda4ef7eece43', '5cef7e7114feef9ce4a9eb0b38f9303a1c2abffea55f437419b2e29d1698b944', '“Administrator”的 iPod', 'iPod touch', '4.2.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-14 12:02:49'),
(34, '0', '趣购街', '1.0', '7188ad3c8f06f3486f29a537dc21c9ae', 'ee4c04987e184eae8d7670e89166f5ff16d1fb02ecad03788f85d709d2c8f355', '王勇的 iPod', 'iPod touch', '4.2.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-14 15:10:50'),
(35, '790', '趣购街', '1.0', '7188ad3c8f06f3486f29a537dc21c9ae', 'ee4c04987e184eae8d7670e89166f5ff16d1fb02ecad03788f85d709d2c8f355', '王勇的 iPod', 'iPod touch', '4.2.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-14 15:14:25'),
(36, '790', '趣购街', '1.0', '7188ad3c8f06f3486f29a537dc21c9ae', 'ee4c04987e184eae8d7670e89166f5ff16d1fb02ecad03788f85d709d2c8f355', '王勇的 iPod', 'iPod touch', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-14 15:27:44'),
(37, '0', '趣购街', '1.0', '8691e28938116ee62342be47783ef71a', '19f616ad137e02249fa6c884e8304360c157a0f9e4e34cefd59d71a49174c299', '“yewl”的 iPhone', 'iPhone', '5.0.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-14 15:57:01'),
(38, '790', '趣购街', '1.0', '7188ad3c8f06f3486f29a537dc21c9ae', 'ee4c04987e184eae8d7670e89166f5ff16d1fb02ecad03788f85d709d2c8f355', '王勇的 iPod', 'iPod touch', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-14 22:59:16'),
(39, '790', '趣购街', '1.0', '7188ad3c8f06f3486f29a537dc21c9ae', 'ee4c04987e184eae8d7670e89166f5ff16d1fb02ecad03788f85d709d2c8f355', '王勇的 iPod', 'iPod touch', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-14 22:59:43'),
(40, '0', '趣购街', '1.0', 'e338c2c919bed1a7d95321adf085fdc6', 'a95eded5331126113ead0c410a75f6d6a2050515778cbd1120f39305ef4a3614', '“Administrator”的 iPhone', 'iPhone', '4.2.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-15 15:20:05'),
(41, '790', '趣购街', '1.0', '7188ad3c8f06f3486f29a537dc21c9ae', 'ee4c04987e184eae8d7670e89166f5ff16d1fb02ecad03788f85d709d2c8f355', '王勇的 iPod', 'iPod touch', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-15 23:50:38'),
(42, '0', '趣购街', '1.0', 'e338c2c919bed1a7d95321adf085fdc6', 'a95eded5331126113ead0c410a75f6d6a2050515778cbd1120f39305ef4a3614', '“Administrator”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-16 01:21:14'),
(43, '985', '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-16 16:24:58'),
(44, '985', '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-16 19:35:34'),
(45, '0', '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-17 09:19:49'),
(46, '0', '趣购街', '1.0', 'e338c2c919bed1a7d95321adf085fdc6', 'a95eded5331126113ead0c410a75f6d6a2050515778cbd1120f39305ef4a3614', '“Administrator”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 09:49:44'),
(47, '0', '趣购街', '1.0', 'e338c2c919bed1a7d95321adf085fdc6', 'a95eded5331126113ead0c410a75f6d6a2050515778cbd1120f39305ef4a3614', '“Administrator”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'uninstalled', '2012-01-17 11:09:32'),
(48, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 16:28:22'),
(49, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 16:32:26'),
(50, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 22:33:50'),
(51, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 22:38:51'),
(52, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 22:46:03'),
(53, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 22:49:29'),
(54, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 22:53:08'),
(55, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:04:36'),
(56, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:13:20'),
(57, '0', '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:15:06'),
(58, '0', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:15:06'),
(59, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:22:16'),
(60, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:25:25'),
(61, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:29:43'),
(62, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:36:44'),
(63, '1014', '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-17 23:50:36'),
(64, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 09:33:11'),
(65, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 10:01:16'),
(66, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 11:02:33'),
(67, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 11:49:59'),
(68, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 14:52:23'),
(69, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 14:57:13'),
(70, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 15:12:18'),
(71, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 15:18:08'),
(72, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 15:21:03'),
(73, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 15:26:24'),
(74, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 15:40:59'),
(75, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 15:44:08'),
(76, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 16:04:28'),
(77, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 16:08:05'),
(78, '1014', '方维o2o', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', '4261aa2025f701d2dfa1291cd266046b3c51e8b9608e47698a7a415159dd87c3', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-18 16:29:01'),
(79, '0', '趣购街', '1.0', '2d2257a1781dd9b5887741308822453f', '2b1d1c9327c7176d7cfeeec19401787ac09873ec97f729c595c26e76f0d8b9e3', 'Sophia-Phone', 'iPhone', '5.0.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-18 22:50:36'),
(80, '0', '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-19 13:27:53'),
(81, '0', '趣购街', '1.0', '799f010d1eca412c5a784c66dcb62375', 'f8eed9bb4508a3cafeb5084a7665552e5ff4d67a7defd3fd4a24fab1c6cb0636', 'Owen''s iPad', 'iPad', '4.3.3', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-19 18:31:45'),
(82, '0', '趣购街', '1.0', '2d2257a1781dd9b5887741308822453f', '2b1d1c9327c7176d7cfeeec19401787ac09873ec97f729c595c26e76f0d8b9e3', 'Sophia-Phone', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-21 11:18:23'),
(83, '1014', '趣购街', '1.0', 'c1e34ff19505c5b11d876bdffb451aa8', 'a191fc35b39cc62bdb21872262ac77446428afa6cb3b78b82b22b02028b86630', '酷酷菜', 'iPhone', '4.3.5', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-24 19:30:31'),
(84, '1234', '趣购街', '1.0', '799f010d1eca412c5a784c66dcb62375', 'f8eed9bb4508a3cafeb5084a7665552e5ff4d67a7defd3fd4a24fab1c6cb0636', 'Owen''s iPad', 'iPad', '4.3.3', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-26 10:27:00'),
(85, '0', '趣购街', '1.0', '7078c1c90e37a31ba6a0d3d2fdb9552a', '28301094c2ab457ba0de0a71188df56de506e1a4e68b0de4bf34354388fb77ba', '老毛的 iPhone', 'iPhone', '5.0.1', 'disabled', 'disabled', 'disabled', 'production', 'active', '2012-01-26 17:11:20'),
(86, '0', '趣购街', '1.0', 'e338c2c919bed1a7d95321adf085fdc6', 'a95eded5331126113ead0c410a75f6d6a2050515778cbd1120f39305ef4a3614', '“Administrator”的 iPhone', 'iPhone', '4.2.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-27 18:05:28'),
(87, '0', '趣购街', '1.0', 'bd9c36ea42983a53dad9a2d4d09b9c74', '92f131624babe262e19516213dfc61dcf87985b3df67d17a24bfdbdd6bb6df9f', 'kukucha', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-29 11:57:45'),
(88, '0', '趣购街', '1.0', 'da18357842c560a916ef4a171533862c', 'bff9c06027f9760bf4aba4bda6259da1704568bca96a4697396d7decbc88555c', '“robb”的 iPhone', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-30 01:02:44'),
(89, '1255', '趣购街', '1.0', 'da18357842c560a916ef4a171533862c', 'bff9c06027f9760bf4aba4bda6259da1704568bca96a4697396d7decbc88555c', '“robb”的 iPhone', 'iPhone', '5.0.1', 'enabled', 'enabled', 'enabled', 'production', 'active', '2012-01-30 01:07:18');

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%apns_messages` (
  `pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` varchar(64) NOT NULL,
  `fk_device` int(9) unsigned NOT NULL,
  `message` varchar(255) NOT NULL,
  `delivery` datetime NOT NULL,
  `status` enum('queued','delivered','failed') CHARACTER SET latin1 NOT NULL DEFAULT 'queued',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`),
  KEY `clientid` (`clientid`),
  KEY `fk_device` (`fk_device`),
  KEY `status` (`status`),
  KEY `created` (`created`),
  KEY `modified` (`modified`),
  KEY `message` (`message`),
  KEY `delivery` (`delivery`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Messages to push to APNS' AUTO_INCREMENT=41 ;

INSERT IGNORE INTO `%DB_PREFIX%apns_messages` (`pid`, `clientid`, `fk_device`, `message`, `delivery`, `status`, `created`, `modified`) VALUES
(29, '0', 21, '{"aps":{"clientid":null,"alert":{"body":"\\u63a8\\u9001\\u6d4b\\u8bd5"},"sound":"bingbong.aiff"}}', '2012-01-16 19:39:58', 'delivered', '2012-01-16 19:39:58', '2012-01-16 19:39:59'),
(30, '0', 21, '{"aps":{"clientid":null,"alert":{"body":"\\u63a8\\u9001\\u6d4b\\u8bd5"},"sound":"bingbong.aiff"}}', '2012-01-16 19:40:37', 'delivered', '2012-01-16 19:40:37', '2012-01-16 19:40:37'),
(31, '0', 21, '{"aps":{"clientid":null,"alert":{"body":"===="},"sound":"bingbong.aiff"}}', '2012-01-17 09:20:47', 'delivered', '2012-01-17 09:20:47', '2012-01-17 09:20:50'),
(32, '0', 22, '{"aps":{"clientid":null,"alert":{"body":"===="},"sound":"bingbong.aiff"}}', '2012-01-17 09:20:47', 'delivered', '2012-01-17 09:20:47', '2012-01-17 09:20:48'),
(33, '0', 21, '{"aps":{"clientid":null,"alert":{"body":"111"},"sound":"bingbong.aiff"}}', '2012-01-17 09:40:11', 'delivered', '2012-01-17 09:40:11', '2012-01-17 09:40:12'),
(34, '0', 22, '{"aps":{"clientid":null,"alert":{"body":"111"},"sound":"bingbong.aiff"}}', '2012-01-17 09:40:11', 'delivered', '2012-01-17 09:40:11', '2012-01-17 09:40:14'),
(35, '0', 23, '{"aps":{"clientid":null,"alert":{"body":"111"},"sound":"bingbong.aiff"}}', '2012-01-17 09:40:11', 'delivered', '2012-01-17 09:40:11', '2012-01-17 09:40:16'),
(36, '0', 21, '{"aps":{"clientid":null,"alert":{"body":"sesds"},"sound":"bingbong.aiff"}}', '2012-01-17 09:49:42', 'delivered', '2012-01-17 09:49:42', '2012-01-17 09:49:47'),
(37, '0', 22, '{"aps":{"clientid":null,"alert":{"body":"sesds"},"sound":"bingbong.aiff"}}', '2012-01-17 09:49:42', 'delivered', '2012-01-17 09:49:42', '2012-01-17 09:49:45'),
(38, '0', 23, '{"aps":{"clientid":null,"alert":{"body":"sesds"},"sound":"bingbong.aiff"}}', '2012-01-17 09:49:42', 'delivered', '2012-01-17 09:49:42', '2012-01-17 09:49:43'),
(39, '0', 21, '{"aps":{"clientid":null,"alert":{"body":"\\u8da3\\u8d2d\\u8857\\u4fe1\\u606f\\u63a8\\u9001\\u6d4b\\u8bd5"},"sound":"bingbong.aiff"}}', '2012-01-17 09:50:27', 'delivered', '2012-01-17 09:50:27', '2012-01-17 09:50:29'),
(40, '0', 22, '{"aps":{"clientid":null,"alert":{"body":"\\u8da3\\u8d2d\\u8857\\u4fe1\\u606f\\u63a8\\u9001\\u6d4b\\u8bd5"},"sound":"bingbong.aiff"}}', '2012-01-17 09:50:27', 'delivered', '2012-01-17 09:50:27', '2012-01-17 09:50:27');

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%m_adv` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `img` varchar(255) DEFAULT '',
  `page` enum('sharelist','index') DEFAULT 'sharelist',
  `type` tinyint(1) DEFAULT '0' COMMENT '1.标签集,2.url地址,3.分类排行,4.最亮达人,5.搜索发现,6.一起拍,7.热门单品排行,8.直接显示某个分享',
  `data` text,
  `sort` smallint(5) DEFAULT '10',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

INSERT IGNORE INTO `%DB_PREFIX%m_adv` (`id`, `name`, `img`, `page`, `type`, `data`, `sort`, `status`) VALUES
(1, '至In美衣', './public/upload/m/Lighthouse.jpg', 'index', 1, 'a:2:{s:3:"cid";i:2;s:4:"tags";a:20:{i:0;s:6:"衬衫";i:1;s:9:"连衣裙";i:2;s:6:"短裤";i:3;s:6:"短裙";i:4;s:6:"毛衣";i:5;s:9:"呢大衣";i:6;s:6:"西装";i:7;s:6:"斗篷";i:8;s:6:"棉服";i:9;s:9:"羽绒服";i:10;s:6:"马甲";i:11;s:6:"卫衣";i:12;s:9:"打底衫";i:13;s:6:"蕾丝";i:14;s:9:"娃娃领";i:15;s:6:"长款";i:16;s:6:"豹纹";i:17;s:6:"英伦";i:18;s:6:"日系";i:19;s:6:"复古";}}', 1, 1),
(2, '趣购街', './public/upload/m/Tulips.jpg', 'index', 2, 'a:1:{s:3:"url";s:23:"http://www.qugoujie.com";}', 2, 1),
(3, '冬季短裤女', './public/upload/m/Koala.jpg', 'index', 8, 'a:1:{s:8:"share_id";i:67240;}', 3, 1),
(4, '逛街页广告测试1', './public/upload/m/201201/07/4f0820c3e94db.jpg', 'sharelist', 1, 'a:2:{s:3:"cid";i:2;s:4:"tags";a:3:{i:0;s:6:"衣服";i:1;s:6:"上衣";i:2;s:6:"欧美";}}', 10, 1),
(5, 'dsdasdsa', './public/upload/m/201201/07/4f08261495fac.png', 'index', 2, 'a:1:{s:3:"url";s:20:"http://www.fanwe.com";}', 10, 1),
(6, '逛街页广告测试2', './public/upload/m/201201/11/4f0d476b23d94.jpg', 'sharelist', 2, 'a:1:{s:3:"url";s:23:"http://www.qugoujie.com";}', 10, 1),
(7, '逛街页广告测试3', './public/upload/m/201201/11/4f0d47d7d7fe9.jpg', 'sharelist', 8, 'a:1:{s:8:"share_id";i:67180;}', 10, 1);

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%m_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `val` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

INSERT IGNORE INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`) VALUES
(10, 'kf_phone', '客服电话', '0591-83323127'),
(11, 'kf_email', '客服邮箱', 'kf@futuan.com'),
(16, 'page_size', '分页大小', '15'),
(17, 'about_info', '关于我们', '<p>\r\n	福团网 www.futuan.com</p>\r\n'),
(18, 'version', '软件版本', '1.0'),
(19, 'filename', '软件文件名', 'futuan.apk'),
(20, 'program_title', '程序标题名称', '福团城市生活'),
(21, 'index_logo', '首页LOGO', './public/upload/m/201201/12/4f0e576b8181c.png');

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%m_config_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pay_id` varchar(50) DEFAULT NULL,
  `group` int(10) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `has_calc` int(1) DEFAULT NULL,
  `money` float(10,2) DEFAULT NULL,
  `is_verify` int(1) DEFAULT '0' COMMENT '0:无效；1:有效',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT IGNORE INTO `%DB_PREFIX%m_config_list` (`id`, `pay_id`, `group`, `code`, `title`, `has_calc`, `money`, `is_verify`) VALUES
(1, '19', 1, 'Malipay', '支付宝/各银行', 0, NULL, 0),
(2, '20', 1, 'Mcod', '现金支付', 0, NULL, 0),
(5, NULL, 4, '新闻公告', '新闻公告dsadsada', NULL, NULL, 1),
(6, NULL, 4, '新闻公告2', 'dsatfdsaewfewa', NULL, NULL, 1);

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%m_index` (
  `id` mediumint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `vice_name` varchar(100) DEFAULT NULL,
  `desc` varchar(100) DEFAULT '',
  `img` varchar(255) DEFAULT '',
  `type` tinyint(1) DEFAULT '0' COMMENT '1.标签集,2.url地址,3.分类排行,4.最亮达人,5.搜索发现,6.一起拍,7.热门单品排行,8.直接显示某个分享',
  `data` text,
  `sort` smallint(5) DEFAULT '10',
  `status` tinyint(1) DEFAULT '1',
  `is_hot` tinyint(1) DEFAULT '0',
  `is_new` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

INSERT IGNORE INTO `%DB_PREFIX%m_index` (`id`, `name`, `vice_name`, `desc`, `img`, `type`, `data`, `sort`, `status`, `is_hot`, `is_new`) VALUES
(1, '至In美衣', 'Pretty Clothes', '扮美必备，件件都是的必头好', './public/upload/m/IMG_0236_02.png', 1, 'a:2:{s:3:"cid";i:2;s:4:"tags";a:20:{i:0;s:6:"衬衫";i:1;s:9:"连衣裙";i:2;s:6:"短裤";i:3;s:6:"短裙";i:4;s:6:"毛衣";i:5;s:9:"呢大衣";i:6;s:6:"西装";i:7;s:6:"斗篷";i:8;s:6:"棉服";i:9;s:9:"羽绒服";i:10;s:6:"马甲";i:11;s:6:"卫衣";i:12;s:9:"打底衫";i:13;s:6:"蕾丝";i:14;s:9:"娃娃领";i:15;s:6:"长款";i:16;s:6:"豹纹";i:17;s:6:"英伦";i:18;s:6:"日系";i:19;s:6:"复古";}}', 1, 1, 1, 0),
(2, '恋恋鞋事', 'Lady Shoes', '女人总是缺少一双鞋子', './public/upload/m/IMG_0236_04.png', 1, 'a:2:{s:3:"cid";i:4;s:4:"tags";a:12:{i:0;s:9:"高跟鞋";i:1;s:9:"牛津鞋";i:2;s:9:"及踝靴";i:3;s:6:"短靴";i:4;s:6:"长靴";i:5;s:9:"雪地靴";i:6;s:9:"家居鞋";i:7;s:9:"过膝靴";i:8;s:9:"机车靴";i:9;s:6:"英伦";i:10;s:6:"日系";i:11;s:6:"复古";}}', 2, 1, 0, 0),
(3, '暖暖饰物', 'Accessories', '恋物癖の决胜关键', './public/upload/m/IMG_0236_05.png', 1, 'a:2:{s:3:"cid";i:6;s:4:"tags";a:20:{i:0;s:6:"围巾";i:1;s:6:"帽子";i:2;s:6:"耳罩";i:3;s:6:"手套";i:4;s:6:"披肩";i:5;s:6:"腰带";i:6;s:6:"项链";i:7;s:6:"发箍";i:8;s:6:"发夹";i:9;s:6:"镜框";i:10;s:9:"轻松熊";i:11;s:11:"Hello Kitty";i:12;s:9:"假领子";i:13;s:6:"手表";i:14;s:6:"抱枕";i:15;s:9:"马克杯";i:16;s:9:"手机链";i:17;s:6:"雨伞";i:18;s:9:"手机壳";i:19;s:6:"情侣";}}', 3, 1, 0, 0),
(4, '最爱潮包', 'Fashion Bags', '漂亮包包，惊声尖叫吧！', './public/upload/m/IMG_0236_06.png', 1, 'a:2:{s:3:"cid";i:5;s:4:"tags";a:16:{i:0;s:9:"明星款";i:1;s:9:"公文包";i:2;s:9:"单肩包";i:3;s:9:"双肩包";i:4;s:9:"复古包";i:5;s:9:"水桶包";i:6;s:9:"手提包";i:7;s:9:"拼接包";i:8;s:6:"钱包";i:9;s:9:"旅行箱";i:10;s:9:"斜挎包";i:11;s:9:"零钱包";i:12;s:9:"相机包";i:13;s:9:"收纳包";i:14;s:9:"购物袋";i:15;s:9:"帆布包";}}', 4, 1, 0, 0),
(5, '时尚风向标', 'Hot Trend', '人气NO.1单口大搜罗！', './public/upload/m/IMG_0237_02.png', 3, NULL, 5, 1, 0, 1),
(6, '最亮达人', 'Hot Stars', '趣购街达人全家福，和她们逛街吧！', './public/upload/m/IMG_0237_05.png', 4, NULL, 6, 1, 0, 0),
(7, '搜索发现', 'Search', '一键搜索，时尚直达', './public/upload/m/IMG_0237_06.png', 5, NULL, 7, 1, 0, 0),
(8, '一起拍', 'Photo', '简单而有趣的方式和大家一起拍照分享美丽', './public/upload/m/IMG_0237_03.png', 6, NULL, 8, 1, 0, 0),
(9, '趣购街', 'QuGouJie', '趣购街，当下最时尚最流行的女性社区', './public/upload/m/IMG_0236_03.png', 2, 'a:1:{s:3:"url";s:23:"http://www.qugoujie.com";}', 9, 1, 0, 0),
(10, '冬季短裤女', 'User Share', '滚边牛角扣牛仔短裤靴裤', './public/upload/m/IMG_0237_04.png', 8, 'a:1:{s:8:"share_id";i:67240;}', 10, 1, 0, 0);

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%m_searchcate` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT '',
  `cid` smallint(6) DEFAULT '0',
  `bg` varchar(255) DEFAULT '',
  `tags` text,
  `sort` smallint(5) DEFAULT '10',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

INSERT IGNORE INTO `%DB_PREFIX%m_searchcate` (`id`, `name`, `cid`, `bg`, `tags`, `sort`, `status`) VALUES
(1, '当季', 0, './public/upload/m/dangji.png', 'a:12:{i:0;a:2:{s:3:"tag";s:6:"显瘦";s:5:"color";s:0:"";}i:1;a:2:{s:3:"tag";s:6:"英伦";s:5:"color";s:7:"#E60E6E";}i:2;a:2:{s:3:"tag";s:6:"欧美";s:5:"color";s:0:"";}i:3;a:2:{s:3:"tag";s:6:"波点";s:5:"color";s:0:"";}i:4;a:2:{s:3:"tag";s:6:"毛绒";s:5:"color";s:7:"#E60E6E";}i:5;a:2:{s:3:"tag";s:6:"条纹";s:5:"color";s:0:"";}i:6;a:2:{s:3:"tag";s:6:"日韩";s:5:"color";s:0:"";}i:7;a:2:{s:3:"tag";s:6:"复古";s:5:"color";s:0:"";}i:8;a:2:{s:3:"tag";s:6:"情侣";s:5:"color";s:7:"#E60E6E";}i:9;a:2:{s:3:"tag";s:6:"红色";s:5:"color";s:0:"";}i:10;a:2:{s:3:"tag";s:12:"明星同款";s:5:"color";s:7:"#E60E6E";}i:11;a:2:{s:3:"tag";s:10:"vivi风格";s:5:"color";s:0:"";}}', 1, 1),
(2, '衣服', 2, './public/upload/m/yifu.png', 'a:13:{i:0;a:2:{s:3:"tag";s:6:"毛衣";s:5:"color";s:7:"#E60E6E";}i:1;a:2:{s:3:"tag";s:6:"开衫";s:5:"color";s:0:"";}i:2;a:2:{s:3:"tag";s:6:"风衣";s:5:"color";s:0:"";}i:3;a:2:{s:3:"tag";s:9:"羽绒服";s:5:"color";s:7:"#E60E6E";}i:4;a:2:{s:3:"tag";s:9:"连衣裙";s:5:"color";s:0:"";}i:5;a:2:{s:3:"tag";s:6:"卫衣";s:5:"color";s:7:"#E60E6E";}i:6;a:2:{s:3:"tag";s:6:"斗篷";s:5:"color";s:0:"";}i:7;a:2:{s:3:"tag";s:6:"衬衫";s:5:"color";s:0:"";}i:8;a:2:{s:3:"tag";s:9:"呢大衣";s:5:"color";s:7:"#E60E6E";}i:9;a:2:{s:3:"tag";s:9:"牛仔裤";s:5:"color";s:0:"";}i:10;a:2:{s:3:"tag";s:9:"打底裤";s:5:"color";s:7:"#E60E6E";}i:11;a:2:{s:3:"tag";s:6:"西装";s:5:"color";s:0:"";}i:12;a:2:{s:3:"tag";s:7:"fdsfdsf";s:5:"color";s:7:"#ed008c";}}', 2, 1),
(3, '鞋子', 4, './public/upload/m/xiezhi.png', 'a:12:{i:0;a:2:{s:3:"tag";s:9:"过膝靴";s:5:"color";s:7:"#E60E6E";}i:1;a:2:{s:3:"tag";s:9:"雪地靴";s:5:"color";s:0:"";}i:2;a:2:{s:3:"tag";s:9:"家居鞋";s:5:"color";s:7:"#E60E6E";}i:3;a:2:{s:3:"tag";s:6:"长靴";s:5:"color";s:0:"";}i:4;a:2:{s:3:"tag";s:9:"机车靴";s:5:"color";s:7:"#E60E6E";}i:5;a:2:{s:3:"tag";s:6:"雨鞋";s:5:"color";s:0:"";}i:6;a:2:{s:3:"tag";s:9:"运动鞋";s:5:"color";s:0:"";}i:7;a:2:{s:3:"tag";s:9:"坡跟鞋";s:5:"color";s:0:"";}i:8;a:2:{s:3:"tag";s:9:"马丁鞋";s:5:"color";s:7:"#E60E6E";}i:9;a:2:{s:3:"tag";s:9:"高跟鞋";s:5:"color";s:0:"";}i:10;a:2:{s:3:"tag";s:9:"及踝鞋";s:5:"color";s:7:"#E60E6E";}i:11;a:2:{s:3:"tag";s:9:"帆布鞋";s:5:"color";s:0:"";}}', 3, 1),
(4, '包包', 5, './public/upload/m/baobao.png', 'a:11:{i:0;a:2:{s:3:"tag";s:9:"旅行箱";s:5:"color";s:7:"#E60E6E";}i:1;a:2:{s:3:"tag";s:9:"环保袋";s:5:"color";s:0:"";}i:2;a:2:{s:3:"tag";s:9:"双肩包";s:5:"color";s:0:"";}i:3;a:2:{s:3:"tag";s:9:"化妆包";s:5:"color";s:0:"";}i:4;a:2:{s:3:"tag";s:6:"钱包";s:5:"color";s:7:"#E60E6E";}i:5;a:2:{s:3:"tag";s:9:"邮差包";s:5:"color";s:0:"";}i:6;a:2:{s:3:"tag";s:9:"菱格包";s:5:"color";s:7:"#E60E6E";}i:7;a:2:{s:3:"tag";s:9:"相机包";s:5:"color";s:0:"";}i:8;a:2:{s:3:"tag";s:9:"单肩包";s:5:"color";s:0:"";}i:9;a:2:{s:3:"tag";s:9:"链条包";s:5:"color";s:7:"#E60E6E";}i:10;a:2:{s:3:"tag";s:9:"水桶包";s:5:"color";s:0:"";}}', 4, 1),
(5, '配饰', 6, './public/upload/m/peishi.png', 'a:12:{i:0;a:2:{s:3:"tag";s:6:"围巾";s:5:"color";s:7:"#E60E6E";}i:1;a:2:{s:3:"tag";s:6:"手套";s:5:"color";s:7:"#E60E6E";}i:2;a:2:{s:3:"tag";s:6:"耳罩";s:5:"color";s:0:"";}i:3;a:2:{s:3:"tag";s:6:"帽子";s:5:"color";s:7:"#E60E6E";}i:4;a:2:{s:3:"tag";s:6:"镜框";s:5:"color";s:0:"";}i:5;a:2:{s:3:"tag";s:6:"耳钉";s:5:"color";s:0:"";}i:6;a:2:{s:3:"tag";s:6:"发箍";s:5:"color";s:0:"";}i:7;a:2:{s:3:"tag";s:6:"围脖";s:5:"color";s:7:"#E60E6E";}i:8;a:2:{s:3:"tag";s:6:"腰带";s:5:"color";s:0:"";}i:9;a:2:{s:3:"tag";s:6:"戒指";s:5:"color";s:0:"";}i:10;a:2:{s:3:"tag";s:6:"发夹";s:5:"color";s:0:"";}i:11;a:2:{s:3:"tag";s:6:"手表";s:5:"color";s:7:"#E60E6E";}}', 5, 1),
(6, '彩妆', 7, './public/upload/m/caizhuang.png', 'a:12:{i:0;a:2:{s:3:"tag";s:6:"香水";s:5:"color";s:7:"#E60E6E";}i:1;a:2:{s:3:"tag";s:6:"清洁";s:5:"color";s:0:"";}i:2;a:2:{s:3:"tag";s:9:"爽肤水";s:5:"color";s:0:"";}i:3;a:2:{s:3:"tag";s:6:"乳霜";s:5:"color";s:0:"";}i:4;a:2:{s:3:"tag";s:6:"唇膏";s:5:"color";s:7:"#E60E6E";}i:5;a:2:{s:3:"tag";s:6:"面膜";s:5:"color";s:7:"#E60E6E";}i:6;a:2:{s:3:"tag";s:6:"美白";s:5:"color";s:0:"";}i:7;a:2:{s:3:"tag";s:6:"眼妆";s:5:"color";s:0:"";}i:8;a:2:{s:3:"tag";s:6:"祛痘";s:5:"color";s:7:"#E60E6E";}i:9;a:2:{s:3:"tag";s:6:"底妆";s:5:"color";s:0:"";}i:10;a:2:{s:3:"tag";s:5:"BB霜";s:5:"color";s:0:"";}i:11;a:2:{s:3:"tag";s:6:"美甲";s:5:"color";s:7:"#E60E6E";}}', 6, 1),
(7, '家居', 8, './public/upload/m/jiaju.png', 'a:12:{i:0;a:2:{s:3:"tag";s:9:"手机壳";s:5:"color";s:7:"#E60E6E";}i:1;a:2:{s:3:"tag";s:9:"保温杯";s:5:"color";s:0:"";}i:2;a:2:{s:3:"tag";s:6:"礼品";s:5:"color";s:7:"#E60E6E";}i:3;a:2:{s:3:"tag";s:6:"靠垫";s:5:"color";s:0:"";}i:4;a:2:{s:3:"tag";s:6:"餐具";s:5:"color";s:0:"";}i:5;a:2:{s:3:"tag";s:9:"收纳盒";s:5:"color";s:0:"";}i:6;a:2:{s:3:"tag";s:6:"宜家";s:5:"color";s:7:"#E60E6E";}i:7;a:2:{s:3:"tag";s:6:"抱枕";s:5:"color";s:0:"";}i:8;a:2:{s:3:"tag";s:6:"相机";s:5:"color";s:0:"";}i:9;a:2:{s:3:"tag";s:12:"床上用品";s:5:"color";s:7:"#E60E6E";}i:10;a:2:{s:3:"tag";s:6:"沙发";s:5:"color";s:0:"";}i:11;a:2:{s:3:"tag";s:6:"地毯";s:5:"color";s:0:"";}}', 7, 1);