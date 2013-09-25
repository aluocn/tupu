<?php
$_FANWE['request']['uid'] = $_FANWE['uid'];

$data = array();
$data['share_id'] = intval($_FANWE['request']['share_id']);
$data['album_id'] = intval($_FANWE['request']['albumid']);
$data['old_album_id'] = intval($_FANWE['request']['old_album_id']);
$data['title'] = $_FANWE['request']['title'];
$data['text'] = $_FANWE['request']['text'];
$data['server_code']=$_FANWE['request']['server_code'];


$txt = stripcslashes($_FANWE['request']['text']);
preg_match('/src=\"(.*?)\"/',$txt,$matches);
$img_url=$matches[1];
if($data['server_code']){
	$data['src']= $_FANWE['site_url'].$img_url;
	$o_img=FS('Image')->updateImage($data,true);
}else{
	$img_url=str_replace($_FANWE['site_root'],"",$img_url);
	$img_url = FANWE_ROOT."./".$img_url;
	$o_img = copyImage($img_url,array(),'share',false,$_FANWE['request']['share_id']);	
}
$data['img'] = $o_img['url'];

$data['photo_id'] = intval($_FANWE['request']['photo_id']);

if(empty($data['text']) || empty($data['album_id']))
{
	$result['status'] = 0;
	$result['error_msg'] = '没选择杂志社或者文章的内容为空,请重新选择';
	outputJson($result);
}
$upImg = false;
if(!empty($data['img']))
{
	$upImg = true;
}

$share_sql = "update ".FDB::table("share")." set content = '".$data['title']."',share_content_match = '".segmentToUnicode(clearSymbol($data['title']))."' where share_id = ".$data['share_id'];

if($upImg){
	$share_photo_sql = "update ".FDB::table("share_photo")." set img = '".$data['img']."',img_width = ".$o_img['width'].",img_height = ".$o_img['height'].",title='".$data['title']."',text='".$data['text']."' where photo_id = ".$data['photo_id'];	
}else{
	$share_photo_sql = "update ".FDB::table("share_photo")." set img = '',img_width = 0,img_height = 0,title='".$data['title']."',text='".$data['text']."' where photo_id = ".$data['photo_id'];
}
	

$album_share_sql = "select * from ".FDB::table("album_share")." where album_id = ".$data['album_id']." and share_id = ".$data['share_id'];


if(!FDB::fetchFirst($album_share_sql))
{
	FDB::query("delete from ".FDB::table("album_share")." where share_id =".$data['share_id']);
	FDB::query("update ".FDB::table("album")." set share_count = paper_count - 1 where id =".$data['old_album_id']);
	FDB::query("update ".FDB::table("album")." set share_count = paper_count + 1 where id =".$data['album_id']);
	
	$cid = FDB::resultFirst("select cid from ".FDB::table("album")." where id = ".$data['album_id']);
	
	if($cid)
	{
		$album_data = array();
		$album_data['album_id'] = $data['album_id'];
		$album_data['share_id'] = $data['share_id'];
		
		$album_data['cid'] = $cid;
		$album_data['create_day'] = TIME_UTC;
		
		$sql = "INSERT INTO ".FDB::table('album_share')." SET album_id=".$album_data['album_id'].",share_id=".$album_data['share_id'].",cid=".$album_data['cid'].",create_day='".$album_data['create_day']."'";
		if(FDB::query($sql))
		{
			FDB::query($share_sql);
			FDB::query($share_photo_sql);
		}
		$result['status'] = 1;
		$result['url'] = FU("note/index",array("sid"=>$data['share_id']));
		outputJson($result);					
	}
	else
	{
		$result['status'] = 0;
		$result['error_msg'] = '杂志社不存在,更新失败';
		outputJson($result);
	}
}
else 
{
	FDB::query($share_sql);
	FDB::query($share_photo_sql);
	
	$result['status'] = 1;
	$result['url'] = FU("note/index",array("sid"=>$data['share_id']));		
	outputJson($result);
}
?>