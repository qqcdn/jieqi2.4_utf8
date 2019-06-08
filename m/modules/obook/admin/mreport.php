<?php

function jieqi_obook_getexpmrrow($row)
{
    $row = jieqi_query_rowvars($row, 's', 'article');
    $row['authorprepay'] = $row['authormoney'] - $row['authordeposit'];
    $row['posterprepay'] = $row['postermoney'] - $row['posterdeposit'];
    $row['agentprepay'] = $row['agentmoney'] - $row['agentdeposit'];
    $row['masterprepay'] = $row['mastermoney'] - $row['masterdeposit'];
    $row['siteprepay'] = $row['sitemoney'] - $row['sitedeposit'];
    $row['otherprepay'] = $row['othermoney'] - $row['otherdeposit'];
    $money_fields = array('sumemoney', 'authormoney', 'authordeposit', 'postermoney', 'posterdeposit', 'agentmoney', 'agentdeposit', 'mastermoney', 'masterdeposit', 'sitemoney', 'sitedeposit', 'othermoney', 'otherdeposit', 'summoney', 'paidmoney', 'authorprepay', 'posterprepay', 'agentprepay', 'masterprepay', 'siteprepay', 'otherprepay');
    foreach ($money_fields as $f) {
        if (isset($row[$f])) {
            $row[$f . '_y'] = floatval(intval($row[$f]) / 100);
        }
    }
    return $row;
}
define('JIEQI_MODULE_NAME', 'obook');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('mreport', JIEQI_MODULE_NAME);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/admin/mreport.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_getconfigs('obook', 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
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
        jieqi_printfail($jieqiLang['obook']['mreport_allow_premonth']);
    }
    if (empty($_REQUEST['crontab'])) {
        jieqi_checkpost();
    }
    $reportmonth = intval($_REQUEST['ryear'] . sprintf('%02d', $_REQUEST['rmonth']));
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('obook_mreport') . ' WHERE reportmonth = ' . $reportmonth . ' LIMIT 0, 1';
    $query->execute($sql);
    $row = $query->getRow();
    if (is_array($row)) {
        if (!empty($_REQUEST['forcemake'])) {
            $sql = 'DELETE FROM ' . jieqi_dbprefix('obook_mreport') . ' WHERE reportmonth = ' . $reportmonth;
            $query->execute($sql);
        } else {
            jieqi_printfail(sprintf($jieqiLang['obook']['mreport_already_make'], $reportmonth));
        }
    }
    @ignore_user_abort(true);
    @set_time_limit(3600);
    @session_write_close();
    @ini_set('memory_limit', '1024M');
    $starttime = mktime(0, 0, 0, $_REQUEST['rmonth'], 1, $_REQUEST['ryear']);
    $endtime = mktime(0, 0, 0, $_REQUEST['rmonth'] + 1, 1, $_REQUEST['ryear']);
    $reportrows = array();
    $aids = array();
    $sql = 'SELECT articleid, SUM(saleprice) AS sumegold FROM ' . jieqi_dbprefix('obook_osale') . ' WHERE buytime >= ' . $starttime . ' AND buytime < ' . $endtime . ' GROUP BY articleid';
    $query->execute($sql);
    while ($row = $query->getRow()) {
        $reportrows[$row['articleid']] = $row;
        $aids[] = intval($row['articleid']);
    }
    $sql = 'SELECT articleid, SUM(actnum) AS sumtip FROM ' . jieqi_dbprefix('article_actlog') . ' WHERE actname = \'tip\' AND addtime >= ' . $starttime . ' AND addtime < ' . $endtime . ' GROUP BY articleid';
    $query->execute($sql);
    while ($row = $query->getRow()) {
        if (isset($reportrows[$row['articleid']])) {
            $reportrows[$row['articleid']]['sumtip'] = $row['sumtip'];
        } else {
            $reportrows[$row['articleid']] = $row;
            $aids[] = intval($row['articleid']);
        }
    }
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE articleid IN (' . implode(',', $aids) . ')';
    $query->execute($sql);
    while ($row = $query->getRow()) {
        if (isset($reportrows[$row['articleid']])) {
            $reportrows[$row['articleid']]['obookname'] = $row['articlename'];
            $reportrows[$row['articleid']]['authorid'] = $row['authorid'];
            $reportrows[$row['articleid']]['author'] = $row['author'];
            $reportrows[$row['articleid']]['posterid'] = $row['posterid'];
            $reportrows[$row['articleid']]['poster'] = $row['poster'];
            $reportrows[$row['articleid']]['agentid'] = $row['agentid'];
            $reportrows[$row['articleid']]['agent'] = $row['agent'];
            $reportrows[$row['articleid']]['masterid'] = $row['masterid'];
            $reportrows[$row['articleid']]['master'] = $row['master'];
            $reportrows[$row['articleid']]['sortid'] = $row['sortid'];
            $reportrows[$row['articleid']]['typeid'] = $row['typeid'];
            $_REQUEST['ryear'] = intval($_REQUEST['ryear']);
            $_REQUEST['rmonth'] = intval($_REQUEST['rmonth']);
            $rmonths = $_REQUEST['ryear'] * 12 + $_REQUEST['rmonth'];
            $tmonths = intval(date('Y', $row['lastupdate'])) * 12 + intval(date('m', $row['lastupdate']));
            $reportrows[$row['articleid']]['prewords'] = $tmonths == $rmonths ? $row['monthwords'] : $tmonths - $rmonths == 1 ? $row['prewords'] : 0;
            $reportrows[$row['articleid']]['preupds'] = $tmonths == $rmonths ? $row['monthupds'] : $tmonths - $rmonths == 1 ? $row['preupds'] : 0;
            $reportrows[$row['articleid']]['preupdt'] = $tmonths == $rmonths ? $row['monthupdt'] : $tmonths - $rmonths == 1 ? $row['preupdt'] : 0;
            $tmonths = intval(date('Y', $row['lastvisit'])) * 12 + intval(date('m', $row['lastvisit']));
            $reportrows[$row['articleid']]['previsit'] = $tmonths == $rmonths ? $row['monthvisit'] : $tmonths - $rmonths == 1 ? $row['previsit'] : 0;
            $tmonths = intval(date('Y', $row['lastvote'])) * 12 + intval(date('m', $row['lastvote']));
            $reportrows[$row['articleid']]['prevote'] = $tmonths == $rmonths ? $row['monthvote'] : $tmonths - $rmonths == 1 ? $row['prevote'] : 0;
            $tmonths = intval(date('Y', $row['lastdown'])) * 12 + intval(date('m', $row['lastdown']));
            $reportrows[$row['articleid']]['predown'] = $tmonths == $rmonths ? $row['monthdown'] : $tmonths - $rmonths == 1 ? $row['predown'] : 0;
            $tmonths = intval(date('Y', $row['lastflower'])) * 12 + intval(date('m', $row['lastflower']));
            $reportrows[$row['articleid']]['preflower'] = $tmonths == $rmonths ? $row['monthflower'] : $tmonths - $rmonths == 1 ? $row['preflower'] : 0;
            $tmonths = intval(date('Y', $row['lastegg'])) * 12 + intval(date('m', $row['lastegg']));
            $reportrows[$row['articleid']]['preegg'] = $tmonths == $rmonths ? $row['monthegg'] : $tmonths - $rmonths == 1 ? $row['preegg'] : 0;
            $tmonths = intval(date('Y', $row['lastvipvote'])) * 12 + intval(date('m', $row['lastvipvote']));
            $reportrows[$row['articleid']]['previpvote'] = $tmonths == $rmonths ? $row['monthvipvote'] : $tmonths - $rmonths == 1 ? $row['previpvote'] : 0;
            $reportrows[$row['articleid']]['fullflag'] = $row['fullflag'];
            $reportrows[$row['articleid']]['issign'] = $row['issign'];
            $reportrows[$row['articleid']]['isvip'] = $row['isvip'];
            $reportrows[$row['articleid']]['operatorid'] = $_SESSION['jieqiUserId'];
            $reportrows[$row['articleid']]['operator'] = $_SESSION['jieqiUserName'];
        }
    }
    jieqi_getconfigs('article', 'rule');
    foreach ($reportrows as $k => $v) {
        $fieldrows = $v;
        $fieldrows['addtime'] = JIEQI_NOW_TIME;
        $fieldrows['reportmonth'] = $reportmonth;
        $fieldrows['obookid'] = $fieldrows['articleid'];
        if (!isset($fieldrows['obookname'])) {
            $fieldrows['obookname'] = 'ID:' . $fieldrows['articleid'];
        }
        if (!isset($fieldrows['sumegold'])) {
            $fieldrows['sumegold'] = 0;
        }
        if (!isset($fieldrows['sumesilver'])) {
            $fieldrows['sumesilver'] = 0;
        }
        if (!isset($fieldrows['sumtip'])) {
            $fieldrows['sumtip'] = 0;
        }
        if (!isset($fieldrows['sumhurry'])) {
            $fieldrows['sumhurry'] = 0;
        }
        if (!isset($fieldrows['sumbesp'])) {
            $fieldrows['sumbesp'] = 0;
        }
        if (!isset($fieldrows['sumaward'])) {
            $fieldrows['sumaward'] = 0;
        }
        if (!isset($fieldrows['sumagent'])) {
            $fieldrows['sumagent'] = 0;
        }
        if (!isset($fieldrows['sumgift'])) {
            $fieldrows['sumgift'] = 0;
        }
        if (!isset($fieldrows['sumother'])) {
            $fieldrows['sumother'] = 0;
        }
        $fieldrows['sumemoney'] = $fieldrows['sumegold'] + $fieldrows['sumesilver'] + $fieldrows['sumtip'] + $fieldrows['sumhurry'] + $fieldrows['sumbesp'] + $fieldrows['sumaward'] + $fieldrows['sumagent'] + $fieldrows['sumgift'] + $fieldrows['sumother'];
        $fieldrows['summoney'] = $fieldrows['sumemoney'];
        if (function_exists('jieqi_rule_article_mreportdivide')) {
            jieqi_rule_article_mreportdivide($fieldrows);
        }
        $sql = $query->makeupsql(jieqi_dbprefix('obook_mreport'), $fieldrows, 'INSERT');
        $query->execute($sql);
    }
    if (!empty($_REQUEST['crontab'])) {
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['obook']['mreport_make_success'] . '<script type="text/javascript"> setTimeout("if(navigator.userAgent.indexOf(\'Firefox\')==-1){window.opener=null;window.open(\'\',\'_self\');window.close();}else{var opened=window.open(\'about:blank\',\'_self\');opened.close();}", 3000); </script>');
    } else {
        jieqi_jumppage($jieqiModules['obook']['url'] . '/admin/mreport.php', LANG_DO_SUCCESS, $jieqiLang['obook']['mreport_make_success']);
        exit;
    }
}
if (!empty($_REQUEST['act']) && in_array($_REQUEST['act'], array('paid', 'unpay'))) {
    $mrids = array();
    foreach ($_REQUEST['checkid'] as $v) {
        if (is_numeric($v)) {
            $mrids[] = intval($v);
        }
    }
    if (0 < count($mrids) && is_array($_REQUEST['paidtype']) && 0 < count($_REQUEST['paidtype'])) {
        $upfields = array();
        $tmpvar = 0;
        if (in_array('authormoney', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 3;
        }
        if (in_array('authordeposit', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 2;
        }
        if (in_array('authorprepay', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 1;
        }
        if ($tmpvar != 0) {
            $upfields['authorpaid'] = $tmpvar;
        }
        $tmpvar = 0;
        if (in_array('postermoney', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 3;
        }
        if (in_array('posterdeposit', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 2;
        }
        if (in_array('posterprepay', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 1;
        }
        if ($tmpvar != 0) {
            $upfields['posterpaid'] = $tmpvar;
        }
        $tmpvar = 0;
        if (in_array('agentmoney', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 3;
        }
        if (in_array('agentdeposit', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 2;
        }
        if (in_array('agentprepay', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 1;
        }
        if ($tmpvar != 0) {
            $upfields['agentpaid'] = $tmpvar;
        }
        $tmpvar = 0;
        if (in_array('mastermoney', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 3;
        }
        if (in_array('masterdeposit', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 2;
        }
        if (in_array('masterprepay', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 1;
        }
        if ($tmpvar != 0) {
            $upfields['masterpaid'] = $tmpvar;
        }
        $tmpvar = 0;
        if (in_array('sitemoney', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 3;
        }
        if (in_array('sitedeposit', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 2;
        }
        if (in_array('siteprepay', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 1;
        }
        if ($tmpvar != 0) {
            $upfields['sitepaid'] = $tmpvar;
        }
        $tmpvar = 0;
        if (in_array('othermoney', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 3;
        }
        if (in_array('otherdeposit', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 2;
        }
        if (in_array('otherprepay', $_REQUEST['paidtype'])) {
            $tmpvar = $tmpvar | 1;
        }
        if ($tmpvar != 0) {
            $upfields['otherpaid'] = $tmpvar;
        }
        if (0 < count($upfields)) {
            $sql = '';
            if ($_REQUEST['act'] == 'paid') {
                foreach ($upfields as $k => $v) {
                    if ($sql != '') {
                        $sql .= ' ,' . $k . ' = ' . $k . ' | ' . $v;
                    } else {
                        $sql .= ' ' . $k . ' = ' . $k . ' | ' . $v;
                    }
                }
            } else {
                if ($_REQUEST['act'] == 'unpay') {
                    foreach ($upfields as $k => $v) {
                        $v = 3 ^ $v;
                        if ($sql != '') {
                            $sql .= ' ,' . $k . ' = ' . $k . ' & ' . $v;
                        } else {
                            $sql .= ' ' . $k . ' = ' . $k . ' & ' . $v;
                        }
                    }
                }
            }
            $sql = 'UPDATE ' . jieqi_dbprefix('obook_mreport') . ' SET' . $sql . ' WHERE mreportid IN (' . implode(', ', $mrids) . ')';
            $query->execute($sql);
        }
    }
}
if (2 <= floatval(JIEQI_VERSION)) {
    jieqi_getconfigs('system', 'sites', 'jieqiSites');
    $customsites = array();
    foreach ($jieqiSites as $k => $v) {
        if (!empty($v['custom'])) {
            $customsites[$k] = $v;
        }
    }
    $jieqiTpl->assign('customsites', jieqi_funtoarray('jieqi_htmlstr', $customsites));
    $jieqiTpl->assign('customsitenum', count($customsites));
    $jieqiTpl->assign('jieqisites', jieqi_funtoarray('jieqi_htmlstr', $jieqiSites));
}
$slimit = '1';
if (!empty($_REQUEST['siteid'])) {
    switch ($_REQUEST['siteid']) {
        case -1:
            $slimit .= ' AND siteid = 0';
            break;
        case -2:
            $slimit .= ' AND siteid > 0';
            break;
        default:
            $slimit .= ' AND siteid = ' . intval($_REQUEST['siteid']);
            break;
    }
}
if (!empty($_REQUEST['authorid'])) {
    $slimit .= ' AND authorid = ' . intval($_REQUEST['authorid']);
} else {
    if (!empty($_REQUEST['author'])) {
        $slimit .= ' AND author = \'' . jieqi_dbslashes($_REQUEST['author']) . '\'';
    }
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
if (!empty($_REQUEST['keyword'])) {
    switch ($_REQUEST['keytype']) {
        case 'authorid':
        case 'obookid':
        case 'agentid':
        case 'masterid':
            $slimit .= ' AND ' . $_REQUEST['keytype'] . ' = ' . intval($_REQUEST['keyword']);
            break;
        case 'author':
        case 'obookname':
        case 'agent':
        case 'master':
            $slimit .= ' AND ' . $_REQUEST['keytype'] . ' = \'' . jieqi_dbslashes($_REQUEST['keyword']) . '\'';
            break;
    }
}
if (!empty($_REQUEST['format'])) {
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('obook_mreport') . ' WHERE ' . $slimit . ' ORDER BY reportmonth DESC';
    $res = $query->execute($sql);
    jieqi_getconfigs('obook', 'exportmr', 'jieqiExport');
    include_once JIEQI_ROOT_PATH . '/include/funexport.php';
    if (!isset($_REQUEST['format'])) {
        $_REQUEST['format'] = 'exceltxt';
    }
    $params = array('res' => $res, 'format' => $_REQUEST['format'], 'fields' => $jieqiExport['mreport'], 'filename' => 'mreport_' . date('Ymd') . '.xls', 'funrow' => 'jieqi_obook_getexpmrrow');
    $ret = jieqi_system_exportfile($params);
    if ($ret === false) {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
} else {
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('obook_mreport') . ' WHERE ' . $slimit . ' ORDER BY reportmonth DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
    $res = $query->execute($sql);
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
    $jieqiTpl->setCaching(0);
    include_once JIEQI_ROOT_PATH . '/admin/footer.php';
}