<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/myptopics.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
$criteria = new CriteriaCompo();
$criteria->setFields('t.*,u.uname,u.name');
$criteria->setTables(jieqi_dbprefix('system_ptopics') . ' AS t LEFT JOIN ' . jieqi_dbprefix('system_users') . ' AS u ON t.ownerid=u.uid');
$criteria->add(new Criteria('t.posterid', intval($_SESSION['jieqiUserId']), '='));
if (isset($_REQUEST['display']) && is_numeric($_REQUEST['display'])) {
    $criteria->add(new Criteria('t.display', intval($_REQUEST['display'])));
    $jieqiTpl->assign('display', intval($_REQUEST['display']));
} else {
    $jieqiTpl->assign('display', '');
}
if (!empty($_REQUEST['keyword'])) {
    $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
    if ($_REQUEST['keytype'] == 2) {
        $criteria->add(new Criteria('t.title', '%' . $_REQUEST['keyword'] . '%', 'LIKE'));
    } else {
        $criteria->add(new Criteria('u.name', $_REQUEST['keyword'], '='));
    }
}
if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'good') {
    $jieqiTpl->assign('type', 'good');
    $criteria->add(new Criteria('t.isgood', 1));
} else {
    $_REQUEST['type'] = 'all';
    $jieqiTpl->assign('type', 'all');
}
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
$criteria->setSort('t.topicid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$query->queryObjects($criteria);
$ptopicrows = array();
$k = 0;
while ($v = $query->getObject()) {
    $ptopicrows[$k] = jieqi_topic_vars($v);
    $ptopicrows[$k]['ownername'] = strlen($v->getVar('name')) == 0 ? $v->getVar('uname') : $v->getVar('name');
    $k++;
}
$jieqiTpl->assign_by_ref('ptopicrows', $ptopicrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $query->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';