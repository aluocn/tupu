<?php
global $_FANWE;
$cache_file = getTplCache('services/user/create_board');
	if(!@include($cache_file))
	{	
		$login_modules = getLoginModuleList();
		$list = FS("Album")->getAlbumListByUid($_FANWE['uid']);
		$categoryList = FS("Album")->getAlbumCategory(0,"└");
		include template('services/user/create_board');
	}
	display($cache_file);

?>