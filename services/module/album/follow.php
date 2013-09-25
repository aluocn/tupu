<?php
	global $_FANWE;
	$uid = (int)$_FANWE['uid'];
	$aid = (int)$_FANWE['request']['album_id'];
	if(!$uid)
		exit;
		
	$result = array();
	$is_follow = FS('album')->followAlbum($aid,$uid);
	
	
	if($is_follow < 0)
		$result['status'] = 0;
	else
		$result['status'] = 1;
	$result['is_follow'] = $is_follow;
	
	
	outputJson($result);
?>