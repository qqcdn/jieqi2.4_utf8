<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_NO_USER);
}
jieqi_loadlang('users', JIEQI_MODULE_NAME);
$_REQUEST['id'] = intval($_REQUEST['id']);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$user = $users_handler->get($_REQUEST['id']);
if (!is_object($user)) {
    jieqi_printfail(LANG_NO_USER);
}
if ($user->getVar('groupid') == JIEQI_GROUP_ADMIN && $jieqiUsersGroup != JIEQI_GROUP_ADMIN) {
    jieqi_printfail($jieqiLang['system']['cant_manage_admin']);
}
if (jieqi_checkpower($jieqiPower['system']['deluser'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) {
    $adminlevel = 4;
} else {
    if (jieqi_checkpower($jieqiPower['system']['adminvip'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) {
        $adminlevel = 3;
    } else {
        if (jieqi_checkpower($jieqiPower['system']['changegroup'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) {
            $adminlevel = 2;
        } else {
            $adminlevel = 1;
        }
    }
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'edit';
}
switch ($_POST['act']) {
    case 'update':
        jieqi_checkpost();
        $_POST['reason'] = trim($_POST['reason']);
        $_POST['pass'] = trim($_POST['pass']);
        $_POST['repass'] = trim($_POST['repass']);
        if ($_POST['pass'] != $_POST['repass']) {
            $errtext .= $jieqiLang['system']['password_not_equal'] . '<br />';
        }
        if (empty($errtext)) {
            $log_fromdata = serialize($user);
            if (4 <= $adminlevel && isset($_POST['deluser']) && $_POST['deluser'] == 1) {
                $_REQUEST['uid'] = $user->getVar('uid');
                $_REQUEST['jumpurl'] = JIEQI_URL . '/admin/users.php';
                jieqi_useraction('delete', $_REQUEST);
            } else {
                $_REQUEST['uid'] = $user->getVar('uid');
                $_REQUEST['jumpurl'] = JIEQI_URL . '/admin/users.php';
                jieqi_useraction('edit', $_REQUEST);
            }
            exit;
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'edit':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('username', $user->getVar('uname', 's'));
        $jieqiTpl->assign('nickname', $user->getVar('name', 's'));
        $uservals = $user->getVars('e');
        $uservals['setting'] = jieqi_unserialize($user->getVar('setting', 'n'));
        $jieqiTpl->assign_by_ref('uservals', $uservals);
        $jieqiTpl->assign_by_ref('grouprows', $jieqiGroups);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/usermanage.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}