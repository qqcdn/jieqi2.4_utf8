<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
jieqi_checklogin();
jieqi_loadlang('database', JIEQI_MODULE_NAME);
if ($jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
    jieqi_printfail(LANG_NEED_ADMIN);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once JIEQI_ROOT_PATH . '/admin/header.php';
if (isset($_POST['act']) && $_POST['act'] == 'login') {
    jieqi_checkpost();
    if (!defined('JIEQI_NO_CHECKCODE') && defined('JIEQI_LOGIN_CHECKCODE') && 0 < JIEQI_LOGIN_CHECKCODE && (empty($_POST['checkcode']) || empty($_SESSION['jieqiCheckCode']) || strtolower($_POST['checkcode']) != strtolower($_SESSION['jieqiCheckCode']))) {
        jieqi_printfail($jieqiLang['system']['db_error_logincheck']);
    }
    if (trim($_POST['dbuser']) != trim(JIEQI_DB_USER) || trim($_POST['dbpass']) != trim(JIEQI_DB_PASS)) {
        jieqi_printfail($jieqiLang['system']['db_error_userpass']);
    }
    $_SESSION['jieqiDbLogin'] = 1;
    if (!defined('JIEQI_NO_CHECKCODE') && defined('JIEQI_LOGIN_CHECKCODE') && 0 < JIEQI_LOGIN_CHECKCODE) {
        if (isset($_SESSION['jieqiCheckCode'])) {
            unset($_SESSION['jieqiCheckCode']);
        }
    }
    if (empty($_REQUEST['jumpurl'])) {
        $_REQUEST['jumpurl'] = JIEQI_URL . '/admin/dboptimize.php?option=optimize';
    }
    header('Location: ' . jieqi_headstr($_REQUEST['jumpurl']));
    exit;
} else {
    $self_fname = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);
    if (!empty($_REQUEST['jumpurl'])) {
        $jieqiTpl->assign('url_dblogin', JIEQI_USER_URL . '/admin/' . $self_fname . '?do=submit&jumpurl=' . urlencode($_REQUEST['jumpurl']));
    } else {
        $jieqiTpl->assign('url_dblogin', JIEQI_USER_URL . '/admin/' . $self_fname . '?do=submit');
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
    $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/dblogin.html';
}
include_once JIEQI_ROOT_PATH . '/admin/footer.php';