<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/admin/monthlylog.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$slimit = '1';
if (!empty($_REQUEST['userid'])) {
    $slimit .= ' AND userid = ' . intval($_REQUEST['userid']);
}
if (!empty($_REQUEST['username'])) {
    $slimit .= ' AND username = \'' . jieqi_dbslashes($_REQUEST['username']) . '\'';
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
$sql = 'SELECT * FROM ' . jieqi_dbprefix('obook_monthlylog') . ' WHERE ' . $slimit . ' ORDER BY logid DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
$query->execute($sql);
$monthlylogrows = array();
$k = 0;
while ($row = $query->getRow()) {
    $monthlylogrows[$k] = jieqi_query_rowvars($row);
    $k++;
}
$jieqiTpl->assign_by_ref('monthlylogrows', $monthlylogrows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$sql = 'SELECT count(*) AS cot, sum(month) as summonth, sum(egold) as sumegold, sum(money) as summoney FROM ' . jieqi_dbprefix('obook_monthlylog') . ' WHERE ' . $slimit;
$query->execute($sql);
$row = $query->getRow();
$jieqiTpl->assign('monthlylogstat', jieqi_funtoarray('jieqi_htmlstr', $row));
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';