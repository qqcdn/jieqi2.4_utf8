<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['system']['viewuser'], $jieqiUsersStatus, $jieqiUsersGroup, false);
if (isset($_GET['module']) && !preg_match('/^\\w+$/', $_GET['module']) || isset($_GET['template']) && !preg_match('/^\\w+$/', $_GET['template'])) {
    exit('//error parameter');
}
if (empty($_GET['module']) || !isset($jieqiModules[$_GET['module']])) {
    $_GET['module'] = 'system';
}
if (empty($_GET['template'])) {
    $_GET['template'] = 'userselect';
}
$jieqiTset['jieqi_contents_template'] = $_GET['module'] == 'system' ? JIEQI_ROOT_PATH . '/templates/' . $_GET['template'] . '.html' : JIEQI_ROOT_PATH . '/modules/' . $_GET['module'] . '/templates/' . $_GET['template'] . '.html';
include_once JIEQI_ROOT_PATH . '/header.php';
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$criteria = new CriteriaCompo();
if (!empty($_REQUEST['group'])) {
    $criteria->add(new Criteria('groupid', intval($_REQUEST['group'])));
    $criteria->setSort('name');
    $criteria->setOrder('ASC');
}
$criteria->setLimit(200);
$criteria->setStart(0);
$users_handler->queryObjects($criteria);
$userrows = array();
$k = 0;
include_once JIEQI_ROOT_PATH . '/include/funusers.php';
while ($v = $users_handler->getObject()) {
    $userrows[$k] = jieqi_system_usersvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('userrows', $userrows);
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';