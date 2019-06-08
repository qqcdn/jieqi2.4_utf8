<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
if (empty($_REQUEST['oid']) || !is_numeric($_REQUEST['oid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['oid'] = intval($_REQUEST['oid']);
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
$obook = $obook_handler->get($_REQUEST['oid']);
if (!$obook) {
    jieqi_printfail($jieqiLang['obook']['obook_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$canedit = jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($obook->getVar('authorid') == $tmpvar || $obook->getVar('posterid') == $tmpvar || $obook->getVar('agentid') == $tmpvar)) {
        $canedit = true;
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['obook']['noper_manage_obook']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/paidlog.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$where = ' WHERE 1';
if (isset($_REQUEST['oid']) && is_numeric($_REQUEST['oid'])) {
    $where .= ' AND obookid = ' . intval($_REQUEST['oid']);
} else {
    if (!empty($_REQUEST['keyword'])) {
        $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
        if ($_REQUEST['keytype'] == 1) {
            $where .= ' AND username = \'' . jieqi_dbslashes($_REQUEST['keyword']) . '\'';
        } else {
            $where .= ' AND obookname = \'' . jieqi_dbslashes($_REQUEST['keyword']) . '\'';
        }
    }
}
$sql = 'select * FROM ' . jieqi_dbprefix('obook_paidlog') . $where . ' ORDER BY paidid DESC LIMIT ' . intval($jieqiPset['start']) . ', ' . intval($jieqiPset['rows']);
$sqlcot = 'select count(*) as cot FROM ' . jieqi_dbprefix('obook_paidlog') . $where;
$query->execute($sql);
$paidrows = array();
$k = 0;
while ($row = $query->getRow()) {
    $paidrows[$k] = jieqi_query_rowvars($row);
    $paidrows[$k]['unpaidemoney'] = $paidrows[$k]['sumemoney'] - $paidrows[$k]['paidemoney'];
    $k++;
}
$jieqiTpl->assign_by_ref('paidrows', $paidrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$query->execute($sqlcot);
$row = $query->getRow();
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$pagelink = '';
if (!empty($_REQUEST['keyword'])) {
    if (empty($pagelink)) {
        $pagelink .= '?';
    } else {
        $pagelink .= '&';
    }
    $pagelink .= 'keyword=' . $_REQUEST['keyword'];
    $pagelink .= '&keytype=' . $_REQUEST['keytype'];
}
if (empty($pagelink)) {
    $pagelink .= '?page=';
} else {
    $pagelink .= '&page=';
}
$jumppage->setlink($obook_dynamic_url . '/admin/paidlog.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
$jieqiTpl->assign('authorarea', 1);
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';