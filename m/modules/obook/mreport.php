<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_checklogin();
jieqi_loadlang('mreport', JIEQI_MODULE_NAME);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/mreport.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_getconfigs('obook', 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$uid = intval($_SESSION['jieqiUserId']);
$slimit = '(authorid = ' . $uid . ' OR posterid = ' . $uid . ' OR agentid = ' . $uid . ')';
if (isset($_REQUEST['oid'])) {
    $_REQUEST['obookid'] = $_REQUEST['oid'];
    unset($_REQUEST['oid']);
}
if (!isset($_REQUEST['obookid'])) {
    $_REQUEST['obookid'] = 0;
}
if (!isset($_REQUEST['obookname'])) {
    $_REQUEST['obookname'] = '';
}
if (!isset($_REQUEST['paidstatus'])) {
    $_REQUEST['paidstatus'] = '';
}
if (!isset($_REQUEST['reportmonth'])) {
    $_REQUEST['reportmonth'] = 0;
}
if (!isset($_REQUEST['startyear'])) {
    $_REQUEST['startyear'] = 0;
}
if (!isset($_REQUEST['endyear'])) {
    $_REQUEST['endyear'] = 0;
}
if (!isset($_REQUEST['startmonth'])) {
    $_REQUEST['startmonth'] = 0;
}
if (!isset($_REQUEST['endmonth'])) {
    $_REQUEST['endmonth'] = 0;
}
if (!empty($_REQUEST['obookid'])) {
    $slimit .= ' AND obookid = ' . intval($_REQUEST['obookid']);
} else {
    if (!empty($_REQUEST['obookname'])) {
        $slimit .= ' AND obookname = \'' . jieqi_dbslashes($_REQUEST['obookname']) . '\'';
    }
}
if (!empty($_REQUEST['paidstatus'])) {
    switch ($_REQUEST['paidstatus']) {
        case 'authorprepaid':
            $slimit .= ' AND (authorpaid & 1) = 1';
            break;
        case 'authordeppaid':
            $slimit .= ' AND (authorpaid & 2) = 2';
            break;
        case 'authorpaid':
            $slimit .= ' AND (authorpaid & 3) = 3';
            break;
        case 'posterprepaid':
            $slimit .= ' AND (posterpaid & 1) = 1';
            break;
        case 'posterdeppaid':
            $slimit .= ' AND (posterpaid & 2) = 2';
            break;
        case 'posterpaid':
            $slimit .= ' AND (posterpaid & 3) = 3';
            break;
        case 'agentprepaid':
            $slimit .= ' AND (agentpaid & 1) = 1';
            break;
        case 'agentdeppaid':
            $slimit .= ' AND (agentpaid & 2) = 2';
            break;
        case 'agentpaid':
            $slimit .= ' AND (agentpaid & 3) = 3';
            break;
        case 'masterprepaid':
            $slimit .= ' AND (masterpaid & 1) = 1';
            break;
        case 'masterdeppaid':
            $slimit .= ' AND (masterpaid & 2) = 2';
            break;
        case 'masterpaid':
            $slimit .= ' AND (masterpaid & 3) = 3';
            break;
        case 'siteprepaid':
            $slimit .= ' AND (sitepaid & 1) = 1';
            break;
        case 'sitedeppaid':
            $slimit .= ' AND (sitepaid & 2) = 2';
            break;
        case 'sitepaid':
            $slimit .= ' AND (sitepaid & 3) = 3';
            break;
        case 'otherprepaid':
            $slimit .= ' AND (otherpaid & 1) = 1';
            break;
        case 'otherdeppaid':
            $slimit .= ' AND (otherpaid & 2) = 2';
            break;
        case 'otherpaid':
            $slimit .= ' AND (otherpaid & 3) = 3';
            break;
    }
}
if (!empty($_REQUEST['reportmonth'])) {
    if (!empty($_REQUEST['reportyear']) && strlen($_REQUEST['reportmonth']) <= 2) {
        $reportmonth = intval($_REQUEST['reportyear'] . sprintf('%02d', $_REQUEST['reportmonth']));
    } else {
        $reportmonth = intval(str_replace(array('-', ' ', '/'), '', $_REQUEST['reportmonth']));
    }
    $slimit .= ' AND reportmonth = ' . $reportmonth;
}
if (!empty($_REQUEST['startmonth'])) {
    if (!empty($_REQUEST['startyear']) && strlen($_REQUEST['startmonth']) <= 2) {
        $startmonth = intval($_REQUEST['startyear'] . sprintf('%02d', $_REQUEST['startmonth']));
    } else {
        $startmonth = intval(str_replace(array('-', ' ', '/'), '', $_REQUEST['startmonth']));
    }
    $slimit .= ' AND reportmonth >= ' . $startmonth;
}
if (!empty($_REQUEST['endmonth'])) {
    if (!empty($_REQUEST['endyear']) && strlen($_REQUEST['endmonth']) <= 2) {
        $endmonth = intval($_REQUEST['endyear'] . sprintf('%02d', $_REQUEST['endmonth']));
    } else {
        $endmonth = intval(str_replace(array('-', ' ', '/'), '', $_REQUEST['endmonth']));
    }
    $slimit .= ' AND reportmonth <= ' . $endmonth;
}
$sql = 'SELECT * FROM ' . jieqi_dbprefix('obook_mreport') . ' WHERE ' . $slimit . ' ORDER BY reportmonth DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
$query->execute($sql);
$mreportrows = array();
$k = 0;
while ($row = $query->getRow()) {
    $mreportrows[$k] = jieqi_query_rowvars($row, 's', 'article');
    $mreportrows[$k]['authorprepay'] = $mreportrows[$k]['authormoney'] - $mreportrows[$k]['authordeposit'];
    $mreportrows[$k]['posterprepay'] = $mreportrows[$k]['postermoney'] - $mreportrows[$k]['posterdeposit'];
    $mreportrows[$k]['agentprepay'] = $mreportrows[$k]['agentmoney'] - $mreportrows[$k]['agentdeposit'];
    $mreportrows[$k]['masterprepay'] = $mreportrows[$k]['mastermoney'] - $mreportrows[$k]['masterdeposit'];
    $mreportrows[$k]['siteprepay'] = $mreportrows[$k]['sitemoney'] - $mreportrows[$k]['sitedeposit'];
    $mreportrows[$k]['otherprepay'] = $mreportrows[$k]['othermoney'] - $mreportrows[$k]['otherdeposit'];
    $k++;
}
$jieqiTpl->assign_by_ref('mreportrows', $mreportrows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
$dyear = intval(date('Y', JIEQI_NOW_TIME));
$dmonth = intval(date('m', JIEQI_NOW_TIME));
if ($dmonth == 1) {
    $dyear--;
    $dmonth = 12;
} else {
    $dmonth--;
}
$ryearrows = array();
for ($i = $dyear; $dyear - 10 <= $i; $i--) {
    $ryearrows[] = $i;
}
$rmonthrows = array();
for ($i = 1; $i <= 12; $i++) {
    $rmonthrows[] = $i;
}
$jieqiTpl->assign('ryearrows', $ryearrows);
$jieqiTpl->assign('rmonthrows', $rmonthrows);
$jieqiTpl->assign('dyear', $dyear);
$jieqiTpl->assign('dmonth', $dmonth);
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('authorid', $_SESSION['jieqiUserId']), 'OR');
$criteria->add(new Criteria('agentid', $_SESSION['jieqiUserId']), 'OR');
$criteria->add(new Criteria('posterid', $_SESSION['jieqiUserId']), 'OR');
$criteria->setSort('lastupdate');
$criteria->setOrder('DESC');
$criteria->setLimit(200);
$criteria->setStart(0);
$obook_handler->queryObjects($criteria);
$obookrows = array();
$k = 0;
include_once $jieqiModules['obook']['path'] . '/include/funobook.php';
while ($v = $obook_handler->getObject()) {
    $obookrows[$k] = jieqi_obook_obookvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('obookrows', $obookrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$sql = 'SELECT count(*) AS cot, sum(sumegold) as sumegold, sum(sumesilver) as sumesilver, sum(sumtip) as sumtip, sum(sumhurry) as sumhurry, sum(sumbesp) as sumbesp, sum(sumaward) as sumaward, sum(sumagent) as sumagent, sum(sumgift) as sumgift, sum(sumother) as sumother, sum(sumemoney) as sumemoney, sum(summoney) as summoney, sum(paidmoney) as paidmoney, sum(authormoney) as authormoney, sum(authordeposit) as authordeposit, sum(postermoney) as postermoney, sum(posterdeposit) as posterdeposit, sum(agentmoney) as agentmoney, sum(agentdeposit) as agentdeposit, sum(mastermoney) as mastermoney, sum(masterdeposit) as masterdeposit, sum(sitemoney) as sitemoney, sum(sitedeposit) as sitedeposit, sum(othermoney) as othermoney, sum(otherdeposit) as otherdeposit FROM ' . jieqi_dbprefix('obook_mreport') . ' WHERE ' . $slimit;
$query->execute($sql);
$row = $query->getRow();
$row['authorprepay'] = $row['authormoney'] - $row['authordeposit'];
$row['posterprepay'] = $row['postermoney'] - $row['posterdeposit'];
$row['agentprepay'] = $row['agentmoney'] - $row['agentdeposit'];
$row['masterprepay'] = $row['mastermoney'] - $row['masterdeposit'];
$row['siteprepay'] = $row['sitemoney'] - $row['sitedeposit'];
$row['otherprepay'] = $row['othermoney'] - $row['otherdeposit'];
$jieqiTpl->assign('mreportstat', jieqi_funtoarray('jieqi_htmlstr', $row));
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->assign('authorarea', 1);
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';