<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
if (empty($_REQUEST['id']) && empty($_REQUEST['name'])) {
    $_REQUEST['id'] = intval($_SESSION['jieqiUserId']);
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
    $jieqiTpl->assign('authorid', $_REQUEST['id']);
    $jieqiTpl->assign('author', $userobj->getVar('name'));
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/userpage.html';
    include_once JIEQI_ROOT_PATH . '/footer.php';
} else {
    jieqi_printfail(LANG_NO_USER);
}