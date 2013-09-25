2.12;

ALTER TABLE  `%DB_PREFIX%share_photo` ADD  `is_animate` INT( 1 ) NOT NULL;

INSERT IGNORE INTO `%DB_PREFIX%sys_conf` (`name`, `val`, `status`, `sort`, `list_type`, `val_arr`, `group_id`, `is_show`, `is_js`) VALUES
('AUDIT_INDEX', '0', 1, 10, 2, '0,1', 1, 1, 0);

DELETE FROM `%DB_PREFIX%role_node` WHERE `%DB_PREFIX%role_node`.`id` = 60 LIMIT 1;
DELETE FROM `%DB_PREFIX%role_node` WHERE `%DB_PREFIX%role_node`.`id` = 61 LIMIT 1;
DELETE FROM `%DB_PREFIX%role_node` WHERE `%DB_PREFIX%role_node`.`id` = 62 LIMIT 1;
DELETE FROM `%DB_PREFIX%role_node` WHERE `%DB_PREFIX%role_node`.`id` = 63 LIMIT 1;
DELETE FROM `%DB_PREFIX%role_node` WHERE `%DB_PREFIX%role_node`.`id` = 64 LIMIT 1;


DELETE FROM `%DB_PREFIX%role_node` WHERE `%DB_PREFIX%role_node`.`id` = 4 LIMIT 1;
DELETE FROM `%DB_PREFIX%role_node` WHERE `%DB_PREFIX%role_node`.`id` = 5 LIMIT 1;
DELETE FROM `%DB_PREFIX%role_node` WHERE `%DB_PREFIX%role_node`.`id` = 6 LIMIT 1;


UPDATE `%DB_PREFIX%sys_conf` SET `val` = '2.12' WHERE `name` = 'SYS_VERSION';
