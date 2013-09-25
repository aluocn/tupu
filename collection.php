<?php 
define('MODULE_NAME','Collection');

$actions = array('index','photo','success');
$action = 'index';
if(isset($_REQUEST['action']))
{
	$action = strtolower($_REQUEST['action']);
	if(!in_array($action,$actions))
		$action = 'index';
}

define('ACTION_NAME',$action);


require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

require fimport('module/collection');

switch(ACTION_NAME)
{
	case 'index':
		CollectionModule::index();
	break;
	case 'photo':
		CollectionModule::photo();
	break;
	case 'success':
		CollectionModule::success();
	break;
	default:
		CollectionModule::index();
		break;
}
?>