<?php
/**
 * 获取登陆后的转向
 */
function getUserRefer()
{
	global $_FANWE;
	$refer = $_FANWE['request']['refer'];
	$redir_url = getCookie('redir_url');
	
	if($redir_url)
	{
		$refer = $redir_url;
	}
	
	if(empty($refer))
		$refer = FU('u/index');
	return $refer;
}

function getTipUserFollow($uid)
{
	global $_FANWE;
	
	$is_follow = false;
	if($_FANWE['uid'] > 0 && $_FANWE['uid'] != $uid)
		$is_follow = FS('User')->getIsFollowUId($uid);
		
	$args = array(
		'uid'=>$uid,
		'is_follow'=>$is_follow,
	);
	return tplFetch('services/user/tip_follow',$args);
}
?>