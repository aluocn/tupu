<?php
global $_FANWE;
FanweService::instance()->cache->loadCache('albums');

$share_id = intval($_FANWE['request']['id']);
if($share_id == 0)
	exit;
	
//未登陆直接退出
$uid = $_FANWE['uid'];
if($uid == 0)
	exit;

$parent_data = FS('Share')->getShareById($share_id);
if(empty($parent_data))
	exit;

$parent_user = FS('User')->getUserCache($parent_data['uid']);
$img = FDB::fetchFirst("select img,server_code from ".FDB::table("share_photo")." where share_id = ".$share_id);
$img['img']=getImgName($img['img'],190,190,1,$img['server_code']);
$img_array = FDB::fetchFirst("select * from ".FDB::table("share_photo")." where share_id = ".$share_id);
if($img_array['type'] == 'paper'){
	if(empty($img_array['img'])){
		$no_img = 1;
	}else{
		$no_img = 0;
	}
}

$content = '';
$title = $parent_data['content'];
$is_base = false;
if($parent_data['base_id'] > 0)
{
	$base_data = FS('Share')->getShareById($parent_data['base_id']);
	if(!empty($base_data))
	{
		$is_base = true;
		$base_user = FS('User')->getUserCache($base_data['uid']);
		$title = $base_data['content'];
	}
	
	$content = '//@'.$parent_user['user_name'].':'.$parent_data['content'];
}
include template("services/share/relay");
display();
?>