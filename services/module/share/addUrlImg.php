<?php
	global $_FANWE;
	$res['url']=$_REQUEST['url'];
	$res['pic_url']=$_REQUEST['pic_url'];
	if(FS('Image')->getIsServer()){//如果有可用的图片服务器
		$imgServer = FS('Image')->getMinImgServerCode();
		$res['server_code']=$imgServer['code'];
	}
	$args = array('res'=>$res);
	$result['html'] = tplFetch("services/share/pic_itemUrl",$args);
	$result['status']=1;
	outPutJson($result);
?>