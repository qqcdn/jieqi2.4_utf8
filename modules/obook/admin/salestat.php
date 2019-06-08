<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('system', 'sites', 'jieqiSites');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/admin/salestat.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
jieqi_getconfigs('article', 'sort');
jieqi_getconfigs('system', 'sites', 'jieqiSites');
if (empty($_REQUEST['sortid'])) {
    $_REQUEST['sortid'] = 0;
}
if (empty($_REQUEST['sortid']) && !empty($_REQUEST['class'])) {
    $_REQUEST['sortid'] = $_REQUEST['class'];
}
$_REQUEST['sortid'] = intval($_REQUEST['sortid']);
$criteria = new CriteriaCompo();
if (2 <= floatval(JIEQI_VERSION)) {
    $criteria->setTables(jieqi_dbprefix('obook_obook') . ' b LEFT JOIN ' . jieqi_dbprefix('system_persons') . ' p ON b.authorid=p.uid');
} else {
    $criteria->setTables(jieqi_dbprefix('obook_obook'));
}
if (!empty($_REQUEST['keyword'])) {
    $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
    if ($_REQUEST['keytype'] == 1) {
        $criteria->add(new Criteria('author', $_REQUEST['keyword'], '='));
    } else {
        if ($_REQUEST['keytype'] == 2) {
            $criteria->add(new Criteria('poster', $_REQUEST['keyword'], '='));
        } else {
            $criteria->add(new Criteria('obookname', $_REQUEST['keyword'], '='));
        }
    }
}
if (!empty($_REQUEST['sortid'])) {
    $criteria->add(new Criteria('sortid', $_REQUEST['sortid'], '='));
    $obooktitle = $jieqiSort['article'][$_REQUEST['sortid']]['caption'];
}
if (isset($_REQUEST['siteid']) && is_numeric($_REQUEST['siteid'])) {
    $criteria->add(new Criteria('siteid', intval($_REQUEST['siteid']), '='));
    $obooktitle = jieqi_htmlstr($jieqiSites[$_REQUEST['siteid']]['name']);
}
$jieqiTpl->assign('obooktitle', $obooktitle);
$jieqiTpl->assign('url_salestat', $obook_dynamic_url . '/admin/salestat.php');
$jieqiTpl->assign('jieqisites', jieqi_funtoarray('jieqi_htmlstr', $jieqiSites));
$criteria->setSort('b.obookid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$query->queryObjects($criteria);
$obookrows = array();
$k = 0;
include_once $jieqiModules['obook']['path'] . '/include/funobook.php';
while ($v = $query->getObject()) {
    $obookrows[$k] = jieqi_obook_obookvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('obookrows', $obookrows);
$jieqiTpl->assign('jieqisites', jieqi_funtoarray('jieqi_htmlstr', $jieqiSites));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $query->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';