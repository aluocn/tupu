<?php
define('MODULE_NAME', 'About');

$actions = array('index', 'goodies', 'help', 'etiquette');

if (isset($_REQUEST['action'])) {
    $action = strtolower($_REQUEST['action']);
    if (!in_array($action, $actions))
        $action = 'index';
}

define('ACTION_NAME', $action);

require dirname(__FILE__) . '/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();


$act = $_REQUEST['action'];
$sql = "select * from ".FDB::table('page_seo')." where action_name ="."'$act'";
$list  = FDB::fetchFirst($sql);
$_FANWE['nav_title'] = $list['seo_title'];
$_FANWE['seo_keywords'] = $list['seo_keywords'];
$_FANWE['seo_description'] = $list['seo_desc'];

require fimport('module/about');

switch (ACTION_NAME) {
    case 'index':
        AboutModule::index();
        break;
    case 'goodies':
        AboutModule::goodies();
        break;
    case 'help':
        AboutModule::help();
        break;
    case 'etiquette':
        AboutModule::etiquette();
        break;
}
?>