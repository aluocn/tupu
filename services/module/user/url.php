<?php
$cache_file = getTplCache('services/user/url');
FanweService::instance()->cache->loadCache('albums');

if(!@include($cache_file))
{
	$login_modules = getLoginModuleList();
	include template('services/user/url');
}
display($cache_file);
?>