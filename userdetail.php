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
$jieqiTpl->assign_by_ref('gift', $uservals['setting']['gift']);
if (!empty($_REQUEST['sendemail'])) {
    $jieqiTpl->assign('sendemail', 1);
} else {
    $jieqiTpl->assign('sendemail', 0);
}
if (!empty($_REQUEST['refresh'])) {
    $jieqiTpl->assign('refresh', 1);
} else {
    $jieqiTpl->assign('refresh', 0);
}
$email_link = '';
$tmpvar = explode('@', $jieqiUsers->getVar('email', 'n'));
if (isset($tmpvar[1])) {
    $email_link = 'http://mail.' . $tmpvar[1];
}
$jieqiTpl->assign('email_link', $email_link);
include_once JIEQI_ROOT_PATH . '/class/message.php';
$message_handler = JieqiMessageHandler::getInstance('JieqiMessageHandler');
$criteria = new CriteriaCompo(new Criteria('toid', $jieqiUsers->getVar('uid'), '='));
$criteria->add(new Criteria('isread', 0, '='));
$criteria->add(new Criteria('todel', 0, '='));
$newmsgnum = $message_handler->getCount($criteria);
unset($criteria);
$jieqiTpl->assign('newmessage', $newmsgnum);
$_SESSION['jieqiNewMessage'] = $newmsgnum;
$right = array();
jieqi_getconfigs('system', 'configs');
jieqi_getconfigs('system', 'right');
foreach ($jieqiModules as $k => $v) {
    if ($v['publish'] && is_dir(JIEQI_ROOT_PATH . '/modules/' . $k)) {
        @jieqi_getconfigs($k, 'configs');
        @jieqi_getconfigs($k, 'right');
    }
}
$honorid = jieqi_gethonorid($jieqiUsers->getVar('score'), $jieqiHonors);
foreach ($jieqiRight as $mod => $t) {
    foreach ($t as $r => $v) {
        $tmpvar = 0;
        if (isset($jieqiConfigs[$mod][$r])) {
            $tmpvar = $jieqiConfigs[$mod][$r];
        }
        if ($honorid && isset($jieqiRight[$mod][$r]['honors'][$honorid]) && is_numeric($jieqiRight[$mod][$r]['honors'][$honorid])) {
            $tmpvar = intval($jieqiRight[$mod][$r]['honors'][$honorid]);
        }
        $right[$mod][$r] = $tmpvar;
        $jieqiTpl->assign($mod . '_' . $r, $tmpvar);
    }
}
$jieqiTpl->assign_by_ref('right', $right);
$channelerate = isset($jieqiConfigs['system']['channelerate']) ? $jieqiConfigs['system']['channelerate'] : 0;
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
$exchangerate = isset($jieqiConfigs['system']['exchangerate']) ? $jieqiConfigs['system']['exchangerate'] : 0;
$jieqiTpl->assign('exchangerate', $exchangerate);
$settlemin = isset($jieqiConfigs['system']['settlemin']) && is_numeric($jieqiConfigs['system']['settlemin']) ? $jieqiConfigs['system']['settlemin'] : 0;
$jieqiTpl->assign('settlemin', $settlemin);
if (!empty($_SESSION['jieqiUserId']) && $jieqiUsers->getVar('uid', 'n') == $_SESSION['jieqiUserId']) {
    $jieqiUsers->saveToSession();
}
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/userdetail.html';
include_once JIEQI_ROOT_PATH . '/footer.php';