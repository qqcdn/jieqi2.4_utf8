<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower[JIEQI_MODULE_NAME]['manageattach'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_loadlang('attachment', JIEQI_MODULE_NAME);
$jieqiTset['jieqi_contents_template'] = $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/templates/admin/attachlist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
include_once $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/class/attachment.php';
if (isset($_POST['act']) && $_POST['act'] == 'delete' && is_array($_POST['checkid']) && 0 < count($_POST['checkid'])) {
    jieqi_checkpost();
    $attach_handler = JieqiNewsattachHandler::getInstance('JieqiNewsattachHandler');
    foreach ($_POST['checkid'] as $v) {
        if (!is_numeric($v)) {
            jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['attach_id_error']);
        }
        $p = JIEQI_ROOT_PATH . $attach_handler->JieqiNewsattachPath($v);
        if (file_exists($p)) {
            jieqi_delfile($p);
        }
        $criteria = new CriteriaCompo(new Criteria('attachid', $v));
        if (!$attach_handler->delete($criteria)) {
            jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['attach_del_failure']);
        }
    }
    jieqi_jumppage($jieqiModules[JIEQI_MODULE_NAME]['url'] . '/admin/attachlist.php', LANG_DO_SUCCESS, $jieqiLang[JIEQI_MODULE_NAME]['attach_del_success']);
}
$attach_handler = JieqiNewsattachHandler::getInstance('JieqiNewsattachHandler');
$criteria = new CriteriaCompo();
$criteria->setSort('attachid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
if (!($result = $attach_handler->queryObjects($criteria))) {
    jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['attach_query_error']);
}
$attachrows = array();
$i = 0;
while ($v = $attach_handler->getObject()) {
    $attachrows[$i] = jieqi_query_rowvars($v);
    $i++;
}
$jieqiTpl->assign('page_head_name', $jieqiLang[JIEQI_MODULE_NAME]['attach_manage']);
$jieqiTpl->assign('attachrows', $attachrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $attach_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';