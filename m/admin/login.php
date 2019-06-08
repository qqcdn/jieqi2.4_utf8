<?php

define('JIEQI_MODULE_NAME', 'system');
define('JIEQI_ADMIN_LOGIN', 1);
define('JIEQI_ADMIN_FRAME', 1);
if (!isset($_REQUEST['act']) && isset($_REQUEST['action'])) {
    $_REQUEST['act'] = $_REQUEST['action'];
}
if (isset($_POST['act']) && $_POST['act'] == 'login') {
    define('JIEQI_NEED_SESSION', 1);
}
require_once '../global.php';
if (!empty($_SESSION['jieqiUserId']) && !empty($_SESSION['jieqiAdminLogin'])) {
    if (empty($_REQUEST['jumpurl']) || !preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_REQUEST['jumpurl'])) {
        $_REQUEST['jumpurl'] = JIEQI_URL . '/admin/index.php';
    }
    header('Location: ' . jieqi_headstr($_REQUEST['jumpurl']));
    exit;
}
if (isset($_POST['act']) && $_POST['act'] == 'login' && empty($_SESSION['jieqiUserId'])) {
    @session_regenerate_id();
}
jieqi_loadlang('users', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (!isset($_POST['act'])) {
    $_POST['act'] = '';
}
if ($_POST['act'] == 'login' && !empty($_REQUEST['password']) && (!empty($_REQUEST['username']) || !empty($_SESSION['jieqiUserUname']))) {
    if (empty($_REQUEST['username']) && !empty($_SESSION['jieqiUserUname'])) {
        $_REQUEST['username'] = $_SESSION['jieqiUserUname'];
    }
    if (empty($_REQUEST['jumpurl']) || !preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_REQUEST['jumpurl'])) {
        $_REQUEST['jumpurl'] = JIEQI_URL . '/admin/index.php';
    }
    jieqi_useraction('login', $_REQUEST);
    exit;
}
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$self_fname = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);
if (!empty($_REQUEST['jumpurl']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_REQUEST['jumpurl'])) {
    $jieqiTpl->assign('url_login', JIEQI_USER_URL . '/admin/' . $self_fname . '?do=submit&jumpurl=' . urlencode($_REQUEST['jumpurl']));
} else {
    $jieqiTpl->assign('url_login', JIEQI_USER_URL . '/admin/' . $self_fname . '?do=submit');
}
if (empty($_SESSION['jieqiUserId'])) {
    $jieqiTpl->assign('jieqi_userid', 0);
    $jieqiTpl->assign('jieqi_username', '');
} else {
    $jieqiTpl->assign('jieqi_userid', $_SESSION['jieqiUserId']);
    $jieqiTpl->assign('jieqi_username', jieqi_htmlstr($_SESSION['jieqiUserUname']));
}
$show_checkcode = defined('JIEQI_LOGIN_CHECKCODE') && !defined('JIEQI_NO_CHECKCODE') ? JIEQI_LOGIN_CHECKCODE : 0;
$jieqiTpl->assign('show_checkcode', $show_checkcode);
$jieqiTpl->assign('url_checkcode', JIEQI_USER_URL . '/checkcode.php');
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/login.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';