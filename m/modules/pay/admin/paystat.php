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
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'paytype');
jieqi_getconfigs('system', 'channels', 'jieqiChannels');
if (empty($jieqiChannels)) {
    $jieqiChannels = array();
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/admin/paystat.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$slimit = 'payflag > 0';
if (empty($_REQUEST['statall'])) {
    $slimit .= ' AND moneytype >= 0';
}
$datefield = '';
if (!empty($_REQUEST['datestart'])) {
    $_REQUEST['datestart'] = trim(str_replace(array('-', '/', ' '), '', $_REQUEST['datestart']));
    if (strlen($_REQUEST['datestart']) == 6) {
        $datefield = 'buymonth';
    } else {
        if (strlen($_REQUEST['datestart']) == 8) {
            $datefield = 'buydate';
        }
    }
}
if (empty($datefield)) {
    $datefield = isset($_REQUEST['datefield']) && $_REQUEST['datefield'] == 'buymonth' ? 'buymonth' : 'buydate';
    $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
    $_REQUEST['datestart'] = $datefield == 'buymonth' ? date('Ym', mktime(0, 0, 0, (int) $tmpvar[1] - 12, 1, (int) $tmpvar[0])) : date('Ymd', mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2] - 30, (int) $tmpvar[0]));
}
if (!empty($_REQUEST['dateend'])) {
    $_REQUEST['dateend'] = trim(str_replace(array('-', '/', ' '), '', $_REQUEST['dateend']));
}
if (empty($_REQUEST['dateend'])) {
    $_REQUEST['dateend'] = $datefield == 'buydate' ? date('Ymd', JIEQI_NOW_TIME) : date('Ym', JIEQI_NOW_TIME);
}
$_REQUEST['datestart'] = intval($_REQUEST['datestart']);
$_REQUEST['dateend'] = intval($_REQUEST['dateend']);
$slimit .= ' AND ' . $datefield . ' >= ' . $_REQUEST['datestart'] . ' AND ' . $datefield . ' <= ' . $_REQUEST['dateend'];
if (!empty($_REQUEST['channel'])) {
    $slimit .= ' AND channel = \'' . jieqi_dbslashes($_REQUEST['channel']) . '\'';
}
if (!empty($_REQUEST['paytype'])) {
    $slimit .= ' AND paytype = \'' . jieqi_dbslashes($_REQUEST['paytype']) . '\'';
}
$sql = 'SELECT ' . $datefield . ', SUM(money) AS summoney, SUM(egold) AS sumegold, count(*) AS paycount FROM ' . jieqi_dbprefix('pay_paylog') . ' WHERE ' . $slimit . ' GROUP BY ' . $datefield . ' ORDER BY ' . $datefield . ' DESC LIMIT 0, 100';
$query->execute($sql);
if (!empty($_REQUEST['isexport'])) {
    jieqi_getconfigs('pay', 'exportstat', 'jieqiExport');
    header('Accept-Ranges: bytes');
    if ($_REQUEST['exportformat'] == 'exceltext') {
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=paystat_' . date('Ymd') . '.xls');
    } else {
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename=paystat_' . date('Ymd') . '.txt');
    }
    foreach ($jieqiExport['paystat'] as $v) {
        echo $v['caption'] . '	';
    }
    echo "\n";
    while ($row = $query->getRow()) {
        $row = jieqi_query_rowvars($row);
        $row['datevalue'] = $row[$datefield];
        $row['summoney_n'] = $row['summoney'];
        $row['summoney'] = floatval(intval($row['summoney']) / 100);
        if (isset($row['channel'])) {
            $row['channel_n'] = $row['channel'];
            if (isset($jieqiChannels[$row['channel']])) {
                $row['channel'] = jieqi_htmlstr($jieqiChannels[$row['channel']]['name']);
            }
        }
        foreach ($jieqiExport['paystat'] as $k => $v) {
            if (isset($row[$k])) {
                echo $row[$k];
            }
            echo '	';
        }
        echo "\n";
    }
    exit;
}
$paystatrows = array();
$k = 0;
$countmax = 0;
$paystatsum = array('cot' => 0, 'sumegold' => 0, 'summoney' => 0);
while ($row = $query->getRow()) {
    $paystatrows[$k] = jieqi_query_rowvars($row);
    $paystatrows[$k]['datevalue'] = $paystatrows[$k][$datefield];
    if ($countmax < $paystatrows[$k]['paycount']) {
        $countmax = $paystatrows[$k]['paycount'];
    }
    $paystatsum['cot']++;
    $paystatsum['sumegold'] += $paystatrows[$k]['sumegold'];
    $paystatsum['summoney'] += $paystatrows[$k]['summoney'];
    $k++;
}
foreach ($paystatrows as $k => $v) {
    $paystatrows[$k]['countpercent'] = round($paystatrows[$k]['paycount'] * 100 / $countmax, 2);
}
$jieqiTpl->assign_by_ref('paystatrows', $paystatrows);
$jieqiTpl->assign('datefield', $datefield);
$jieqiTpl->assign('countmax', $countmax);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
jieqi_getconfigs('system', 'channels', 'jieqiChannels');
if (empty($jieqiChannels)) {
    $jieqiChannels = array();
}
$jieqiTpl->assign('jieqi_channels', jieqi_funtoarray('jieqi_htmlstr', $jieqiChannels));
$jieqiTpl->assign('jieqi_channelnum', count($jieqiChannels));
$jieqiTpl->assign('jieqi_paytype', jieqi_funtoarray('jieqi_htmlstr', $jieqiPaytype));
$jieqiTpl->assign('paystatsum', jieqi_funtoarray('jieqi_htmlstr', $paystatsum));
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';