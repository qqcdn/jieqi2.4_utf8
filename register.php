<?php

define('JIEQI_MODULE_NAME', 'system');
if (isset($_POST['act']) && $_POST['act'] == 'newuser') {
    define('JIEQI_NEED_SESSION', 1);
}
require_once 'global.php';
jieqi_loadlang('users', JIEQI_MODULE_NAME);
if (!defined('JIEQI_ALLOW_REGISTER') || JIEQI_ALLOW_REGISTER != 1) {
    jieqi_printfail($jieqiLang['system']['user_stop_register']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
if (!isset($_POST['act'])) {
    $_POST['act'] = 'input';
}
switch ($_POST['act']) {
    case 'newuser':
        jieqi_useraction('register', $_REQUEST);
        break;
    case 'input':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('form_action', JIEQI_USER_URL . '/register.php');
        $jieqiTpl->assign('check_url', JIEQI_USER_URL . '/regcheck.php');
        foreach ($jieqiOption['system'] as $k => $v) {
            $jieqiTpl->assign($k, $jieqiOption['system'][$k]);
        }
        $register_checkcode = defined('JIEQI_REGISTER_CHECKCODE') && !defined('JIEQI_NO_CHECKCODE') ? JIEQI_REGISTER_CHECKCODE : 0;
        $jieqiTpl->assign('register_checkcode', $register_checkcode);
        $show_checkcode = 0 < ($register_checkcode & 1) ? 1 : 0;
        $jieqiTpl->assign('show_checkcode', $show_checkcode);
        $show_emailrand = 0 < ($register_checkcode & 2) ? 1 : 0;
        $jieqiTpl->assign('show_emailrand', $show_emailrand);
        $show_mobilerand = 0 < ($register_checkcode & 4) ? 1 : 0;
        $jieqiTpl->assign('show_mobilerand', $show_mobilerand);
        $jieqiTpl->assign('url_checkcode', JIEQI_USER_URL . '/checkcode.php');
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/register.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}