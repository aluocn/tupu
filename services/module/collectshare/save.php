<?php
//http://localhost/tupu_svn/services/service.php?m=collectshare&a=save
	global $_FANWE;
	$_FANWE['request']['uid'] = $_FANWE['uid'];
	
	$result = array();
	if($_FANWE['uid'] == 0)
	{
		$result['status'] = 0;
		$result['message'] = "未登录";
	}
	
	$result['status'] = 1;
	
	$pageUrl = $_FANWE['request']['pageUrl'];
	$aid_one = $_FANWE['request']['album_id'];
	$content_one = $_FANWE['request']['content'];
	$imgs = $_FANWE['request']['imgArray'];
	$videos = $_FANWE['request']['videoArray'];
	$title_one = $_FANWE['request']['title'];
	$pub_out_check = $_FANWE['request']['pub_out_check'];
	$goods_type=($_FANWE['request']['is_video']==2)?1:0;
	$is_video = $_FANWE['request']['is_video'];
	if($_FANWE['request']['lym_test']==1){
	error_reporting(E_ALL ^ E_NOTICE);
	//测试代码
	$pageUrl="http://v.youku.com/v_show/id_XMzk2NzQ4NjIw.html?f=17550464";
	$aid_one=2;
	$content_one='祖国的花朵谁来保护 - 【拍客】震惊！保安猥亵三名女童，但仍逍遥法外！ - 视频 - 优酷视频 - 在线观看';
	//http://joke.cnball.net/attachments/images/c090320/123k10mh14z-2922b.jpg
	//$imgs=array('http://img10.360buyimg.com/da/20120422/670_240_euUDXS.jpg');
	//$imgs=array('http://img.hb.aicdn.com/0b85f3e86823a9c76d9413b59859943adf55ae611197c5-EooETB_fw554');
	//$imgs=array('http://www.910club.cn/kjsc/uploadfiles20081888/200904/2009042412385876.gif');//动态图片
	$imgs=array('http://g3.ykimg.com/1100641F464FB3335E2E7C0247A48F92F69854-4447-5599-A668-2E2A1673D266');
	$videos=array('http://player.youku.com/player.php/sid/XMzk2NzQ4NjIw/v.swf');
	$title_one='祖国的花朵谁来保护 - 【拍客】震惊！保安猥亵三名女童，但仍逍遥法外！ - 视频 - 优酷视频 - 在线观看';
	$pub_out_check=1;
   	$goods_type=0;
	}elseif($_FANWE['request']['lym_test']==2){
	error_reporting(E_ALL ^ E_NOTICE);
	$pageUrl="http://sale.360buy.com/p11639.html";
	$aid_one=5;
	$content_one='京东诺曼底，史上最强店庆月 - 京东商城';
	$imgs=array('http://img30.360buyimg.com/cms/g5/M02/01/10/rBEIDE_IGuwIAAAAAAQnmT-l_U8AAATIAGnS_8ABCex973.jpg');
	$videos=array('');
	$title_one='东诺曼底，史上最强店庆月 - 京东商城';
	$pub_out_check=0;
   	$goods_type=0;
	}
	$aid = array();
	$title = array();
	$content = array();
	$img_key=false;
	foreach($imgs as $k=> $v)
	{
		if(!empty($v)){
			$aid[$k] = $aid_one;
			$title[$k] = $title_one;
			$content[$k] = $content_one;
			$img_key=true;
		}
		
	}
	if(!$aid_one||empty($aid)){
		$result['status'] = 0;
		$result['message'] = '请重新选择分类';
		outputJson($result);
	}
	if(!$img_key){
		$result['status'] = 0;
		$result['message'] = '请上传有效图片';
		outputJson($result);
	}
	
	$data = array();
	$data['album_id'] = $aid;
	$data['content'] = $content;
	$data['imgs'] = $imgs;
	$data['videos'] = $videos;
	$data['title'] = $title;
	$data['pageUrl'] = $pageUrl;
	$data['pub_out_check'] = $pub_out_check;
	$data['is_video']=$is_video;
	if(FS("Image")->getIsServer()){
		$server=FS('Image')->getMinImgServerCode();
		$data['server_code']=$server['code'];
	}
	
	if($goods_type){
		//设置类型为商品类型
		$data['is_video']=2;
	}
	$share_submit = FS("Collect")->submitList($data);
	if(!$share_submit){
		$result['status'] = 0;
		$result['message'] = '分享失败';
		outputJson($result);
	}
	$success_url = FU("collection/success");
	$result['url'] =FU("album/show",array("id"=>intval($_FANWE['request']['album_id'])));
	$result['success_url'] = $success_url;
	
	
	outputJson($result);
?>