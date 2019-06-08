<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['system']['path'] . '/templates/admin/earnlog.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$where = ' WHERE 1';
if (isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid'])) {
    $where .= ' AND userid = ' . intval($_REQUEST['uid']);
} else {
    if (!empty($_REQUEST['keyword'])) {
        $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
        $where .= ' AND username = \'' . jieqi_dbslashes($_REQUEST['keyword']) . '\'';
    }
}
$sql = 'select * FROM ' . jieqi_dbprefix('system_earnlog') . $where . ' ORDER BY logid DESC LIMIT ' . intval($jieqiPset['start']) . ', ' . intval($jieqiPset['rows']);
$sqlcot = 'select count(*) as cot FROM ' . jieqi_dbprefix('system_earnlog') . $where;
$query->execute($sql);
$earnrows = array();
$k = 0;
while ($row = $query->getRow()) {
    $earnrows[$k] = jieqi_query_rowvars($row);
    $k++;
}
$jieqiTpl->assign_by_ref('earnrows', $earnrows);
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
}
if (empty($pagelink)) {
    $pagelink .= '?page=';
} else {
    $pagelink .= '&page=';
}
$jumppage->setlink($system_dynamic_url . '/admin/earnlog.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';