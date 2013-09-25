<?php
	global $_FANWE;
	$result = array();
	
	if($_FANWE['uid'] == 0)
	{
		$result['status'] = 0;
		$result['message'] = "未登录";
	}
	$result['status'] = 1;
	$pageUrl = $_FANWE['request']['pageUrl'];
	$aid = $_FANWE['request']['aidArray'];
	$content = $_FANWE['request']['contentArray'];
	$imgs = $_FANWE['request']['imgArray'];
	$title = $_FANWE['request']['titleArray'];
	$pub_out_check = $_FANWE['request']['pub_out_check'];
	$videos = $_FANWE['request']['videoArray'];
	$is_video=$_FANWE['request']['is_video'];
	$share_info = $_FANWE['request']['shareInfoArray'];
	$data = array();
	$data['album_id'] = $aid;
	$data['content'] = $content;
	$data['imgs'] = $imgs;
	$data['videos'] = $videos;
	$data['title'] = $title;
	$data['pageUrl'] = $pageUrl;
	$data['pub_out_check'] = $pub_out_check;
	$data['is_video']=$is_video;
	$data['share_info']=$share_info;
	
	if(FS("Image")->getIsServer()){
		$server=FS('Image')->getMinImgServerCode();
		$data['server_code']=$server['code'];
	}
	
	$share_submit = FS("Collect")->submitList($data);
	$success_url = FU("collection/success");
	$result['url'] = FU("u/me");
	$result['success_url'] = $success_url;
	outputJson($result);
?>