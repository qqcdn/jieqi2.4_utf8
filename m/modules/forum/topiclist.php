<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../global.php';
if (empty($_REQUEST['fid']) || !is_numeric($_REQUEST['fid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['fid'] = intval($_REQUEST['fid']);
$_REQUEST['oid'] = $_REQUEST['fid'];
jieqi_loadlang('list', JIEQI_MODULE_NAME);
include_once $jieqiModules['forum']['path'] . '/class/forums.php';
$forums_handler = JieqiForumsHandler::getInstance('JieqiForumsHandler');
$forum = $forums_handler->get($_REQUEST['oid']);
if (!$forum) {
    jieqi_printfail($jieqiLang['forum']['forum_not_exists']);
}
include_once $jieqiModules['forum']['path'] . '/include/funforum.php';
jieqi_forum_checkpower($forum, 'authview', false);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/topiclist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$forum_type = intval($forum->getVar('forumtype', 'n'));
$jieqiTpl->assign(jieqi_forum_vars($forum));
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTpl->assign('page', intval($_REQUEST['page']));
if ($_REQUEST['page'] == 1 && $forum_type == 0) {
    jieqi_getcachevars('forum', 'forumtops');
    $jieqiTpl->assign_by_ref('forumtops', $jieqiForumtops);
}
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
$topicrows = array();
$table_topics = jieqi_dbprefix('forum_forumtopics');
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('ownerid', $_REQUEST['oid'], '='));
$criteria->add(new Criteria('display', 0));
if (isset($_REQUEST['isgood'])) {
    $criteria->add(new Criteria('isgood', intval($_REQUEST['isgood'])));
}
$criteria->setSort('istop DESC, replytime');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$criteria->setTables($table_topics);
$query->queryObjects($criteria);
$k = 0;
while ($topic = $query->getObject()) {
    $topicrows[$k] = jieqi_topic_vars($topic);
    $k++;
}
$jieqiTpl->assign_by_ref('topicrows', $topicrows);
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
$jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
$page_rowcount = $query->getCount($criteria);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $page_rowcount;
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';