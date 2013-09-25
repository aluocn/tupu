<?php
$cache_file = getTplCache('services/user/goods');
FanweService::instance()->cache->loadCache('albums');

if(!@include($cache_file))
{
	$login_modules = getLoginModuleList();
	include template('services/user/goods');
}
display($cache_file);
?>