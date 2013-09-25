<?php
	global $_FANWE;
	$src = $_REQUEST['src'];
	
	$img_src=FANWE_ROOT.$src;
	print_r($img_src);exit;
    if(file_exists($img_src)){
    	unlink($img_src);
    	$result['status'] =1;	
    }else{
    	$result['status'] = 0;
    }
    
    outputJson($result);
    
?>