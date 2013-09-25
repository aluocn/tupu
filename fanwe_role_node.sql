/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50151
Source Host           : localhost:3306
Source Database       : zuilv

Target Server Type    : MYSQL
Target Server Version : 50151
File Encoding         : 65001

Date: 2012-02-29 14:14:08
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `fanwe_role_node`
-- ----------------------------
DROP TABLE IF EXISTS `fanwe_role_node`;
CREATE TABLE `fanwe_role_node` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `action` varchar(60) NOT NULL DEFAULT '',
  `action_name` varchar(60) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `module` varchar(60) NOT NULL DEFAULT '',
  `module_name` varchar(60) NOT NULL DEFAULT '',
  `nav_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '从属于哪个模块组, 为0时表示不属于菜单节点',
  `sort` smallint(5) NOT NULL DEFAULT '0',
  `auth_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '授权模式：1:模块授权(module) 2:操作授权(action) 0:节点授权(node)',
  `is_show` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=234 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_role_node
-- ----------------------------
INSERT INTO `fanwe_role_node` VALUES ('1', '', '', '1', 'SysConf', '系统管理', '7', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('2', 'index', '系统设置', '1', 'SysConf', '系统管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('3', 'update', '更新设置', '1', 'SysConf', '系统管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('4', '', '', '1', 'SharegoodsModule', '商品接口管理', '7', '9', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('5', 'index', '接口列表', '1', 'SharegoodsModule', '商品接口管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('6', 'update', '更新接口', '1', 'SharegoodsModule', '商品接口管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('7', '', '', '1', 'LoginModule', '同步登陆管理', '7', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('8', 'index', '模块列表', '1', 'LoginModule', '同步登陆管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('9', 'update', '更新模块', '1', 'LoginModule', '同步登陆管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('10', '', '', '1', 'Cache', '缓存管理', '7', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('11', 'system', '清除系统缓存', '1', 'Cache', '缓存管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('12', 'custom', '清除程序缓存', '1', 'Cache', '缓存管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('13', '', '', '1', 'TempFile', '临时文件管理', '7', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('14', 'index', '临时文件列表', '1', 'TempFile', '临时文件管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('15', 'clear', '清除临时文件', '1', 'TempFile', '临时文件管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('16', '', '', '1', 'AdminLog', '操作日志管理', '7', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('17', 'index', '操作日志列表', '1', 'AdminLog', '操作日志管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('18', 'remove', '删除操作日志', '1', 'AdminLog', '操作日志管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('19', '', '', '1', 'Region', '城市管理', '7', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('20', 'index', '城市列表', '1', 'Region', '城市管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('21', 'add', '添加城市', '1', 'Region', '城市管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('22', 'update', '更新城市', '1', 'Region', '城市管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('23', 'remove', '删除城市', '1', 'Region', '城市管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('24', '', '', '1', 'DataBase', '数据库操作', '6', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('25', 'index', '数据库备份', '1', 'DataBase', '数据库操作', '6', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('26', 'dump', '备份操作', '1', 'DataBase', '数据库操作', '6', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('27', 'delete', '删除操作', '1', 'DataBase', '数据库操作', '6', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('28', 'restore', '恢复操作', '1', 'DataBase', '数据库操作', '6', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('29', '', '', '1', 'Sql', 'SQL操作', '6', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('30', 'index', 'SQL操作', '1', 'Sql', 'SQL操作', '6', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('31', 'execute', '执行SQL', '1', 'Sql', 'SQL操作', '6', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('32', '', '', '1', 'Admin', '管理员管理', '5', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('33', 'index', '管理员列表', '1', 'Admin', '管理员管理', '5', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('34', 'add', '添加管理员', '1', 'Admin', '管理员管理', '5', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('35', 'update', '更新管理员', '1', 'Admin', '管理员管理', '5', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('36', 'remove', '删除管理员', '1', 'Admin', '管理员管理', '5', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('37', '', '', '1', 'Role', '权限组管理', '5', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('38', 'index', '角色列表', '1', 'Role', '权限组管理', '5', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('39', 'add', '添加角色', '1', 'Role', '权限组管理', '5', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('40', 'update', '更新角色', '1', 'Role', '权限组管理', '5', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('41', 'remove', '删除角色', '1', 'Role', '权限组管理', '5', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('42', '', '', '1', 'RoleNode', '权限节点管理', '5', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('43', 'index', '节点列表', '1', 'RoleNode', '权限节点管理', '5', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('44', 'add', '添加节点', '1', 'RoleNode', '权限节点管理', '5', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('45', 'update', '更新节点', '1', 'RoleNode', '权限节点管理', '5', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('46', 'remove', '删除节点', '1', 'RoleNode', '权限节点管理', '5', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('47', '', '', '1', 'RoleNav', '后台导航菜单管理', '5', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('48', 'index', '菜单列表', '1', 'RoleNav', '后台导航菜单管理', '5', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('49', 'add', '添加菜单', '1', 'RoleNav', '后台导航菜单管理', '5', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('50', 'update', '更新菜单', '1', 'RoleNav', '后台导航菜单管理', '5', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('51', 'remove', '删除菜单', '1', 'RoleNav', '后台导航菜单管理', '5', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('52', '', '', '1', 'UserSetting', '会员配置管理', '4', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('53', 'index', '设置配置', '1', 'UserSetting', '会员配置管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('54', 'update', '更新配置', '1', 'UserSetting', '会员配置管理', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('55', '', '', '1', 'User', '会员管理', '4', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('56', 'index', '会员列表', '1', 'User', '会员管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('57', 'add', '添加会员', '1', 'User', '会员管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('58', 'update', '更新会员', '1', 'User', '会员管理', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('59', 'remove', '删除会员', '1', 'User', '会员管理', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('60', '', '', '1', 'UserDaren', '达人管理', '4', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('61', 'index', '达人列表', '1', 'UserDaren', '达人管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('62', 'add', '添加达人', '1', 'UserDaren', '达人管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('63', 'update', '更新达人', '1', 'UserDaren', '达人管理', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('64', 'remove', '删除达人', '1', 'UserDaren', '达人管理', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('65', '', '', '1', 'UserGroup', '会员组管理', '4', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('66', 'index', '会员组列表', '1', 'UserGroup', '会员组管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('67', 'add', '添加会员组', '1', 'UserGroup', '会员组管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('68', 'update', '更新会员组', '1', 'UserGroup', '会员组管理', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('69', 'remove', '删除会员组', '1', 'UserGroup', '会员组管理', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('70', '', '', '1', 'Forum', '论坛分类管理', '3', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('71', 'index', '分类列表', '1', 'Forum', '论坛分类管理', '3', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('72', 'add', '添加分类', '1', 'Forum', '论坛分类管理', '3', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('73', 'update', '更新分类', '1', 'Forum', '论坛分类管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('74', 'remove', '删除分类', '1', 'Forum', '论坛分类管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('75', '', '', '1', 'ForumThread', '论坛主题管理', '3', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('76', 'index', '主题列表', '1', 'ForumThread', '论坛主题管理', '3', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('77', 'update', '更新主题', '1', 'ForumThread', '论坛主题管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('78', 'remove', '删除主题', '1', 'ForumThread', '论坛主题管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('79', '', '', '1', 'Ask', '问答分类管理', '3', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('80', 'index', '分类列表', '1', 'Ask', '问答分类管理', '3', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('81', 'add', '添加分类', '1', 'Ask', '问答分类管理', '3', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('82', 'update', '更新分类', '1', 'Ask', '问答分类管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('83', 'remove', '删除分类', '1', 'Ask', '问答分类管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('84', '', '', '1', 'AskThread', '问答主题管理', '3', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('85', 'index', '主题列表', '1', 'AskThread', '问答主题管理', '3', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('86', 'update', '更新主题', '1', 'AskThread', '问答主题管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('87', 'remove', '删除主题', '1', 'AskThread', '问答主题管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('88', '', '', '1', 'Event', '话题管理', '3', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('89', 'index', '话题列表', '1', 'Event', '话题管理', '3', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('90', 'update', '更新话题', '1', 'Event', '话题管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('91', 'remove', '删除话题', '1', 'Event', '话题管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('92', '', '', '1', 'EventShare', '话题回复管理', '3', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('93', 'index', '话题回复列表', '1', 'EventShare', '话题回复管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('94', 'remove', '删除话题回复', '1', 'EventShare', '话题回复管理', '3', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('95', '', '', '1', 'Share', '分享管理', '2', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('96', 'index', '分享列表', '1', 'Share', '分享管理', '2', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('128', '', '', '1', 'NavCategory', '前台菜单分类管理', '8', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('129', 'index', '分类列表', '1', 'NavCategory', '前台菜单分类管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('130', 'add', '添加分类', '1', 'NavCategory', '前台菜单分类管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('131', 'update', '更新分类', '1', 'NavCategory', '前台菜单分类管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('132', 'remove', '删除分类', '1', 'NavCategory', '前台菜单分类管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('133', '', '', '1', 'Nav', '前台菜单管理', '8', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('134', 'index', '菜单列表', '1', 'Nav', '前台菜单管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('135', 'add', '添加菜单', '1', 'Nav', '前台菜单管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('136', 'update', '更新菜单', '1', 'Nav', '前台菜单管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('137', 'remove', '删除菜单', '1', 'Nav', '前台菜单管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('138', '', '', '1', 'FriendLink', '友情链接管理', '8', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('139', 'index', '链接列表', '1', 'FriendLink', '友情链接管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('140', 'add', '添加链接', '1', 'FriendLink', '友情链接管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('141', 'update', '更新链接', '1', 'FriendLink', '友情链接管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('142', 'remove', '删除链接', '1', 'FriendLink', '友情链接管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('143', '', '', '1', 'UserMsg', '会员信件管理', '4', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('144', 'index', '会员信件列表', '1', 'UserMsg', '会员信件管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('145', 'groupSend', '发送系统信件', '1', 'UserMsg', '会员信件管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('146', 'groupList', '系统信件列表', '1', 'UserMsg', '会员信件管理', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('147', '', '', '1', 'Integrate', '会员整合', '4', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('148', 'index', '会员整合', '1', 'Integrate', '会员整合', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('154', '', '', '1', 'WordType', '敏感词分类管理', '7', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('155', 'index', '分类列表', '1', 'WordType', '敏感词分类管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('156', 'add', '添加分类', '1', 'WordType', '敏感词分类管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('157', 'update', '更新分类', '1', 'WordType', '敏感词分类管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('158', 'remove', '删除分类', '1', 'WordType', '敏感词分类管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('159', '', '', '1', 'Word', '敏感词管理', '7', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('160', 'index', '敏感词列表', '1', 'Word', '敏感词管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('161', 'add', '添加敏感词', '1', 'Word', '敏感词管理', '7', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('162', 'update', '更新敏感词', '1', 'Word', '敏感词管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('163', 'remove', '删除敏感词', '1', 'Word', '敏感词管理', '7', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('164', '', '', '1', 'AdvPosition', '广告位管理', '8', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('165', 'index', '广告位列表', '1', 'AdvPosition', '广告位管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('166', 'add', '添加广告位', '1', 'AdvPosition', '广告位管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('167', 'update', '更新广告位', '1', 'AdvPosition', '广告位管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('168', 'remove', '删除广告位', '1', 'AdvPosition', '广告位管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('169', '', '', '1', 'Adv', '广告管理', '8', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('170', 'index', '广告列表', '1', 'Adv', '广告管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('171', 'add', '添加广告', '1', 'Adv', '广告管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('172', 'update', '添加广告', '1', 'Adv', '广告管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('173', 'remove', '删除广告', '1', 'Adv', '广告管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('174', '', '', '1', 'AdvLayout', '广告布局管理', '8', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('175', 'index', '布局列表', '1', 'AdvLayout', '广告布局管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('176', 'add', '添加布局', '1', 'AdvLayout', '广告布局管理', '8', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('177', 'update', '添加布局', '1', 'AdvLayout', '广告布局管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('178', 'remove', '删除布局', '1', 'AdvLayout', '广告布局管理', '8', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('179', '', '', '1', 'SecondSetting', '二手设置管理', '9', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('180', 'index', '二手设置', '1', 'SecondSetting', '二手设置管理', '9', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('181', 'update', '更新设置', '1', 'SecondSetting', '二手设置管理', '9', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('182', '', '', '1', 'Second', '二手分类管理', '9', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('183', 'index', '分类列表', '1', 'Second', '二手分类管理', '9', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('184', 'add', '添加分类', '1', 'Second', '二手分类管理', '9', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('185', 'update', '更新分类', '1', 'Second', '二手分类管理', '9', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('186', 'remove', '删除分类', '1', 'Second', '二手分类管理', '9', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('187', '', '', '1', 'SecondGoods', '二手商品管理', '9', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('188', 'index', '商品列表', '1', 'SecondGoods', '二手商品管理', '9', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('189', 'update', '更新商品', '1', 'SecondGoods', '二手商品管理', '9', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('190', 'remove', '删除商品', '1', 'SecondGoods', '二手商品管理', '9', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('191', '', '', '1', 'UserScoreLog', '会员积分日志', '4', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('192', 'index', '日志列表', '1', 'UserScoreLog', '会员积分日志', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('193', 'remove', '删除日志', '1', 'UserScoreLog', '会员积分日志', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('194', '', '', '1', 'Referrals', '会员邀请日志', '4', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('195', 'index', '日志列表', '1', 'Referrals', '会员邀请日志', '4', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('196', 'update', '更新日志', '1', 'Referrals', '会员邀请日志', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('197', 'remove', '删除日志', '1', 'Referrals', '会员邀请日志', '4', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('207', '', '', '1', 'AlbumSetting', '杂志社配置管理', '10', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('208', 'index', '设置配置', '1', 'AlbumSetting', '杂志社配置管理', '10', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('209', 'update', '更新配置', '1', 'AlbumSetting', '杂志社配置管理', '10', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('210', '', '', '1', 'AlbumCategory', '杂志社分类管理', '10', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('211', 'index', '分类列表', '1', 'AlbumCategory', '杂志社分类管理', '10', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('212', 'add', '添加分类', '1', 'AlbumCategory', '杂志社分类管理', '10', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('213', 'update', '更新分类', '1', 'AlbumCategory', '杂志社分类管理', '10', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('214', 'remove', '删除分类', '1', 'AlbumCategory', '杂志社分类管理', '10', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('215', '', '', '1', 'Album', '杂志社管理', '10', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('216', 'index', '杂志社列表', '1', 'Album', '杂志社管理', '10', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('217', 'update', '更新杂志社', '1', 'Album', '杂志社管理', '10', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('218', 'remove', '删除杂志社', '1', 'Album', '杂志社管理', '10', '10', '0', '0');
INSERT INTO `fanwe_role_node` VALUES ('220', '', '', '1', 'MConfig', '手机端管理', '11', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('221', 'index', '手机端配置', '1', 'MConfig', '手机端管理', '11', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('222', '', '', '1', 'MAdv', '手机端广告管理', '0', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('223', 'index', '广告列表', '1', 'MAdv', '手机端广告管理', '11', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('224', 'add', '添加广告', '1', 'MAdv', '手机端广告管理', '11', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('225', '', '', '1', 'MIndex', '手机端首页菜单管理', '0', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('226', 'index', '菜单列表', '1', 'MIndex', '手机端首页菜单管理', '11', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('227', 'add', '添加菜单', '1', 'MIndex', '手机端首页菜单管理', '11', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('228', '', '', '1', 'MSearchcate', '手机端搜索页配置', '0', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('229', 'index', '分类列表', '1', 'MSearchcate', '手机端搜索页配置', '11', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('230', 'add', '添加分类', '1', 'MSearchcate', '手机端搜索页配置', '11', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('231', '', '', '1', 'MApns', '信息推送', '0', '10', '1', '0');
INSERT INTO `fanwe_role_node` VALUES ('232', 'index', '发送记录', '1', 'MApns', '信息推送', '11', '10', '0', '1');
INSERT INTO `fanwe_role_node` VALUES ('233', 'add', '发送信息', '1', 'MApns', '信息推送', '11', '10', '0', '1');
