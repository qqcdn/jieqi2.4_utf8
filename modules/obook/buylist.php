<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_checklogin();
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/buylist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_getconfigs('obook', 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
if (!empty($_POST['act'])) {
    jieqi_checkpost();
    switch ($_POST['act']) {
        case 'setautobuy':
            $_REQUEST['obuyid'] = intval($_REQUEST['obuyid']);
            if (0 < $_REQUEST['obuyid']) {
                $sql = 'UPDATE ' . jieqi_dbprefix('obook_obuy') . ' SET autobuy = 1 WHERE obuyid = ' . $_REQUEST['obuyid'];
                $query->execute($sql);
            }
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'unsetautobuy':
            $_REQUEST['obuyid'] = intval($_REQUEST['obuyid']);
            if (0 < $_REQUEST['obuyid']) {
                $sql = 'UPDATE ' . jieqi_dbprefix('obook_obuy') . ' SET autobuy = 0 WHERE obuyid = ' . $_REQUEST['obuyid'];
                $query->execute($sql);
            }
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
    }
}
$criteria = new CriteriaCompo(new Criteria('b.userid', $_SESSION['jieqiUserId']));
$criteria->setTables(jieqi_dbprefix('obook_obuy') . ' b LEFT JOIN ' . jieqi_dbprefix('obook_obook') . ' o ON b.obookid=o.obookid');
$criteria->setFields('b.*, o.articleid, o.lastupdate, o.lastchapterid, o.lastchapter, o.chapters');
$criteria->setSort('b.obuyid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$query->queryObjects($criteria);
$obuyrows = array();
$k = 0;
while ($v = $query->getObject()) {
    $obuyrows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('obuyrows', $obuyrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $query->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$pagelink = '';
if (!empty($_REQUEST['oid'])) {
    if (empty($pagelink)) {
        $pagelink .= '?';
    } else {
        $pagelink .= '&';
    }
    $pagelink .= 'oid=' . urlencode($_REQUEST['oid']);
} else {
    if (!empty($_REQUEST['oname'])) {
        if (empty($pagelink)) {
            $pagelink .= '?';
        } else {
            $pagelink .= '&';
        }
        $pagelink .= 'oname=' . urlencode($_REQUEST['oname']);
    }
}
if (empty($pagelink)) {
    $pagelink .= '?page=';
} else {
    $pagelink .= '&page=';
}
$jumppage->setlink($obook_dynamic_url . '/buylist.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';