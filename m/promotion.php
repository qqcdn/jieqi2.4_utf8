<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
if (!$jieqiUsers) {
    jieqi_printfail(LANG_NO_USER);
}
jieqi_getconfigs('system', 'configs');
$channelerate = isset($jieqiConfigs['system']['channelerate']) ? $jieqiConfigs['system']['channelerate'] : 0;
if ($channelerate <= 0) {
    jieqi_printfail(LANG_DENY_FUNCTION);
}
jieqi_getconfigs('system', 'honors');
include_once JIEQI_ROOT_PATH . '/header.php';
include_once JIEQI_ROOT_PATH . '/include/funusers.php';
$uservals = jieqi_system_usersvars($jieqiUsers);
$usermoney = $jieqiUsers->getEmoney();
$uservals['egold'] = $usermoney['egold'];
$uservals['esilver'] = $usermoney['esilver'];
$uservals['emoney'] = $usermoney['emoney'];
$jieqiTpl->assign_by_ref('uservals', $uservals);
foreach ($uservals as $k => $v) {
    $jieqiTpl->assign_by_ref($k, $uservals[$k]);
}
$jieqiTpl->assign('channelerate', $channelerate);
jieqi_getconfigs('system', 'rule');
if (function_exists('jieqi_rule_system_channelerate')) {
    $mychrate = jieqi_rule_system_channelerate($channelerate, $jieqiUsers);
} else {
    $mychrate = $channelerate;
}
$jieqiTpl->assign('mychrate', $mychrate);
$channeldlimit = isset($jieqiConfigs['system']['channeldlimit']) && is_numeric($jieqiConfigs['system']['channeldlimit']) ? intval($jieqiConfigs['system']['channeldlimit']) : 0;
$jieqiTpl->assign('channeldlimit', $channeldlimit);
$exchangerate = isset($jieqiConfigs['system']['exchangerate']) && is_numeric($jieqiConfigs['system']['channeldlimit']) ? $jieqiConfigs['system']['exchangerate'] : 0;
$jieqiTpl->assign('exchangerate', $exchangerate);
$settlemin = isset($jieqiConfigs['system']['settlemin']) && is_numeric($jieqiConfigs['system']['settlemin']) ? $jieqiConfigs['system']['settlemin'] : 0;
$jieqiTpl->assign('settlemin', $settlemin);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/promotion.html';
include_once JIEQI_ROOT_PATH . '/footer.php';