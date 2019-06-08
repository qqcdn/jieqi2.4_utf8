<?php

define('JIEQI_MODULE_NAME', 'pay');
require_once '../../global.php';
jieqi_checklogin();
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
include_once $jieqiModules['pay']['path'] . '/class/paylog.php';
$paylog_handler = JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/paylog.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('buyid', $_SESSION['jieqiUserId'], '='));
if (!isset($_REQUEST['status'])) {
    $_REQUEST['status'] = NULL;
}
if ($_REQUEST['status'] == 'finish') {
    $criteria->add(new Criteria('payflag', 0, '>'));
} else {
    if ($_REQUEST['status'] == 'cancel') {
        $criteria->add(new Criteria('payflag', 0, '='));
    }
}
$criteria->setSort('payid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$paylog_handler->queryObjects($criteria);
$paylogrows = array();
$k = 0;
jieqi_getconfigs(JIEQI_MODULE_NAME, 'paytype');
while ($v = $paylog_handler->getObject()) {
    $paylogrows[$k] = jieqi_query_rowvars($v);
    if (isset($jieqiPaytype[$v->getVar('paytype', 'n')])) {
        $paylogrows[$k]['paytype'] = $jieqiPaytype[$v->getVar('paytype', 'n')]['shortname'];
    }
    $k++;
}
$jieqiTpl->assign_by_ref('paylogrows', $paylogrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $paylog_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';