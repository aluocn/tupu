<?php

define('MODULE_NAME', 'Book');

$actions = array('share', 'user', 'album');

if (isset($_REQUEST['action'])) {
    $action = strtolower($_REQUEST['action']);
    if (!in_array($action, $actions))
        $action = 'share';
}

define('ACTION_NAME', $action);

require dirname(__FILE__) . '/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

require fimport('module/book');

switch (ACTION_NAME) {
    case 'share':
        BookModule::share();
        break;
    case 'user':
        BookModule::user();
        break;
    case 'album':
        BookModule::album();
        break;
}
?>