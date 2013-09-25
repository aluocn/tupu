<?php

if (!isset($_REQUEST['http_url']) || empty($_REQUEST['http_url']) || $_REQUEST['http_url'] == "http://")
    exit;
//http://localhost/share_tu/services/service.php?m=share&a=uploadhttpfile&http_url=http://file.weipic.com/uploadfile/20110706/photo_23vs24_2011070603184014.jpg
$result = array();
$pic = $_REQUEST['http_url'];

$o_img = copyImage($pic,md5(microtime(true)).random('6').".jpg",array(),'temp',false,0);

//$pic=GrabImage($pic, ""); 
//echo $pic;exit;
////URL是远程的完整图片地址，不能为空, $filename 是另存为的图片名字 
////默认把图片放在以此脚本相同的目录里 
//function GrabImage($url, $filename=""){ 
////$url 为空则返回 false; 
//if($url == ""){return false;} 
//$ext = strrchr($url, ".");//得到图片的扩展名 
//if($ext != ".gif" && $ext != ".jpg" && $ext != ".bmp"){echo "格式不支持！";return false;} 
//if($filename == ""){$filename = time()."$ext";}//以时间戳另起名 
////开始捕捉 
//ob_start(); 
//readfile($url); 
//$img = ob_get_contents(); 
//ob_end_clean(); 
//$size = strlen($img); 
//$fp2 = fopen($filename , "a"); 
//fwrite($fp2, $img); 
//fclose($fp2); 
//return $filename; 
//} 



if ($o_img) {
    $result['src'] = $o_img['url'];
    $info = array('path' => $o_img['path'], 'type' => 'default');
    $result['info'] = authcode(serialize($info), 'ENCODE');
    $result['status'] = 1;
} else {
    $result['status'] = 0;
}

outputJson($result);
?>