<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
if (!isset($_REQUEST['id']) && isset($_REQUEST['uid'])) {
    $_REQUEST['id'] = $_REQUEST['uid'];
}
if (!isset($_REQUEST['name']) && isset($_REQUEST['username'])) {
    $_REQUEST['name'] = $_REQUEST['username'];
}
if (empty($_REQUEST['id']) && empty($_REQUEST['name'])) {
    jieqi_printfail(LANG_NO_USER);
}
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
if (!empty($_REQUEST['id'])) {
    $userobj = $users_handler->get($_REQUEST['id']);
} else {
    $_REQUEST['name'] = trim($_REQUEST['name']);
    $userobj = $users_handler->getByname($_REQUEST['name']);
}
if (is_object($userobj)) {
    $_REQUEST['uid'] = $userobj->getVar('uid', 'n');
    $_REQUEST['id'] = $_REQUEST['uid'];
    jieqi_getconfigs('system', 'honors');
    include_once JIEQI_ROOT_PATH . '/header.php';
    include_once JIEQI_ROOT_PATH . '/include/funusers.php';
    $uservals = jieqi_system_usersvars($userobj);
    $jieqiTpl->assign_by_ref('uservals', $uservals);
    foreach ($uservals as $k => $v) {
        $jieqiTpl->assign_by_ref($k, $uservals[$k]);
    }
    $jieqiTpl->assign_by_ref('id', $userobj->getVar('uid', 'n'));
    $jieqiTpl->assign_by_ref('uid', $userobj->getVar('uid', 'n'));
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/userpage.html';
    include_once JIEQI_ROOT_PATH . '/footer.php';
} else {
    jieqi_printfail(LANG_NO_USER);
}