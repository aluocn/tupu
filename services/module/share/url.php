<?php
//http://localhost/tupu/services/service.php?m=share&a=video&url=http://v.youku.com/v_show/id_XMzk0NjA4MDg0.html?f=17517332
global $_FANWE;

$uid=$_FANWE['uid'];
$url=$_REQUEST['url'];
$result['status']=1;
$files=file_get_url($url);
if($files==''){
	$files=curl_get_url($url);
}

$title=get_title_url($files);
$pic=get_imgs($files);
if($pic==false){
	$result['status']=0;
}
unset($files);
$result['video']=$video;
$result['pic']=$pic;
$result['title']=$title;
outputJson($result);

function get_imgs($files){
	preg_match_all('/<img[^>]*src="(http.*)"[^>]*>/isU',$files,$info);
	if(!$info){
		preg_match_all('/<img[^>]*src="(.*)"[^>]*>/isU',$files,$info);
	}
	if(count($info[1])>0){
		return check_img_size($info[1]);
	}else{
	return false;
	}
}
 function is_utf8($word)
		{
		if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word) == true)
		{
			return true;
		}
		else
		{
			return false;
		}
		} // function is_utf8
	
function check_img_size($url_array){
	$data='';
	$num=1;
	foreach($url_array as $k=>$v){
		$data.='<li><a href="javascript:void(0);" onclick="change_img(\''.$v.'\')"><img src="'.$v.'" /></a><span>图片'.$num.'</span> </li>';
		if($num > 16){
			break;
		}else{
			$num++;
		}
	}
	return $data;
}

function get_title_url($files,$sperater="_",$is_utf8=false){
	if(!empty($files)){
	preg_match("/<title>(.*)<\/title>/i",$files,$items);
	if(is_utf8($items[1])){
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
//使用curl
 function curl_get_url($url=''){
	$ch=curl_init($url);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.56 Safari/536.5');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		$html_content=curl_exec($ch);
		curl_close($ch);
		return $html_content;
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