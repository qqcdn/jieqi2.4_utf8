<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['viewbuylog'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/admin/buylog.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_getconfigs('obook', 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
if (!empty($_REQUEST['uname'])) {
    $jieqiTpl->assign('uname', jieqi_htmlchars($_REQUEST['uname'], ENT_QUOTES));
}
if (!empty($_REQUEST['oname'])) {
    $jieqiTpl->assign('oname', jieqi_htmlchars($_REQUEST['oname'], ENT_QUOTES));
}
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$slimit = '1';
if (!empty($_REQUEST['uid'])) {
    $slimit .= ' AND accountid = ' . intval($_REQUEST['uid']);
} else {
    if (!empty($_REQUEST['uname'])) {
        $slimit .= ' AND account = \'' . jieqi_dbslashes($_REQUEST['uname']) . '\'';
    }
}
if (!empty($_REQUEST['oid'])) {
    $slimit .= ' AND obookid = ' . intval($_REQUEST['oid']);
} else {
    if (!empty($_REQUEST['oname'])) {
        $slimit .= ' AND obookname = \'' . jieqi_dbslashes($_REQUEST['oname']) . '\'';
    }
}
if (!empty($_REQUEST['datestart'])) {
    $tmpvar = @strtotime($_REQUEST['datestart']);
    if (0 < $tmpvar) {
        $slimit .= ' AND buytime >= ' . intval($tmpvar);
    }
}
if (!empty($_REQUEST['dateend'])) {
    $tmpvar = @strtotime($_REQUEST['dateend']);
    if (0 < $tmpvar) {
        $slimit .= ' AND buytime < ' . (intval($tmpvar) + 86400);
    }
}
$sql = 'SELECT * FROM ' . jieqi_dbprefix('obook_osale') . ' WHERE ' . $slimit . ' ORDER BY osaleid DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
$query->execute($sql);
$osalerows = array();
$k = 0;
while ($row = $query->getRow()) {
    $osalerows[$k] = jieqi_query_rowvars($row);
    $k++;
}
$jieqiTpl->assign_by_ref('osalerows', $osalerows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$sql = 'SELECT count(*) AS cot, sum(saleprice) as sumsaleprice FROM ' . jieqi_dbprefix('obook_osale') . ' WHERE ' . $slimit;
$query->execute($sql);
$row = $query->getRow();
$jieqiTpl->assign('osalestat', jieqi_funtoarray('jieqi_htmlstr', $row));
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';