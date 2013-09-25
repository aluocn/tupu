2.13;



INSERT IGNORE INTO `%DB_PREFIX%sys_conf` VALUES ('IPHONE_APK_URL', 'http://itunes.apple.com', '1', '0', '0', '', '1', '1', '0');
INSERT IGNORE INTO `%DB_PREFIX%sys_conf` VALUES ('ANDROID_APK_URL', 'http://itunes.apple.com/us/app/qu-gou-jie/id495602483?ls=1&mt=8', '1', '0', '0', '', '1', '1', '0');


UPDATE `%DB_PREFIX%sys_conf` SET `val` = '2.13' WHERE `name` = 'SYS_VERSION';