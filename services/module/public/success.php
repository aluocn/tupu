<?php
	$type=$_REQEUST['type'];
	if($type=='create_album'){
		$info='创建成功';
	}else{
		$info='成功';
	}
$cache_file = getTplCache('services/public/success');
if(!@include($cache_file))
{
	$login_modules = getLoginModuleList();
	include template('services/public/success');
}
display($cache_file);

?>