3.1;
DROP TABLE IF EXISTS `%DB_PREFIX%collect_cate`;
CREATE TABLE `%DB_PREFIX%collect_cate` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `uname` varchar(255) NOT NULL,
  `seo_title` varchar(255) default NULL,
  `seo_keywords` text,
  `seo_desc` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%page_seo`;
CREATE TABLE `%DB_PREFIX%page_seo` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `action_name` varchar(255) NOT NULL,
  `seo_title` varchar(255) default NULL,
  `seo_keywords` text,
  `seo_desc` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `%DB_PREFIX%role_nav` VALUES ('12', 'seo优化', '1', '9');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1122', 'index', '采集分类列表', '1', 'CollectCate', '采集分类管理', '12', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1123', 'add', '采集分类添加', '1', 'CollectCate', '采集分类管理', '12', '10', '0', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1124', 'edit', '采集分类编辑', '1', 'CollectCate', '采集分类管理', '12', '10', '0', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1125', 'remove', '删除分类', '1', 'CollectCate', '采集分类管理', '12', '10', '0', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1126', '', '', '1', 'CollectCate', '采集分类管理', '12', '10', '1', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1127', '', '', '1', 'PageSeo', '页面优化', '12', '10', '1', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1128', 'index', '页面列表', '1', 'PageSeo', '页面优化', '12', '10', '0', '1');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1129', 'add', '页面添加', '1', 'PageSeo', '页面优化', '12', '10', '0', '0');
INSERT IGNORE INTO `%DB_PREFIX%role_node` VALUES ('1130', 'edit', '页面编辑', '1', 'PageSeo', '页面优化', '12', '10', '0', '0');


INSERT IGNORE INTO `%DB_PREFIX%collect_cate` VALUES ('10', '热门采集', 'hot', '', '', '');
INSERT IGNORE INTO `%DB_PREFIX%collect_cate` VALUES ('11', '美图采集', 'pic', '', '', '');
INSERT IGNORE INTO `%DB_PREFIX%collect_cate` VALUES ('12', '视频采集', 'video', '', '', '');
INSERT IGNORE INTO `%DB_PREFIX%collect_cate` VALUES ('13', '商品采集', 'goods', '', '', '');

INSERT IGNORE INTO `%DB_PREFIX%page_seo` VALUES ('1', '“什么是最旅”页', 'index', '', '', '');
INSERT IGNORE INTO `%DB_PREFIX%page_seo` VALUES ('2', '“采集小工具”页', 'goodies', '', '', '');
INSERT IGNORE INTO `%DB_PREFIX%page_seo` VALUES ('3', '“帮助”页', 'help', '', '', '');
INSERT IGNORE INTO `%DB_PREFIX%page_seo` VALUES ('4', '“最新礼仪”页', 'etiquette', '', '', '');

ALTER TABLE `%DB_PREFIX%user` ADD COLUMN  `avatar` int(11) DEFAULT '0';

UPDATE  `%DB_PREFIX%sys_conf` set val='newhuaban'  where name='SITE_TMPL';
UPDATE `%DB_PREFIX%sys_conf` SET `val` = '3.1' WHERE `name` = 'SYS_VERSION';