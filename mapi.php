<?php

define('MODULE_NAME', 'mapi');

require dirname(__FILE__) . '/core/service/fanwe.service.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

$i_type = 0;//上传数据格式类型; 0:base64;1;REQUEST;2:json
//r_type: 返回数据格式类型; 0:base64;1;json_encode;2:array

if (isset($_REQUEST['i_type']))
{
	$i_type = intval($_REQUEST['i_type']);
}

if ($i_type == 1){
	$requestData = $_REQUEST;
}else{
	if (isset($_REQUEST['requestData'])){
		if ($i_type == 2){
			$requestData = json_decode(trim($_REQUEST['requestData']), 1);
		}else{
			$requestData = base64_decode(trim($_REQUEST['requestData']));
			$requestData = json_decode($requestData, 1);
		}
	}else{
		$requestData = $_REQUEST;
	}
}

$action = $requestData['act'];
$actions = array('init');
define('ACTION_NAME', $action);

$email = $requestData['email'];

$pwd = md5($requestData['pwd']);
if ($email <> '' && $requestData['pwd'] <> '' && $action <> 'login' && $action <> 'register'){
	$user = FDB::fetchFirst("SELECT u.*, uc.*, us.*, up.* FROM ".FDB::table('user')." u
				LEFT JOIN ".FDB::table('user_count')." uc USING(uid)
				LEFT JOIN ".FDB::table('user_status')." us USING(uid)
				LEFT JOIN ".FDB::table('user_profile')." up USING(uid)
				WHERE (u.user_name='$email' OR u.email = '$email') AND u.password = '$pwd'");
}


if($user)
{
	unset($user['user_name_match'],$user['password'],$user['active_hash'],$user['reset_hash']);
	$_FANWE['user'] = $user;
	$_FANWE['uid'] = $user['uid'];
	$_FANWE['user_name'] = $user['user_name'];
	$_FANWE['user']['user_avatar'] = $_FANWE['user_avatar'] = FS("Image")->getImageUrl('.'.avatar($user['uid'],'m',1),2);
	
	$_FANWE['gid'] = $user['gid'];
}else{
	$_FANWE['user'] = '';
	$_FANWE['uid'] = 0;
	$_FANWE['user_name'] = '';
	$_FANWE['user']['user_avatar'] = '';
	$_FANWE['gid'] = 0;
}

require_once dirname(__FILE__). '/core/mapi/base.mapi.php';

require_once dirname(__FILE__). '/core/mapi/'.$action.'.mapi.php';

$_FANWE['requestData'] = $requestData;
$_FANWE['MConfig'] = m_getMConfig();//初始化配送数据
define('PAGE_SIZE', 20);
$mapi_class = $action."Mapi";
$module = new $mapi_class;
$module->run();

?>