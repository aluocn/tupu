<?php
if(!defined('FANWE_ROOT'))
	define('FANWE_ROOT', str_replace('core/fanwe.php', '', str_replace('\\', '/', __FILE__)));
$is_install=0;

//添加点高广告

if(file_exists(FANWE_ROOT."./public/adv_api/DianGao.php")){
	$diangao=@require(FANWE_ROOT."./public/adv_api/DianGao.php");
	define('DIANGAO_JS',$diangao['diangao_js']);
}else{
	define('DIANGAO_JS',FALSE);
}

//添加google广告
if(file_exists(FANWE_ROOT."./public/adv_api/Google.php")){
	$google=@require(FANWE_ROOT."./public/adv_api/Google.php");
	if($google['is_open']){
	define('GOOGLE_JS',urldecode($google['google_js']));
	}else{
		define('GOOGLE_JS',FALSE);
	}
}else{
	define('GOOGLE_JS',FALSE);
}


if(!file_exists(FANWE_ROOT."./domain")){
    		die( "domain is not exists");
    	}else{
    		if(file_exists(FANWE_ROOT."./public/data/~core.php")){
    			@require FANWE_ROOT."./public/data/~core.php";
    		}else{
    			$key_file=FANWE_ROOT."./domain";
				$str=@file_get_contents($key_file);
				require_once FANWE_ROOT."./common/tupu_key.php";
				//require_once FANWE_ROOT."./common/es_key.php";
				$tupu_key=new tupu_key(64);
				$str=$tupu_key->decrypt($str,"TUPU");
				@file_put_contents(FANWE_ROOT."./public/data/~core.php", $str);
				@require FANWE_ROOT."./public/data/~core.php";
				
    		}
    	}
?>