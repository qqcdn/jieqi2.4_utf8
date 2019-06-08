<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminmessage'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('report', JIEQI_MODULE_NAME);
jieqi_getconfigs('system', 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'rsort', 'jieqiRsort');
include_once JIEQI_ROOT_PATH . '/class/report.php';
$report_handler = JieqiReportHandler::getInstance('JieqiReportHandler');
if (isset($_POST['act']) && $_POST['act'] == 'delete' && is_array($_REQUEST['checkid']) && 0 < count($_REQUEST['checkid'])) {
    jieqi_checkpost();
    $where = '';
    foreach ($_REQUEST['checkid'] as $v) {
        if (is_numeric($v)) {
            $v = intval($v);
            if (!empty($where)) {
                $where .= ', ';
            }
            $where .= $v;
        }
    }
    if (!empty($where)) {
        $sql = 'DELETE FROM ' . jieqi_dbprefix('system_report') . ' WHERE reportid IN (' . $where . ')';
        $report_handler->execute($sql);
    }
    $_POST['act'] = 0;
    jieqi_jumppage(JIEQI_URL . '/admin/reportlist.php', '', '', true);
    exit;
}
if (isset($_POST['act'])) {
    unset($_POST['act']);
}
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/reportlist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
$reportrows = array();
$criteria = new CriteriaCompo();
if (!empty($_REQUEST['keyword']) && !empty($_REQUEST['keytype'])) {
    switch ($_REQUEST['keytype']) {
        case 'reportname':
            $criteria->add(new Criteria('reportname', $_REQUEST['keyword']));
            break;
        case 'authname':
            $criteria->add(new Criteria('authname', $_REQUEST['keyword']));
            break;
        case 'reporttitle':
            $criteria->add(new Criteria('reporttitle', '%' . $_REQUEST['keyword'] . '%', 'LIKE'));
            break;
    }
    $_GET['keyword'] = $_REQUEST['keyword'];
    $_GET['keytype'] = $_REQUEST['keytype'];
}
$criteria->setSort('reportid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$report_handler->queryObjects($criteria);
$k = 0;
while ($v = $report_handler->getObject()) {
    $reportrows[$k] = jieqi_query_rowvars($v);
    $reportrows[$k]['sortname'] = $jieqiRsort[$reportrows[$k]['reportsort']]['caption'];
    if (isset($jieqiRsort[$reportrows[$k]['reportsort']]['types'][$reportrows[$k]['reporttype']])) {
        $reportrows[$k]['typename'] = $jieqiRsort[$reportrows[$k]['reportsort']]['types'][$reportrows[$k]['reporttype']];
    } else {
        $reportrows[$k]['typename'] = $jieqiLang['system']['report_type_other'];
    }
    $k++;
}
$jieqiTpl->assign_by_ref('reportrows', $reportrows);
$jieqiTpl->assign_by_ref('rsortrows', $jieqiRsort);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $report_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';