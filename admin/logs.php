<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuserlog'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
include_once JIEQI_ROOT_PATH . '/class/logs.php';
$logs_handler = JieqilogsHandler::getInstance('JieqilogsHandler');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/logs.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
$criteria = new CriteriaCompo();
if (!empty($_REQUEST['keyword'])) {
    if ($_REQUEST['keytype'] == 1) {
        $criteria->add(new Criteria('toname', $_REQUEST['keyword'], '='));
    } else {
        $criteria->add(new Criteria('fromname', $_REQUEST['keyword'], '='));
    }
}
if (!empty($_REQUEST['logtype'])) {
    $jieqiTpl->assign('logtype', $_REQUEST['logtype']);
    $criteria->add(new Criteria('logtype', $_REQUEST['logtype'], '='));
} else {
    $jieqiTpl->assign('logtype', '');
}
if (!empty($_REQUEST['loglevel'])) {
    $jieqiTpl->assign('loglevel', $_REQUEST['loglevel']);
    $criteria->add(new Criteria('loglevel', $_REQUEST['loglevel'], '='));
} else {
    $jieqiTpl->assign('loglevel', '');
}
$criteria->setSort('logid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$logs_handler->queryObjects($criteria);
$logrows = array();
$k = 0;
while ($v = $logs_handler->getObject()) {
    $logrows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('logrows', $logrows);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'lsort', 'jieqiLsort');
if (!isset($jieqiLsort)) {
    $jieqiLsort = array();
}
$jieqiTpl->assign_by_ref('logsort', $jieqiLsort);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $logs_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';