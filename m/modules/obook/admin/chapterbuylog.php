<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['viewbuylog'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_REQUEST['cid']) || !is_numeric($_REQUEST['cid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['cid'] = intval($_REQUEST['cid']);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/admin/chapterbuylog.html';
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
include_once $jieqiModules['obook']['path'] . '/class/osale.php';
$osale_handler = JieqiOsaleHandler::getInstance('JieqiOsaleHandler');
$criteria = new CriteriaCompo(new Criteria('ochapterid', $_REQUEST['cid']));
$criteria->setSort('osaleid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$osale_handler->queryObjects($criteria);
$osalerows = array();
$k = 0;
while ($v = $osale_handler->getObject()) {
    $osalerows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('osalerows', $osalerows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $osale_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';