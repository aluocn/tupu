<?php
$_FANWE['request']['uid'] = $_FANWE['uid'];

$result = array();
/*if(!checkIpOperation("add_share",SHARE_INTERVAL_TIME))
{
	$result['status'] = 0;
	$result['error_msg'] = lang('share','interval_tips');
	outputJson($result);
}*/
$_FANWE['request']['pub_out_check']=$_REQUEST['pub_out_check'];
$share = FS('Share')->submit($_FANWE['request'],true,true);

if($share['status'])
{
	$result['status'] = 1;
	$result['url'] = FU("note/index",array("sid"=>$share['share_id']));
	$result['error_code'] = $share['error_code'];
	$result['error_msg'] = $share['error_msg'];
	$list = array();
	
	
	$list[] = FS('Share')->getShareById($share['share_id']);
	$list = FS('Share')->getShareDetailList($list,true,true,true);
	$args = array(
		'share_item'=>current($list),
	);
	
	$result['html'] = tplFetch('services/share/u_share_item',$args);
}
else
{
	$result['status'] = 0;
	$result['error_code'] = $share['error_code'];
	$result['error_msg'] = $share['error_msg'];
}

outputJson($result);
?>