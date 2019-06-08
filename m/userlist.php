<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['system']['viewuser'], $jieqiUsersStatus, $jieqiUsersGroup, false);
if (empty($_REQUEST['group'])) {
    $_REQUEST['group'] = 0;
}
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/userlist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$criteria = new CriteriaCompo();
if (!empty($_REQUEST['group'])) {
    $criteria->add(new Criteria('groupid', $_REQUEST['group']));
    $criteria->setSort('uid');
    $criteria->setOrder('DESC');
} else {
    if (!empty($_REQUEST['initial'])) {
        $criteria->add(new Criteria('initial', strtoupper($_REQUEST['initial']), '='));
        $criteria->setSort('uname');
        $criteria->setOrder('ASC');
    }
}
if (isset($_REQUEST['isvip']) && is_numeric($_REQUEST['isvip'])) {
    $_REQUEST['isvip'] = intval($_REQUEST['isvip']);
    $criteria->add(new Criteria('isvip', $_REQUEST['isvip']));
}
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$users_handler->queryObjects($criteria);
$userrows = array();
$k = 0;
include_once JIEQI_ROOT_PATH . '/include/funusers.php';
while ($v = $users_handler->getObject()) {
    $userrows[$k] = jieqi_system_usersvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('userrows', $userrows);
$jieqiTpl->assign('url_initial', JIEQI_URL . '/userlist.php?initial=');
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $users_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';