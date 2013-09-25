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
	$serverCode=$_FANWE['request']['serverCodeArray'];
	$isVideo=$_FANWE['request']['is_video'];
	$info=$_FANWE['request']['shareInfoArray'];
	$data = array();
	foreach($aid as $k=>$v){
		$data['album_id'] = $aid[$k];
		$data['content'] = $content[$k];
		$data['imgs'] = $imgs[$k];
		$data['videos'] = $videos[$k];
		$data['title'] = $title[$k];
		$data['pageUrl'] = $pageUrl[$k];
		$data['server_code'] = $serverCode[$k];
		$data['pub_out_check'] = $pub_out_check;
		$data['is_video']=$isVideo;
		$data['info']=$info[$k];
		
		$share_submit = FS("Collect")->submitImg($data);
		if(!$share_submit){
			$result['status']=0;
			break;
		}
	}
	
	$success_url = FU("collection/success");
	$result['url'] = FU("u/me");
	$result['success_url'] = $success_url;
	outputJson($result);
?>