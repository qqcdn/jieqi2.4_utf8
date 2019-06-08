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
include_once JIEQI_ROOT_PATH . '/header.php';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
if (!empty($_POST['act']) && $_POST['act'] == 'unbind' && !empty($_REQUEST['apiname']) && !empty($jieqi_api_sites[$_REQUEST['apiname']]['publish'])) {
    jieqi_checkpost();
    $_REQUEST['apiorder'] = intval($jieqi_api_sites[$_REQUEST['apiname']]['apiorder']);
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE  uid = ' . intval($_SESSION['jieqiUserId']) . ' LIMIT 0, 1';
    $query->execute($sql);
    $apirow = $query->getRow();
    $flagnum = pow(2, $_REQUEST['apiorder'] - 1);
    $flagstr = str_repeat('1', 30);
    $flagstr[30 - $_REQUEST['apiorder']] = '0';
    if ($apirow['apiflag'] == $flagnum) {
        $sql = 'DELETE FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE uid = ' . intval($apirow['uid']);
    } else {
        $sql = 'UPDATE ' . jieqi_dbprefix('system_userapi') . ' SET apiflag = apiflag & ' . bindec($flagstr) . ', ' . jieqi_dbslashes($_REQUEST['apiname']) . 'id = \'\' WHERE uid = ' . intval($apirow['uid']);
    }
    $query->execute($sql);
    $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET conisbind = conisbind & ' . bindec($flagstr) . ' WHERE uid = ' . intval($_SESSION['jieqiUserId']);
    $query->execute($sql);
    jieqi_jumppage(JIEQI_URL . '/userbind.php', '', '', true);
    exit;
}
jieqi_getconfigs('system', 'honors');
include_once JIEQI_ROOT_PATH . '/include/funusers.php';
$uservals = jieqi_system_usersvars($jieqiUsers);
$jieqiTpl->assign_by_ref('uservals', $uservals);
foreach ($uservals as $k => $v) {
    $jieqiTpl->assign_by_ref($k, $uservals[$k]);
}
$sql = 'SELECT * FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE  uid = ' . intval($_SESSION['jieqiUserId']) . ' LIMIT 0, 1';
$query->execute($sql);
$apirow = $query->getRow();
$bindcount = 0;
$userapirows = jieqi_funtoarray('jieqi_htmlstr', $jieqi_api_sites);
foreach ($userapirows as $k => $v) {
    if (empty($apirow[$k . 'id'])) {
        $userapirows[$k]['isbind'] = 0;
    } else {
        $userapirows[$k]['isbind'] = 1;
        $bindcount++;
    }
}
$jieqiTpl->assign_by_ref('userapirows', $userapirows);
$jieqiTpl->assign('bindcount', $bindcount);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/userbind.html';
include_once JIEQI_ROOT_PATH . '/footer.php';