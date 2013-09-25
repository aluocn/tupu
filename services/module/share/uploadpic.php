<?php
if(!isset($_FILES['image']) || empty($_FILES['image']))
	exit;
$result = array();
$pic = $_FILES['image'];
include_once fimport('class/image');
$image = new Image();
if(intval($_FANWE['setting']['max_upload']) > 0)
	$image->max_size = intval($_FANWE['setting']['max_upload']);
$image->init($pic);

if($image->save())
{
	$res['img'] = $image->file['target'];
	$result['status'] = 1;
	$info = array('path'=>$image->file['local_target'],'type'=>$_FANWE['request']['photo_type']);
	$res['info'] = authcode(serialize($info), 'ENCODE');
	if(FS('Image')->getIsServer()){//如果有可用的图片服务器
		$imgServer = FS('Image')->getMinImgServerCode();
		$res['server_code']=$imgServer['code'];
	}
	
	$args = array('res'=>$res);
	
	$result['html'] = tplFetch("services/share/pic_item",$args);
}
else
{
	$result['status'] = 0;
}

$json = getJson($result);
echo "<textarea>$json</textarea>";
?>