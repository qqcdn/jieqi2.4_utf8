<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/myreviews.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
$criteria = new CriteriaCompo();
$criteria->setFields('t.*, a.articleid, a.articlename');
$criteria->setTables(jieqi_dbprefix('article_reviews') . ' AS t LEFT JOIN ' . jieqi_dbprefix('article_article') . ' AS a ON t.ownerid = a.articleid');
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
        $criteria->add(new Criteria('a.articlename', $_REQUEST['keyword'], '='));
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
    $k++;
}
$jieqiTpl->assign_by_ref('ptopicrows', $ptopicrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $query->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';