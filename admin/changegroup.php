<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['changegroup'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('groups', JIEQI_MODULE_NAME);
if (empty($_REQUEST['uid'])) {
    jieqi_printfail(LANG_NO_USER);
}
if ($_REQUEST['gid'] == JIEQI_GROUP_ADMIN && $jieqiUsersGroup != JIEQI_GROUP_ADMIN) {
    jieqi_printfail($jieqiLang['system']['set_admin_deny']);
}
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$user = $users_handler->get($_REQUEST['uid']);
if (!is_object($user)) {
    jieqi_printfail(LANG_NO_USER);
}
if ($user->getVar('groupid') == JIEQI_GROUP_ADMIN && $jieqiUsersGroup != JIEQI_GROUP_ADMIN) {
    jieqi_printfail($jieqiLang['system']['manage_admin_deny']);
}
$chglog = array();
$chginfo = '';
$chglog['groupid']['from'] = $user->getVar('groupid');
$chglog['groupid']['to'] = $_REQUEST['gid'];
$user->setVar('groupid', $_REQUEST['gid']);
$chginfo .= sprintf($jieqiLang['system']['change_group_log'], $jieqiGroups[$chglog['groupid']['from']], $jieqiGroups[$chglog['groupid']['to']]);
if (!$users_handler->insert($user)) {
    jieqi_printfail($jieqiLang['system']['change_group_failure']);
} else {
    include_once JIEQI_ROOT_PATH . '/class/userlog.php';
    $userlog_handler = JieqiUserlogHandler::getInstance('JieqiUserlogHandler');
    $newlog = $userlog_handler->create();
    $newlog->setVar('siteid', JIEQI_SITE_ID);
    $newlog->setVar('logtime', JIEQI_NOW_TIME);
    $newlog->setVar('fromid', $_SESSION['jieqiUserId']);
    $newlog->setVar('fromname', $_SESSION['jieqiUserName']);
    $newlog->setVar('toid', $user->getVar('uid', 'n'));
    $newlog->setVar('toname', $user->getVar('uname', 'n'));
    $newlog->setVar('reason', $jieqiLang['system']['change_group_reason']);
    $newlog->setVar('chginfo', $chginfo);
    $newlog->setVar('chglog', serialize($chglog));
    $newlog->setVar('isdel', '0');
    $newlog->setVar('userlog', '');
    $userlog_handler->insert($newlog);
    jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['change_group_success']);
}