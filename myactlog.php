<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'action');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/myactlog.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$slimit = 'uid = ' . intval($_SESSION['jieqiUserId']);
if (!empty($_REQUEST['aid'])) {
    $slimit .= ' AND tid = ' . intval($_REQUEST['aid']);
}
if (!empty($_REQUEST['aname'])) {
    $slimit .= ' AND tname = \'' . jieqi_dbslashes($_REQUEST['aname']) . '\'';
}
$actname = '';
if (!empty($_REQUEST['act']) && isset($jieqiAction['system'][$_REQUEST['act']])) {
    $actname = jieqi_htmlstr($jieqiAction['system'][$_REQUEST['act']]['acttitle']);
    $slimit .= ' AND actname = \'' . jieqi_dbslashes($_REQUEST['act']) . '\'';
}
$jieqiTpl->assign('actname', $actname);
if (!empty($_REQUEST['datestart'])) {
    $tmpvar = @strtotime($_REQUEST['datestart']);
    if (0 < $tmpvar) {
        $slimit .= ' AND addtime >= ' . intval($tmpvar);
    }
}
if (!empty($_REQUEST['dateend'])) {
    $tmpvar = @strtotime($_REQUEST['dateend']);
    if (0 < $tmpvar) {
        $slimit .= ' AND addtime <= ' . intval($tmpvar);
    }
}
$sql = 'SELECT * FROM ' . jieqi_dbprefix('system_actlog') . ' WHERE ' . $slimit . ' ORDER BY actlogid DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
$query->execute($sql);
$actlogrows = array();
$k = 0;
while ($row = $query->getRow()) {
    $actlogrows[$k] = jieqi_query_rowvars($row);
    $actlogrows[$k]['actname_n'] = $actlogrows[$k]['actname'];
    if (isset($jieqiAction['system'][$actlogrows[$k]['actname_n']])) {
        $actlogrows[$k]['actname'] = jieqi_htmlstr($jieqiAction['system'][$actlogrows[$k]['actname_n']]['acttitle']);
    }
    $k++;
}
$jieqiTpl->assign_by_ref('actlogrows', $actlogrows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
$jieqiTpl->assign('actionrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiAction['system']));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$sql = 'SELECT count(*) AS cot, sum(actnum) as sumactnum FROM ' . jieqi_dbprefix('system_actlog') . ' WHERE ' . $slimit;
$query->execute($sql);
$row = $query->getRow();
$jieqiTpl->assign('actlogstat', jieqi_funtoarray('jieqi_htmlstr', $row));
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';