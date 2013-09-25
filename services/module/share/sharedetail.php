<?php
	global $_FANWE;
	
	$share_id = (int)$_FANWE['request']['share_id'];
	
	if(!$share_id)
		exit;
	
	$img = FDB::resultFirst('select img from '.FDB::table('share_photo').' where share_id = '.$share_id);
	
	$args = array(
		'img'=>$img
	);
	if($img)
		$result['status'] = 1;
	else
		$result['status'] = 0;
	
	$result['html'] = tplFetch('services/share/detail',$args);
	
	outputJson($result)
?>