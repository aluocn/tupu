<?php
$_FANWE['request']['uid'] = $_FANWE['uid'];


$data = array();
$data['share_id'] = intval($_FANWE['request']['share_id']);
$data['content'] = htmlspecialchars($_FANWE['request']['content']);
$data['album_id'] = intval($_FANWE['request']['albumid']);
$data['old_album_id'] = intval($_FANWE['request']['old_album_id']);
$server_code = $_FANWE['request']['server_code'];
$is_new = intval($_FANWE['request']['is_new']);
$data['src'] = $_FANWE['site_url'].$_FANWE['request']['img_url'];
$data['server_code']=$server_code;
if($is_new){
	if($server_code){
		$o_img=FS("Image")->updateImage($data,true);	
	}else{
		$o_img = copyImage(FANWE_ROOT.$_FANWE['request']['img_url'],md5(microtime(true)).random('6').".jpg",'share',true,$_FANWE['request']['share_id']);		
	}
}
$data['img'] = $o_img['url'];		
$data['photo_id'] = intval($_FANWE['request']['photo_id']);

if(empty($data['content']) || empty($data['album_id']) || empty($data['photo_id']))
{
	$result['status'] = 0;
	outputJson($result);
}
$upImg = false;

if(!empty($data['img']))
{
	$upImg = true;
}

$share_sql = "update ".FDB::table("share")." set content = '".$data['content']."',share_content_match = '".segmentToUnicode(clearSymbol($data['content']))."' where share_id = ".$data['share_id'];

$share_photo_sql = "update ".FDB::table("share_photo")." set img = '".$data['img']."',img_width = ".$o_img['width'].",img_height = ".$o_img['height']." where photo_id = ".$data['photo_id'];
$album_share_sql = "select * from ".FDB::table("album_share")." where album_id = ".$data['album_id']." and share_id = ".$data['share_id'];
if(!FDB::fetchFirst($album_share_sql))
{
	FDB::query("delete from ".FDB::table("album_share")." where share_id =".$data['share_id']);
	FDB::query("update ".FDB::table("album")." set share_count = share_count - 1 where id =".$data['old_album_id']);
	FDB::query("update ".FDB::table("album")." set share_count = share_count + 1 where id =".$data['album_id']);
	
	$cid = FDB::resultFirst("select cid from ".FDB::table("album")." where id = ".$data['album_id']);
	if($cid)
	{
		$album_data = array();
		$album_data['album_id'] = $data['album_id'];
		$album_data['share_id'] = $data['share_id'];
		
		$album_data['cid'] = $cid;
		$album_data['create_day'] = TIME_UTC;
		FDB::insert('album_share',$album_data,false);
		FDB::query($share_sql);
		if($upImg)
			FDB::query($share_photo_sql);
		FDB::query("update from ".FDB::table("album")." set img_count = 1 where id = ".$data['album_id']);
		$result['status'] = 1;
		outputJson($result);					
	}
	else
	{
		$result['status'] = 0;
		outputJson($result);
	}
}
else 
{
	FDB::query($share_sql);
	if($upImg)
	{
		FDB::query($share_photo_sql);
	}
	$result['status'] = 1;
	outputJson($result);
}
?>