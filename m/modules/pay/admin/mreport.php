<?php

function jieqi_pay_getexpmrrow($row)
{
    global $jieqiPaytype;
    if (isset($jieqiPaytype[$row['paytype']])) {
        $row['paytype'] = $jieqiPaytype[$row['paytype']]['shortname'];
    }
    $row = jieqi_query_rowvars($row, 's', 'pay');
    $money_fields = array('summoney');
    foreach ($money_fields as $f) {
        if (isset($row[$f])) {
            $row[$f . '_n'] = $row[$f];
            $row[$f] = floatval(intval($row[$f]) / 100);
        }
    }
    return $row;
}
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
jieqi_loadlang('mreport', JIEQI_MODULE_NAME);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/admin/mreport.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_getconfigs('pay', 'configs');
jieqi_getconfigs('pay', 'paytype');
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
if (!empty($_REQUEST['premonth']) && is_numeric($_REQUEST['premonth'])) {
    $_REQUEST['premonth'] = intval($_REQUEST['premonth']);
    if (1 <= $_REQUEST['premonth']) {
        $tmpt = mktime(0, 0, 0, intval(date('m', JIEQI_NOW_TIME)) - $_REQUEST['premonth'], 1, intval(date('Y', JIEQI_NOW_TIME)));
        $_REQUEST['ryear'] = intval(date('Y', $tmpt));
        $_REQUEST['rmonth'] = intval(date('m', $tmpt));
    }
}
if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'make' && !empty($_REQUEST['ryear']) && !empty($_REQUEST['rmonth'])) {
    $_REQUEST['ryear'] = intval($_REQUEST['ryear']);
    $_REQUEST['rmonth'] = intval($_REQUEST['rmonth']);
    if ($_REQUEST['ryear'] < 2000 || intval(date('Y', JIEQI_NOW_TIME)) < $_REQUEST['ryear'] || $_REQUEST['rmonth'] < 1 || 12 < $_REQUEST['rmonth'] || $_REQUEST['ryear'] == intval(date('Y', JIEQI_NOW_TIME)) && intval(date('m', JIEQI_NOW_TIME)) <= $_REQUEST['rmonth']) {
        jieqi_printfail($jieqiLang['pay']['mreport_allow_premonth']);
    }
    if (empty($_REQUEST['crontab'])) {
        jieqi_checkpost();
    }
    $reportmonth = intval($_REQUEST['ryear'] . sprintf('%02d', $_REQUEST['rmonth']));
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('pay_mreport') . ' WHERE reportmonth = ' . $reportmonth . ' LIMIT 0, 1';
    $query->execute($sql);
    $row = $query->getRow();
    if (is_array($row)) {
        if (!empty($_REQUEST['forcemake'])) {
            $sql = 'DELETE FROM ' . jieqi_dbprefix('pay_mreport') . ' WHERE reportmonth = ' . $reportmonth;
            $query->execute($sql);
        } else {
            jieqi_printfail(sprintf($jieqiLang['pay']['mreport_already_make'], $reportmonth));
        }
    }
    @ignore_user_abort(true);
    @set_time_limit(3600);
    @session_write_close();
    @ini_set('memory_limit', '1024M');
    $starttime = mktime(0, 0, 0, $_REQUEST['rmonth'], 1, $_REQUEST['ryear']);
    $endtime = mktime(0, 0, 0, $_REQUEST['rmonth'] + 1, 1, $_REQUEST['ryear']);
    $reportrows = array();
    $sql = 'SELECT siteid, paytype, moneytype, egoldtype, SUM(money) AS summoney, SUM(egold) AS sumegold, count(*) AS paycount FROM ' . jieqi_dbprefix('pay_paylog') . ' WHERE payflag > 0 AND buytime >= ' . $starttime . ' AND buytime < ' . $endtime . ' GROUP BY paytype';
    $query->execute($sql);
    $k = 0;
    while ($row = $query->getRow()) {
        $reportrows[$k] = $row;
        $reportrows[$k]['reportmonth'] = $reportmonth;
        $reportrows[$k]['operatorid'] = $_SESSION['jieqiUserId'];
        $reportrows[$k]['operator'] = $_SESSION['jieqiUserName'];
        $k++;
    }
    foreach ($reportrows as $k => $v) {
        $fieldrows = $v;
        $fieldrows['addtime'] = JIEQI_NOW_TIME;
        $fieldrows['reportmonth'] = $reportmonth;
        if (!isset($fieldrows['summoney'])) {
            $fieldrows['summoney'] = 0;
        }
        if (!isset($fieldrows['sumegold'])) {
            $fieldrows['sumegold'] = 0;
        }
        $sql = $query->makeupsql(jieqi_dbprefix('pay_mreport'), $fieldrows, 'INSERT');
        $query->execute($sql);
    }
    if (!empty($_REQUEST['crontab'])) {
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['pay']['mreport_make_success'] . '<script type="text/javascript"> setTimeout("if(navigator.userAgent.indexOf(\'Firefox\')==-1){window.opener=null;window.open(\'\',\'_self\');window.close();}else{var opened=window.open(\'about:blank\',\'_self\');opened.close();}", 3000); </script>');
    } else {
        jieqi_jumppage($jieqiModules['pay']['url'] . '/admin/mreport.php', LANG_DO_SUCCESS, $jieqiLang['pay']['mreport_make_success']);
        exit;
    }
}
$slimit = '1';
if (!isset($_REQUEST['paytype'])) {
    $_REQUEST['paytype'] = '';
}
if (!isset($_REQUEST['reportmonth'])) {
    $_REQUEST['reportmonth'] = 0;
}
if (!isset($_REQUEST['startmonth'])) {
    $_REQUEST['startmonth'] = 0;
}
if (!isset($_REQUEST['endmonth'])) {
    $_REQUEST['endmonth'] = 0;
}
if (!isset($_REQUEST['format'])) {
    $_REQUEST['format'] = '';
}
if (!empty($_REQUEST['paytype'])) {
    $slimit .= ' AND paytype = \'' . jieqi_dbslashes($_REQUEST['paytype']) . '\'';
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
if (!empty($_REQUEST['format'])) {
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('pay_mreport') . ' WHERE ' . $slimit . ' ORDER BY reportmonth DESC';
    $res = $query->execute($sql);
    jieqi_getconfigs('pay', 'exportmr', 'jieqiExport');
    include_once JIEQI_ROOT_PATH . '/include/funexport.php';
    if (!isset($_REQUEST['format'])) {
        $_REQUEST['format'] = 'exceltxt';
    }
    $params = array('res' => $res, 'format' => $_REQUEST['format'], 'fields' => $jieqiExport['mreport'], 'filename' => 'mreport_' . date('Ymd') . '.xls', 'funrow' => 'jieqi_pay_getexpmrrow');
    $ret = jieqi_system_exportfile($params);
    if ($ret === false) {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
} else {
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('pay_mreport') . ' WHERE ' . $slimit . ' ORDER BY reportmonth DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
    $res = $query->execute($sql);
    $mreportrows = array();
    $k = 0;
    while ($row = $query->getRow()) {
        if (isset($jieqiPaytype[$row['paytype']])) {
            $row['paytype'] = $jieqiPaytype[$row['paytype']]['shortname'];
        }
        $mreportrows[$k] = jieqi_query_rowvars($row, 's', 'pay');
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
    include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
    $sql = 'SELECT count(*) AS cot, sum(sumegold) as sumegold, sum(summoney) as summoney FROM ' . jieqi_dbprefix('pay_mreport') . ' WHERE ' . $slimit;
    $query->execute($sql);
    $row = $query->getRow();
    $jieqiTpl->assign('mreportstat', jieqi_funtoarray('jieqi_htmlstr', $row));
    $jieqiPset['count'] = intval($row['cot']);
    $jumppage = new JieqiPage($jieqiPset);
    $jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
    $paytyperows = array();
    $i = 0;
    foreach ($jieqiPaytype as $k => $v) {
        if (0 < $v['publish']) {
            $paytyperows[$i] = $v;
            $paytyperows[$i]['paytype'] = $k;
            $i++;
        }
    }
    $jieqiTpl->assign('paytyperows', jieqi_funtoarray('jieqi_htmlstr', $paytyperows));
    $jieqiTpl->setCaching(0);
    include_once JIEQI_ROOT_PATH . '/admin/footer.php';
}