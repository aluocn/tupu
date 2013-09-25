3.2;
DROP TABLE IF EXISTS `%DB_PREFIX%adv`;
CREATE TABLE `%DB_PREFIX%adv` (
  `id` int(11) NOT NULL auto_increment,
  `position_id` mediumint(8) NOT NULL,
  `name` varchar(20) NOT NULL,
  `code` text NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1: 图片 2:flash 3:文字 4:外部调用',
  `status` tinyint(1) NOT NULL default '1',
  `url` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `target_key` varchar(60) NOT NULL default '',
  `sort` int(11) NOT NULL default '10',
  PRIMARY KEY  (`id`),
  KEY `position_id` (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- ----------------------------
-- Records of %DB_PREFIX%adv
-- ----------------------------
INSERT INTO `%DB_PREFIX%adv` VALUES ('1', '1', '首页轮播广告位1', './public/upload/adv/201301/28/5106175b76d02.jpg', '1', '1', '', '', '', '100');
INSERT INTO `%DB_PREFIX%adv` VALUES ('2', '1', '首页轮播广告位2', './public/upload/adv/201301/28/5106176395d3e.jpg', '1', '1', '', '', '', '100');
INSERT INTO `%DB_PREFIX%adv` VALUES ('3', '1', '首页轮播广告位3', './public/upload/adv/201301/28/5106176bc32b9.jpg', '1', '1', '', '', '', '100');


DROP TABLE IF EXISTS `%DB_PREFIX%adv_layout`;
CREATE TABLE `%DB_PREFIX%adv_layout` (
  `id` int(11) NOT NULL auto_increment,
  `page` varchar(255) default NULL,
  `layout_id` varchar(255) default NULL,
  `tmpl` varchar(255) default NULL,
  `rec_id` int(11) default NULL,
  `item_limit` int(11) default NULL,
  `target_id` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` USING BTREE (`page`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of %DB_PREFIX%adv_layout
-- ----------------------------
INSERT INTO `%DB_PREFIX%adv_layout` VALUES ('1', '/inc/header', '首页轮播广告位', 'newhuaban', '1', '0', '');

DROP TABLE IF EXISTS `%DB_PREFIX%adv_position`;
CREATE TABLE `%DB_PREFIX%adv_position` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  `width` smallint(5) unsigned NOT NULL default '0',
  `height` smallint(5) unsigned NOT NULL default '0',
  `is_flash` tinyint(1) NOT NULL default '0',
  `flash_style` varchar(60) NOT NULL,
  `bgcolor` varchar(10) NOT NULL,
  `style` text NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- ----------------------------
-- Records of %DB_PREFIX%adv_position
-- ----------------------------
INSERT INTO `%DB_PREFIX%adv_position` VALUES ('1', '首页轮播广告位', '930', '220', '0', '', '#c80000', '<table cellpadding=\"0\" cellspacing=\"0\">\r\n{loop $adv_list $adv}\r\n<tr><td>{$adv[html]}</td></tr>\r\n{/loop}\r\n</table>', '1');

DROP TABLE IF EXISTS `%DB_PREFIX%image_servers`;
CREATE TABLE `%DB_PREFIX%image_servers` (
  `id` smallint(6) NOT NULL auto_increment,
  `code` varchar(30) NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '1',
  `upload_count` bigint(20) unsigned NOT NULL default '0',
  `url_rewrite` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `upload_count` (`upload_count`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `%DB_PREFIX%role_nav` VALUES ('13', '前台', '1', '10');

INSERT INTO `%DB_PREFIX%role_node` VALUES ('1134', 'index', '广告位列表', '1', 'AdvPosition', '广告位管理', '13', '10', '0', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1133', '', '', '1', 'AdvPosition', '广告位管理', '13', '10', '1', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1135', 'add', '广告位添加', '1', 'AdvPosition', '广告位管理', '13', '10', '0', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1136', '', '', '1', 'Adv', '广告管理', '13', '10', '1', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1137', 'index', '广告列表', '1', 'Adv', '广告管理', '13', '10', '0', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1138', 'add', '广告添加', '1', 'Adv', '广告管理', '13', '10', '0', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1139', '', '', '1', 'AdvLayout', '广告布局管理', '13', '10', '1', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1140', 'index', '广告布局列表', '1', 'AdvLayout', '广告布局管理', '13', '10', '0', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1141', 'add', '广告布局添加', '1', 'AdvLayout', '广告布局管理', '13', '10', '0', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1142', '', '', '1', 'GoogleAdv', '谷歌广告', '7', '10', '1', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1143', 'index', '设置谷歌广告', '1', 'GoogleAdv', '谷歌广告', '7', '10', '0', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1144', 'update', '更新谷歌广告', '1', 'GoogleAdv', '谷歌广告', '7', '10', '0', '0');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1145', '', '', '1', 'ImageServers', '图片服务器', '7', '10', '1', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1146', 'index', '图片服务器列表', '1', 'ImageServers', '图片服务器', '7', '10', '0', '1');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('1147', 'add', '添加图片服务器', '1', 'ImageServers', '图片服务器', '7', '10', '0', '1');

INSERT INTO `%DB_PREFIX%sys_conf` VALUES ('IS_OPEN_REGISTER', '1', '1', '8', '2', '0,1', '6', '0', '0');
INSERT INTO `%DB_PREFIX%sys_conf` VALUES ('IS_MAIL_ACTIVATE', '0', '1', '8', '1', '0,1', '6', '0', '0');
INSERT INTO `%DB_PREFIX%sys_conf` VALUES ('CLOSE_REGISTER_DESC', '关闭注册', '1', '8', '8', '0', '6', '0', '0');
INSERT INTO `%DB_PREFIX%sys_conf` VALUES ('SHOP_CLOSED', '0', '1', '2', '2', '0,1', '1', '1', '0');

ALTER TABLE `%DB_PREFIX%share` CHANGE `share_data` `share_data` ENUM('goods','photo','default','paper','video','goods_photo') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'default';
ALTER TABLE `%DB_PREFIX%share_photo` CHANGE `type` `type` ENUM('default','dapei','look','paper','video') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'default';
ALTER TABLE `%DB_PREFIX%share_photo` ADD COLUMN `title` varchar(255) NOT NULL;
ALTER TABLE `%DB_PREFIX%share_photo` ADD COLUMN `text` text;
ALTER TABLE `%DB_PREFIX%share_photo` ADD COLUMN `adv_url` varchar(255) default NULL;

ALTER TABLE `%DB_PREFIX%album` ADD COLUMN `paper_count` int(11) NOT NULL default '0';

ALTER TABLE `%DB_PREFIX%album_category` ADD COLUMN `parent_id` smallint(6) NOT NULL default '0';
ALTER TABLE `%DB_PREFIX%user_count` ADD COLUMN `papers` int(11) NOT NULL default '0';
ALTER TABLE `%DB_PREFIX%user` ADD COLUMN `active_sn` varchar(255) default '';
ALTER TABLE `%DB_PREFIX%user` ADD COLUMN `is_share` smallint(1) NOT NULL default '0';
INSERT INTO `%DB_PREFIX%collect_cate` VALUES ('14', '文章分享', 'paper', '', '', '');
UPDATE `%DB_PREFIX%sharegoods_module` set api_data='a:5:{s:3:"uin";s:0:"";s:10:"appoauthid";s:0:"";s:11:"appoauthkey";s:0:"";s:11:"accesstoken";s:0:"";s:6:"userid";s:0:"";}' where class='paipai';
UPDATE `%DB_PREFIX%sys_conf` set val='方维兴趣分享系统 系统版本：v3.2版权所有&copy; 方维',val_arr='方维兴趣分享系统 系统版本：v3.2版权所有© 方维'  where name='FOOTER_HTML';
UPDATE `%DB_PREFIX%sys_conf` set val='newhuaban'  where name='SITE_TMPL';
UPDATE `%DB_PREFIX%sys_conf` SET `val` = '3.2' WHERE `name` = 'SYS_VERSION';