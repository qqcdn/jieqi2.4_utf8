<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_loadlang('users', JIEQI_MODULE_NAME);
if (empty($_REQUEST['id']) || empty($_REQUEST['checkcode'])) {
    jieqi_printfail($jieqiLang['system']['no_checkcode_id']);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$user = $users_handler->get($_REQUEST['id']);
if (!is_object($user)) {
    jieqi_printfail(LANG_NO_USER);
}
if (md5($user->getVar('pass')) != $_REQUEST['checkcode']) {
    jieqi_printfail($jieqiLang['system']['error_checkcode']);
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'setpass';
}
switch ($_POST['act']) {
    case 'newpass':
        $_REQUEST['uid'] = $_REQUEST['id'];
        $_REQUEST['oldpass'] = $user->getVar('pass', 'n');
        $_REQUEST['jumpurl'] = JIEQI_URL . '/login.php';
        $_REQUEST['lang_failure'] = $jieqiLang['system']['set_password_failure'];
        $_REQUEST['lang_success'] = $jieqiLang['system']['set_password_success'];
        jieqi_useraction('edit', $_REQUEST);
        break;
    case 'setpass':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('url_setpass', JIEQI_USER_URL . '/setpass.php?do=submit');
        $jieqiTpl->assign('id', $_REQUEST['id']);
        $jieqiTpl->assign('checkcode', $_REQUEST['checkcode']);
        $jieqiTpl->assign('username', $user->getVar('uname'));
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/setpass.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}