2.11;

CREATE TABLE IF NOT EXISTS `%DB_PREFIX%album_follow`  (
  `f_uid` int(11) DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0',
  KEY `album_id` (`album_id`),
  KEY `f_uid` (`f_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE  `%DB_PREFIX%share_photo` ADD  `video` VARCHAR( 255 ) NOT NULL;

ALTER TABLE  `%DB_PREFIX%share_photo` CHANGE  `type`  `type` ENUM(  'default',  'dapei',  'look',  'video' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'default';

ALTER TABLE `%DB_PREFIX%album` ADD COLUMN `follow_count` int(11) NULL DEFAULT 0;


INSERT IGNORE INTO `%DB_PREFIX%role_node` (`id`, `action`, `action_name`, `status`, `module`, `module_name`, `nav_id`, `sort`, `auth_type`, `is_show`) VALUES (1115,'', '', 1, 'DianGao', '点高广告API', 7, 10, 1, 1),
(1116,'index', '一键开通', 1, 'DianGao', '点高广告API', 7, 10, 0, 1);

INSERT IGNORE INTO `%DB_PREFIX%role_access` (`role_id`, `node_id`) VALUES (1, 1115);

DELETE FROM `%DB_PREFIX%sharegoods_module` WHERE `id` = 1 LIMIT 1;
DELETE FROM `%DB_PREFIX%sharegoods_module` WHERE `id` = 2 LIMIT 1;

UPDATE `%DB_PREFIX%sys_conf` SET `val` = '2.11' WHERE `name` = 'SYS_VERSION';
