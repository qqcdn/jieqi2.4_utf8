<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/myreplies.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
$criteria = new CriteriaCompo();
$criteria->setTables(jieqi_dbprefix('article_reviews') . ' t RIGHT JOIN ' . jieqi_dbprefix('article_replies') . ' p ON t.topicid=p.topicid');
$criteria->add(new Criteria('p.posterid', intval($_SESSION['jieqiUserId']), '='));
if (isset($_REQUEST['display']) && is_numeric($_REQUEST['display'])) {
    $criteria->add(new Criteria('p.display', intval($_REQUEST['display'])));
    $jieqiTpl->assign('display', intval($_REQUEST['display']));
} else {
    $jieqiTpl->assign('display', '');
}
if (!empty($_REQUEST['keyword'])) {
    $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
    if ($_REQUEST['keytype'] == 1) {
        $criteria->add(new Criteria('p.poster', $_REQUEST['keyword'], '='));
    }
}
$criteria->setSort('p.postid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$query->queryObjects($criteria);
$postrows = array();
$k = 0;
while ($v = $query->getObject()) {
    $postrows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('postrows', $postrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $query->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';