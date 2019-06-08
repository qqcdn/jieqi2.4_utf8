<?php

define('JIEQI_MODULE_NAME', 'system');
if ($_REQUEST['act'] == 'login') {
    define('JIEQI_NEED_SESSION', 1);
}
require_once 'global.php';
jieqi_loadlang('users', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$errstr = '';
if (!isset($_REQUEST['act']) && isset($_REQUEST['action'])) {
    $_REQUEST['act'] = $_REQUEST['action'];
}
if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'login' && !empty($_REQUEST['username']) && !empty($_REQUEST['password'])) {
    $_REQUEST['jumpurl'] = JIEQI_URL . '/loginframe.php';
    $_REQUEST['jumphide'] = true;
    $_REQUEST['return'] = true;
    $islogin = jieqi_useraction('login', $_REQUEST);
    if (!$islogin) {
        $errstr = $_REQUEST['error'];
    }
} else {
    if ($_REQUEST['act'] == 'logout') {
        $_REQUEST['jumpurl'] = JIEQI_URL . '/loginframe.php';
        $_REQUEST['jumphide'] = true;
        $_REQUEST['return'] = true;
        jieqi_useraction('logout', $_REQUEST);
        $islogin = false;
    } else {
        if ($jieqiUsersGroup == JIEQI_GROUP_GUEST) {
            $islogin = false;
        } else {
            $islogin = true;
        }
    }
}
include_once JIEQI_ROOT_PATH . '/lib/template/template.php';
$jieqiTpl = JieqiTpl::getInstance();
if ($islogin) {
    $jieqiTpl->assign('jieqi_userid', $_SESSION['jieqiUserId']);
    $jieqiTpl->assign('jieqi_username', jieqi_htmlstr($_SESSION['jieqiUserName']));
    $jieqiTpl->assign('jieqi_usergroup', $jieqiGroups[$_SESSION['jieqiUserGroup']]);
    $jieqiTpl->assign('jieqi_groupname', $jieqiGroups[$_SESSION['jieqiUserGroup']]);
    $jieqiTpl->assign('jieqi_honor', $_SESSION['jieqiUserHonor']);
    $jieqiTpl->assign('jieqi_score', $_SESSION['jieqiUserScore']);
    $jieqiTpl->assign('jieqi_experience', $_SESSION['jieqiUserExperience']);
    $jieqiTpl->assign('jieqi_vip', $_SESSION['jieqiUserVip']);
    $jieqiTpl->assign('jieqi_egold', $_SESSION['jieqiUserEgold']);
    if (isset($_SESSION['jieqiNewMessage']) && 0 < $_SESSION['jieqiNewMessage']) {
        $jieqiTpl->assign('jieqi_newmessage', $_SESSION['jieqiNewMessage']);
    } else {
        $jieqiTpl->assign('jieqi_newmessage', 0);
    }
    $jieqiTpl->setCaching(0);
    $jieqiTpl->display(JIEQI_ROOT_PATH . '/templates/statusframe.html');
} else {
    if (empty($_REQUEST['username'])) {
        $_REQUEST['username'] = '';
    }
    $jieqiTpl->assign('username', jieqi_htmlstr($_REQUEST['username']));
    $jieqiTpl->assign('errstr', $errstr);
    $show_checkcode = defined('JIEQI_LOGIN_CHECKCODE') && !defined('JIEQI_NO_CHECKCODE') ? JIEQI_LOGIN_CHECKCODE : 0;
    $jieqiTpl->assign('show_checkcode', $show_checkcode);
    $jieqiTpl->assign('url_checkcode', JIEQI_USER_URL . '/checkcode.php');
    $jieqiTpl->setCaching(0);
    $jieqiTpl->display(JIEQI_ROOT_PATH . '/templates/loginframe.html');
}