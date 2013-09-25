<?php
//http://localhost/tupu/services/service.php?m=share&a=video&url=http://v.youku.com/v_show/id_XMzk0NjA4MDg0.html?f=17517332
global $_FANWE;

$uid=$_FANWE['uid'];
$url=$_REQUEST['url'];
$res['url'] = $url;
$files=file_get_url($url);
if(strpos($url,'www.tudou.com')!==false){
	//标题
	$title=get_title_url($files,'_',true);
	//匹配网页中的iid获取ID
	//$video="http://www.tudou.com/v/".$iid."/v.swf";
	//匹配/pic:(.*)[^\,]/i  获取图片，w.jpg是大图
	/*
	preg_match("/iid:(\w+)/i",$files,$items);
	$iid=$items[1];
	if(empty($iid)){
		$result['status']=0;
	}
	*/
	if(strpos($url,'.html')){
		preg_match("/iid:(\w+)/i",$files,$items);
		$iid=$items[1];
		if(empty($iid)){
			$result['status']=0;
		}
		$video="http://www.tudou.com/v/".$iid."/v.swf";
		preg_match("/pic:(.*)[^\,]/i",$files,$items);
		$pic=str_replace('"','',$items[1]);
		$pic=str_replace(basename($pic),'w.jpg',$pic);
		if(empty($pic)){
			$result['status']=0;
		}
	}else{
		
	preg_match("/iid\s*|\s*=\s*|\s*(\w+)/i",$files,$items);
	$iid=$items[1];

	if(empty($iid)){
		$result['status']=0;
	}
	
	$video=tudou_get($url);
	
	if(strlen($iid)==9){
		$pic='http://i.tdimg.com/'.substr($iid,0,3).'/'.substr($iid,3,3).'/'.substr($iid,6,3).'/w.jpg';
			
	}else{
		$result['status']=0;
	}
	}
	
	
}elseif(strpos($url,'v.ku6.com')!==false){
	
	$title=get_title_url($files,'_',false);
	preg_match("/A.VideoInfo(.*)/i",$files,$context);
	$context=$context[1];
	//匹配网页中的内容\"bigpicpath\" 获取图片
	//id:\s*\"([^,]+) 获取 视频ID ，"http://player.ku6.com/refer/$id/v.swf"
	preg_match("/\"bigpicpath\":\"([^,]+)\"/i",$context,$items);
	//json的UTF8 js编码
	$pattner=array('\u003a'=>':','\u002e'=>'.');
	$pic=$items[1];
	if(!empty($pic)){
		foreach($pattner as $k=>$v){
		$pic=str_replace($k,$v,$pic);
		}
		preg_match("/id:\s*\"([^,]+)\"/i",$context,$items);
		$id=$items[1];
		$video="http://player.ku6.com/refer/$id/v.swf";
		
	}else{
		$result['status']=0;
	}
	

	
}elseif(strpos($url,'v.youku.com')!==false){
	
	$title=get_title_url($files,'_',true);
	// 匹配 videoId2= ,获取 "http://v.youku.com/player/getPlayList/VideoIDS/$videoId2" ，获取JSON
	// 通过JSON 获取LOGO
	// $video="http://player.youku.com/player.php/sid/$videoId2/v.swf";
	preg_match("/videoId2=(.*)';/i",$files,$items);
	
	$videoId2=trim(str_replace('\'','',$items[1]));
	$url_info="http://v.youku.com/player/getPlayList/VideoIDS/$videoId2";
	//echo $url_info;	
	$info=file_get_url($url_info);
	$item=json_decode($info);
	//图片
	$pic=$item->data[0]->logo;
	$video="http://player.youku.com/player.php/sid/$videoId2/v.swf";
	if(empty($pic)){
		$result['status']=0;
	}
}elseif(strpos($url,'v.ifeng.com')!==false){
	//凤凰卫视的视频，原理是 http://v.ifeng.com/ent/mingxing//201205/f88a4a3c-c0fe-436f-ab0a-1103bf3b8011.shtml  获取
 	// id=f88a4a3c-c0fe-436f-ab0a-1103bf3b8011 http://v.ifeng.com/video_info_new/倒数第二/倒数第二+倒数第一/id.xml,
	//  /BigPosterUrl="(.+)" SmallPosterUrl/ 获取图片
	//  video="http://v.ifeng.com/include/exterior.swf?guid=" + id + "&AutoPlay=false",
	$title=get_title_url($files,'_',true);
	$url_name=basename($url);
	$url_key=explode(".",$url_name);
	$url_key=trim($url_key[0]);
	$url_key_1=substr($url_key,-2,1);
	$url_key_2=substr($url_key,-1,1);
	$xml=file_get_url("http://v.ifeng.com/video_info_new/$url_key_1/$url_key_1$url_key_2/$url_key.xml");
	preg_match('/BigPosterUrl="(.+)" SmallPosterUrl/',$xml,$items);
	
	$pic=$items[1];
	if(empty($pic)){
		$result['status']=0;
	}
	$video="http://v.ifeng.com/include/exterior.swf?guid=$url_key&AutoPlay=false";

	
}elseif(strpos($url,'www.56.com/u')!==false){
	//不通用
}else{
	//echo $files;
	if(!empty($files)){
		preg_match("/<video>(.*)<\/video>/i",$files,$video);
		if(!empty($video)){
			$video=trim($video[1]);
		}else{
			$result['status']=0;
		}
		preg_match("/<img>(.*)<\/img>/i",$files,$pic);
		if(!empty($pic)){
			$pic=trim($pic[1]);
		}else{
			$result['status']=0;
		}
		preg_match("/<title>(.*)<\/title>/i",$files,$title);
		if(!empty($title)){
			$title=trim($title[1]);
		}else{
			$result['status']=0;
		}
		
	}else{
		$result['status']=0;
	}
	
}

$res['video']=$video;
$res['img']=$pic;
$res['title']=$title;
if(FS('Image')->getIsServer()){//如果有可用的图片服务器
	$imgServer = FS('Image')->getMinImgServerCode();
	$res['server_code']=$imgServer['code'];
}
$args = array(
	'res'=>&$res,
);
$result['html'] = tplFetch('services/share/pic_itemVideo',$args);
$result['status'] = 1;
outputJson($result);

function get_title_url($files,$sperater="_",$is_utf8=false){
	if(!empty($files)){
	preg_match("/<title>(.*)<\/title>/i",$files,$items);
	if($is_utf8){
		$title=$items[1];
	}else{
		$title=iconv('gbk','utf-8',$items[1]);
	}
	
	$title=explode($sperater,$title);
	$title=$title[0];
	
	}
	return $title;
}

function file_get_url($url=''){
	if(!empty($url)){
		$ctx = stream_context_create(array(
			'method'=>'GET',
			'http' => array(  
				'timeout' => 1 //设置一个超时时间，单位为秒  
			)  
		)  
		); 
		$files=file_get_contents($url,false,$ctx);
		return $files;
	}else{
		return false;
	}
}

function tudou_get($url){
	preg_match('#https?://(?:www\.)?tudou\.com/(?:programs/view|listplay/(?<list_id>[a-z0-9_=\-]+))/(?<video_id>[a-z0-9_=\-]+)#i', $url, $matches );
	return  handler_tudou( $matches);
}

function handler_tudou( $matches ) {
	
  $embed = sprintf(
    'http://www.tudou.com/v/%1$s/&resourceId=0_05_05_99&bid=05/v.swf',
    $matches['video_id'] );

  return $embed;
}
?>