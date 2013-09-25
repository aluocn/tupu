<?php

define('MODULE_NAME','apns');
require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->cache_list = array();
$fanwe->initialize();

if(isset($_REQUEST['process']))
{
	require fimport('class/apns');
	$apns = new APNS();
	$bln = (int)$apns->processQueue();

	$log = file_get_contents(FANWE_ROOT.'test.txt');
	$log .= "\r\n============================================\r\n".print_r($_REQUEST,true)."\r\n".$bln."\r\n".fToDate(TIME_UTC);
	file_put_contents(FANWE_ROOT.'test.txt',$log);

	if($bln == 1)
	{
		$fp=fsockopen($_SERVER['HTTP_HOST'],80,&$errno,&$errstr,5);
		if($fp)
		{
			$request = "GET ".SITE_URL."apns.php?process=1 HTTP/1.0\r\n";
			$request .= "Host: ".$_SERVER['HTTP_HOST']."\r\n";
			$request .= "Connection: Close\r\n\r\n";
			fwrite($fp, $request);
			while(!feof($fp))
			{
				fgets($fp, 128);
				break;
			}
			fclose($fp);
		}
	}
}
?>