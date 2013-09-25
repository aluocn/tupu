<?php
if(intval($_FANWE['user']['is_share']) > 0){
	$go_url = FU("index/index");
	$cache_file = getTplCache('services/user/notallowshare');
	
	if(!@include($cache_file))
	{
		include template('services/user/notallowshare');
	}
}else{
	$cache_file = getTplCache('services/user/addshare');
	if(!@include($cache_file))
	{
		//$login_modules = getLoginModuleList();
		include template('services/user/addshare');
	}
}
display($cache_file);
?>