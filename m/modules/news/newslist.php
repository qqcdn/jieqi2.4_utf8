<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs', 'jieqiConfigs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
jieqi_loadlang('news', JIEQI_MODULE_NAME);
include_once $jieqiModules['news']['path'] . '/class/topic.php';
$topic_handler = JieqiNewstopicHandler::getInstance('JieqiNewstopicHandler');
$jieqiTset['jieqi_contents_template'] = $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/templates/newslist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
if (empty($_REQUEST['sortid']) || !isset($jieqiSort['news'][$_REQUEST['sortid']])) {
    $_REQUEST['sortid'] = 0;
}
if (empty($_REQUEST['order']) || !in_array($_REQUEST['order'], array('topicid', 'addtime', 'uptime', 'views', 'marknum', 'topnum', 'downnum', 'scorenum', 'sumscore', 'reviews', 'replies'))) {
    $_REQUEST['order'] = '';
}
include_once JIEQI_ROOT_PATH . '/include/funsort.php';
$sortroutes = jieqi_sort_routes($jieqiSort['news'], $_REQUEST['sortid']);
$jieqiTpl->assign_by_ref('sortroutes', $sortroutes);
$criteria = new CriteriaCompo();
$sortname = '';
if (!empty($_REQUEST['sortid'])) {
    $criteria->add(new Criteria('sortid', '(' . jieqi_sort_childs($jieqiSort['news'], $_REQUEST['sortid']) . ')', 'IN'));
    $sortname = $jieqiSort['news'][$_REQUEST['sortid']]['sortname'];
}
$jieqiTpl->assign('sortname', $sortname);
$criteria->add(new Criteria('display', 0, '='));
if ($_REQUEST['order'] == '') {
    $criteria->setSort('topicid');
} else {
    $criteria->setSort($_REQUEST['order']);
}
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$topic_handler->queryObjects($criteria);
$newsrows = array();
$k = 0;
include_once $jieqiModules['news']['path'] . '/include/funnews.php';
while ($v = $topic_handler->getObject()) {
    $newsrows[$k] = jieqi_news_vars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('newsrows', $newsrows);
$jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['news']));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $topic_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink(jieqi_geturl('news', 'newslist', 0, $_REQUEST['sortid'], $_REQUEST['order']));
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';