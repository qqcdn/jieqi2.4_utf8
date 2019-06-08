<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/online.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
include_once JIEQI_ROOT_PATH . '/class/online.php';
$online_handler = JieqiOnlineHandler::getInstance('JieqiOnlineHandler');
if (isset($_POST['act']) && $_POST['act'] == 'del' && !empty($_REQUEST['sid'])) {
    jieqi_upusersession($_REQUEST['sid'], false);
    $criteria = new CriteriaCompo(new Criteria('sid', $_REQUEST['sid'], '='));
    $online_handler->delete($criteria);
    unset($criteria);
    if (!empty($_REQUEST['ajax_request'])) {
        jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
    }
}
$criteria = new CriteriaCompo(new Criteria('updatetime', JIEQI_NOW_TIME - $jieqiConfigs['system']['onlinetime'], '>'));
$allnum = $online_handler->getCount($criteria);
$criteria->add(new Criteria('uid', '0'), '>');
if (isset($_REQUEST['username']) && !empty($_REQUEST['username'])) {
    $criteria->add(new Criteria('uname', $_REQUEST['username'], '='));
} else {
    if (isset($_REQUEST['groupid']) && !empty($_REQUEST['groupid'])) {
        $criteria->add(new Criteria('groupid', $_REQUEST['groupid'], '='));
    }
}
$criteria->setSort('updatetime');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$result = $online_handler->queryObjects($criteria);
$userrows = array();
$k = 0;
while ($v = $online_handler->getObject()) {
    $userrows[$k] = jieqi_query_rowvars($v);
    if (strlen($userrows[$k]['name']) == 0) {
        $userrows[$k]['name'] = $userrows[$k]['username'];
    }
    $userrows[$k]['group'] = $jieqiGroups[$v->getVar('groupid')];
    $k++;
}
$jieqiTpl->assign_by_ref('userrows', $userrows);
$rowcount = $online_handler->getCount($criteria);
$jieqiTpl->assign_by_ref('rowcount', $rowcount);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $rowcount;
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, false);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';