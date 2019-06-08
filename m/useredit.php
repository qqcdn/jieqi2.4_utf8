<?php


define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_loadlang('users', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
if (!$jieqiUsers) {
    jieqi_printfail(LANG_NO_USER);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
if (!isset($_POST['act'])) {
    $_POST['act'] = 'edit';
}
switch ($_POST['act']) {
    case 'update':
        jieqi_checkpost();
        $isverified = $jieqiUsers->getUserset('verify', 'email');
        if (0 < $isverified) {
            $_REQUEST['email'] = $jieqiUsers->getVar('email', 'n');
        }
        $_REQUEST['uid'] = $_SESSION['jieqiUserId'];
        if (isset($_REQUEST['nextstep']) && $_REQUEST['nextstep'] == 'applywriter') {
            $_REQUEST['jumpurl'] = JIEQI_URL . '/personedit.php?nextstep=applywriter';
        } else {
            $_REQUEST['jumpurl'] = JIEQI_URL . '/userdetail.php';
        }
        jieqi_useraction('edit', $_REQUEST);
        break;
    case 'edit':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        include_once JIEQI_ROOT_PATH . '/include/funusers.php';
        $uservals = jieqi_system_usersvars($jieqiUsers, 'e');
        $uservals['username'] = $uservals['uname'];
        $uservals['nickname'] = $uservals['name'];
        $jieqiTpl->assign_by_ref('uservals', $uservals);
        foreach ($uservals as $k => $v) {
            $jieqiTpl->assign_by_ref($k, $uservals[$k]);
        }
        foreach ($jieqiOption['system'] as $k => $v) {
            $jieqiTpl->assign($k, $jieqiOption['system'][$k]);
        }
        $nextstep = isset($_REQUEST['nextstep']) ? $_REQUEST['nextstep'] : '';
        $jieqiTpl->assign_by_ref('nextstep', $nextstep);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/useredit.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}