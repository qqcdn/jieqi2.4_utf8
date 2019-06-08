<?php

define('JIEQI_MODULE_NAME', 'pay');
require_once '../../../global.php';
jieqi_getconfigs('pay', 'power');
jieqi_getconfigs('system', 'power');
if (!isset($jieqiPower['system']['adminpaylog'])) {
    $jieqiPower['system']['adminpaylog'] = array('caption' => '', 'groups' => false, 'description' => '');
}
if (!jieqi_checkpower($jieqiPower['system']['adminpaylog'], $jieqiUsersStatus, $jieqiUsersGroup, true, true) && !jieqi_checkpower($jieqiPower['pay']['adminpaylog'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) {
    jieqi_printfail(LANG_NO_PERMISSION);
}
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
include_once $jieqiModules['pay']['path'] . '/class/paylog.php';
$paylog_handler = JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
if (!empty($_POST['act'])) {
    jieqi_checkpost();
    switch ($_POST['act']) {
        case 'confirm':
            $_POST['id'] = intval($_POST['id']);
            $tmplog = $paylog_handler->get($_POST['id']);
            if (is_object($tmplog) && $tmplog->getVar('payflag') == 0) {
                $paytype = $tmplog->getVar('paytype', 'n');
                define('JIEQI_PAY_TYPE', $paytype);
                jieqi_loadlang('pay', JIEQI_MODULE_NAME);
                jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
                include $jieqiModules['pay']['path'] . '/include/funpay.php';
                $payinfo = array('orderid' => intval($tmplog->getVar('payid', 'n')), 'retserialno' => '', 'retaccount' => '', 'retinfo' => '', 'return' => true, 'manual' => true);
                jieqi_pay_return($payinfo);
            }
            jieqi_jumppage($jieqiModules['pay']['url'] . '/admin/paylog.php', '', '', true);
            break;
        case 'del':
            $_POST['id'] = intval($_POST['id']);
            $criteria = new CriteriaCompo(new Criteria('payid', $_POST['id'], '='));
            $criteria->add(new Criteria('payflag', 0));
            $paylog_handler->delete($criteria);
            unset($criteria);
            jieqi_jumppage($jieqiModules['pay']['url'] . '/admin/paylog.php', '', '', true);
            break;
        case 'clean':
            if (empty($_POST['days']) || intval($_POST['days']) < 1) {
                jieqi_printfail(LANG_ERROR_PARAMETER);
            }
            $_POST['days'] = intval($_POST['days']);
            $btime = time() - 3600 * 24 * $_POST['days'];
            $criteria = new CriteriaCompo(new Criteria('buytime', $btime, '<'));
            $criteria->add(new Criteria('payflag', 0));
            $paylog_handler->delete($criteria);
            unset($criteria);
            jieqi_jumppage($jieqiModules['pay']['url'] . '/admin/paylog.php', LANG_DO_SUCCESS, $jieqiLang['pay']['paylog_clean_success']);
            break;
    }
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('system', 'channels', 'jieqiChannels');
if (empty($jieqiChannels)) {
    $jieqiChannels = array();
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/admin/paylog.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'paytype');
$criteria = new CriteriaCompo();
if (!isset($_REQUEST['keytype'])) {
    $_REQUEST['keytype'] = NULL;
}
if (!isset($_REQUEST['keyword'])) {
    $_REQUEST['keyword'] = '';
}
if (!isset($_REQUEST['payflag'])) {
    $_REQUEST['payflag'] = NULL;
}
if (!isset($_REQUEST['datestart'])) {
    $_REQUEST['datestart'] = '';
}
if (!isset($_REQUEST['dateend'])) {
    $_REQUEST['dateend'] = '';
}
if (!empty($_REQUEST['keyword'])) {
    switch ($_REQUEST['keytype']) {
        case 'payid':
            $criteria->add(new Criteria('payid', intval($_REQUEST['keyword']), '='));
            break;
        case 'payflag':
            switch ($_REQUEST['keyword']) {
                case $jieqiLang['pay']['state_unconfirm']:
                    $payflag = 0;
                    break;
                case 216:
                    $payflag = 1;
                    break;
                case 219:
                    $payflag = 2;
                    break;
                default:
                    $payflag = -1;
                    break;
            }
            if (0 <= $payflag) {
                $criteria->add(new Criteria('payflag', $payflag, '='));
            }
            break;
        case 'paytype':
            $paytype = $_REQUEST['keyword'];
            foreach ($jieqiPaytype as $k => $v) {
                if ($_REQUEST['keyword'] == $v['name'] || $_REQUEST['keyword'] == $v['shortname']) {
                    $paytype = $k;
                    break;
                }
            }
            if (!empty($paytype)) {
                $criteria->add(new Criteria('paytype', $paytype, '='));
            }
            break;
        case 'money':
            $criteria->add(new Criteria('money', intval($_REQUEST['keyword'] * 100), '='));
            break;
        case 'egold':
            $criteria->add(new Criteria('egold', intval($_REQUEST['keyword']), '='));
            break;
        case 'channel':
            $channel = $_REQUEST['keyword'];
            foreach ($jieqiChannels as $k => $v) {
                if ($_REQUEST['keyword'] == $v['name']) {
                    $channel = $k;
                    break;
                }
            }
            if (!empty($channel)) {
                $criteria->add(new Criteria('channel', $channel, '='));
            }
            break;
        case 'buyid':
            $criteria->add(new Criteria('buyid', intval($_REQUEST['keyword']), '='));
            break;
        case 'buyname':
        default:
            $criteria->add(new Criteria('buyname', $_REQUEST['keyword'], '='));
            break;
    }
}
switch ($_REQUEST['payflag']) {
    case 'success':
        $criteria->add(new Criteria('payflag', 0, '>'));
        break;
    case 'failure':
        $criteria->add(new Criteria('payflag', 0, '='));
        break;
}
if (!empty($_REQUEST['datestart'])) {
    $tmpvar = @strtotime($_REQUEST['datestart']);
    if (0 < $tmpvar) {
        $criteria->add(new Criteria('buytime', $tmpvar, '>='));
    }
}
if (!empty($_REQUEST['dateend'])) {
    $_REQUEST['dateend'] = trim($_REQUEST['dateend']);
    $tmpvar = @strtotime($_REQUEST['dateend']);
    if (strpos($_REQUEST['dateend'], ' ') === false) {
        $tmpvar += 3600 * 24 - 1;
    }
    if (0 < $tmpvar) {
        $criteria->add(new Criteria('buytime', $tmpvar, '<='));
    }
}
$criteria->setSort('payid');
$criteria->setOrder('DESC');
if (!empty($_REQUEST['isexport'])) {
    jieqi_getconfigs('pay', 'export');
    header('Accept-Ranges: bytes');
    if ($_REQUEST['exportformat'] == 'exceltext') {
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=paylog.xls');
    } else {
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename=paylog.txt');
    }
    foreach ($jieqiExport['pay'] as $v) {
        echo $v['caption'] . '	';
    }
    echo "\n";
    $paylog_handler->queryObjects($criteria);
    while ($v = $paylog_handler->getObject()) {
        $row = jieqi_query_rowvars($v);
        if (isset($jieqiPaytype[$v->getVar('paytype', 'n')])) {
            $row['paytype'] = $jieqiPaytype[$v->getVar('paytype', 'n')]['shortname'];
        }
        $row['buytime'] = date('Y-m-d H:i:s', $row['buytime']);
        $row['rettime'] = date('Y-m-d H:i:s', $row['rettime']);
        $row['money_n'] = $row['money'];
        $row['money'] = floatval(intval($row['money']) / 100);
        $row['channel_n'] = $row['channel'];
        if (isset($jieqiChannels[$row['channel']])) {
            $row['channel'] = jieqi_htmlstr($jieqiChannels[$row['channel']]['name']);
        }
        foreach ($jieqiExport['pay'] as $k => $v) {
            if (isset($row[$k])) {
                echo $row[$k];
            }
            echo '	';
        }
        echo "\n";
    }
    exit;
}
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$paylog_handler->queryObjects($criteria);
$paylogrows = array();
$k = 0;
while ($v = $paylog_handler->getObject()) {
    $paylogrows[$k] = jieqi_query_rowvars($v);
    if (isset($jieqiPaytype[$v->getVar('paytype', 'n')])) {
        $paylogrows[$k]['paytype'] = $jieqiPaytype[$v->getVar('paytype', 'n')]['shortname'];
    }
    $paylogrows[$k]['channel_n'] = $paylogrows[$k]['channel'];
    if (isset($jieqiChannels[$paylogrows[$k]['channel']])) {
        $paylogrows[$k]['channel'] = jieqi_htmlstr($jieqiChannels[$paylogrows[$k]['channel']]['name']);
    }
    $k++;
}
$jieqiTpl->assign_by_ref('paylogrows', $paylogrows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
$jieqiTpl->assign('jieqi_channels', jieqi_funtoarray('jieqi_htmlstr', $jieqiChannels));
$jieqiTpl->assign('jieqi_channelnum', count($jieqiChannels));
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = 'SELECT count(*) as cot, sum(egold) as sumegold, sum(money) as summoney FROM ' . jieqi_dbprefix('pay_paylog') . ' ' . $criteria->renderWhere();
$query->execute($sql);
$paylogstat = $query->getRow();
$paylogstat = jieqi_funtoarray('jieqi_htmlstr', $paylogstat);
$jieqiTpl->assign_by_ref('paylogstat', $paylogstat);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $paylog_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';