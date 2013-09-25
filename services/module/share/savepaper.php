<?php

$_FANWE['request']['uid'] = $_FANWE['uid'];

$result = array();
/*if(!checkIpOperation("add_share",SHARE_INTERVAL_TIME))
{
	$result['status'] = 0;
	$result['error_msg'] = lang('share','interval_tips');
	outputJson($result);
}*/
$txt = stripcslashes($_REQUEST['text']);
preg_match('/src=\"(.*?)\"/',$txt,$matches);
$img_url=$matches[1];
if($img_url){
	$_FANWE['request']['img_url'] = ".".$img_url;
}else{
	$_FANWE['request']['no_img'] = 1;//分享文章没有图片的时候
}

$_FANWE['request']['content'] = trim($_REQUEST['title']);//文章的存放share表里content和title字段一样
$_FANWE['request']['pub_out_check']=$_REQUEST['pub_out_check'];
$_FANWE['request']['type'] = 'paper';
$_FANWE['request']['is_video'] = 3;
if(FS('Image')->getIsServer()){//如果有可用的图片服务器
	$imgServer = FS('Image')->getMinImgServerCode();
	$_FANWE['request']['server_code']=$imgServer['code'];
	$_FANWE['request']['pics'] = $_FANWE['site_url'].$img_url;
}else{
	$img_url=str_replace($_FANWE['site_root'],"",$img_url);
	$_FANWE['request']['pics'] = FANWE_ROOT."./".$img_url;
}

$share = FS('Share')->submit($_FANWE['request'],true,true);

if($share['status'])
{
	$result['status'] = 1;
	$result['url'] = FU("note/index",array("sid"=>$share['share_id']));
	$result['error_code'] = $share['error_code'];
	$result['error_msg'] = $share['error_msg'];
}
else
{
	$result['status'] = 0;
	$result['error_code'] = $share['error_code'];
	$result['error_msg'] = $share['error_msg'];
}

outputJson($result);
?>