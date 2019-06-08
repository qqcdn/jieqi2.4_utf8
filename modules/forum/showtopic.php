<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../global.php';
if (empty($_REQUEST['tid']) || !is_numeric($_REQUEST['tid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['tid'] = intval($_REQUEST['tid']);
jieqi_loadlang('list', JIEQI_MODULE_NAME);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('t.topicid', $_REQUEST['tid']));
$criteria->setTables(jieqi_dbprefix('forum_forumtopics') . ' t LEFT JOIN ' . jieqi_dbprefix('forum_forums') . ' f ON t.ownerid=f.forumid');
$query->queryObjects($criteria);
$topic = $query->getObject();
unset($criteria);
if (!$topic) {
    jieqi_printfail($jieqiLang['forum']['post_not_exists']);
}
include_once $jieqiModules['forum']['path'] . '/include/funforum.php';
jieqi_forum_checkpower($topic, 'authread', false);
$ismaster = jieqi_forum_checkpower($topic, 'authedit', true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$ismanager = jieqi_checkpower($jieqiPower['forum']['manageforum'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$ismaster && $ismanager) {
    $ismaster = $ismanager;
}
if ($topic->getVar('display', 'n') != 0 && !$ismaster) {
    if ($topic->getVar('display', 'n') == 1) {
        jieqi_printfail($jieqiLang['forum']['topic_not_audit']);
    } else {
        jieqi_printfail($jieqiLang['forum']['topic_not_exists']);
    }
}
$jieqi_pagetitle = jieqi_substr($topic->getVar('title'), 0, 56);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/showtopic.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
if ($ismaster) {
    $jieqiTpl->assign('ismaster', 1);
} else {
    $jieqiTpl->assign('ismaster', 0);
}
include_once JIEQI_ROOT_PATH . '/class/users.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
jieqi_getconfigs('system', 'honors');
if (!empty($jieqiModules['badge']['publish']) && is_file($jieqiModules['badge']['path'] . '/include/badgefunction.php')) {
    include_once $jieqiModules['badge']['path'] . '/include/badgefunction.php';
    define('JIEQI_SHOW_BADGE', 1);
} else {
    define('JIEQI_SHOW_BADGE', 0);
}
$jieqiTpl->assign('jieqi_use_badge', JIEQI_SHOW_BADGE);
$jieqiTpl->assign('tid', $_REQUEST['tid']);
$jieqiTpl->assign(jieqi_forum_vars($topic));
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
$jieqiTpl->assign(jieqi_topic_vars($topic));
$criteria = new CriteriaCompo(new Criteria('p.topicid', $_REQUEST['tid']));
if (!$ismaster) {
    $criteria->add(new Criteria('p.display', 0));
}
$criteria->setTables(jieqi_dbprefix('forum_forumposts') . ' p LEFT JOIN ' . jieqi_dbprefix('system_users') . ' u ON p.posterid=u.uid');
$criteria->setSort('p.postid');
$criteria->setOrder('ASC');
$jieqiPset['count'] = $query->getCount($criteria);
if (isset($_REQUEST['page']) && ($_REQUEST['page'] == 'last' || $_REQUEST['page'] == -1)) {
    $_REQUEST['page'] = ceil($jieqiPset['count'] / $jieqiPset['rows']);
    $jieqiPset['page'] = $_REQUEST['page'];
    $jieqiPset['start'] = ($jieqiPset['page'] - 1) * $jieqiPset['rows'];
}
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$query->queryObjects($criteria);
include_once JIEQI_ROOT_PATH . '/lib/text/textconvert.php';
$jieqiTxtcvt = TextConvert::getInstance('TextConvert');
$postrowss = array();
$k = 0;
while ($post = $query->getObject()) {
    $addvars = array('order' => ($jieqiPset['page'] - 1) * $jieqiPset['rows'] + $k + 1);
    $postrows[$k] = jieqi_post_vars($post, $jieqiConfigs['forum'], $addvars, true);
    $k++;
}
$jieqiTpl->assign('postrows', $postrows);
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
$jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
if ($ismaster) {
    include_once $jieqiModules['forum']['path'] . '/class/forums.php';
    $criteria = new CriteriaCompo();
    $criteria->setSort('catid ASC, forumorder');
    $criteria->setOrder('ASC');
    $forums_handler = JieqiForumsHandler::getInstance('JieqiForumsHandler');
    $forums_handler->queryObjects($criteria);
    $forumrows = array();
    $i = 0;
    while ($v = $forums_handler->getObject()) {
        $authpower = array();
        $authpower['groups'] = jieqi_unserialize($v->getVar('authedit', 'n'));
        if (!is_array($authpower['groups'])) {
            $authpower['groups'] = array();
        }
        if ($topic->getVar('forumid') != $v->getVar('forumid') && jieqi_checkpower($authpower, $jieqiUsersStatus, $jieqiUsersGroup, true)) {
            $forumrows[$i]['catid'] = $v->getVar('catid');
            $forumrows[$i]['forumid'] = $v->getVar('forumid');
            $forumrows[$i]['forumname'] = $v->getVar('forumname');
            $i++;
        }
    }
    $jieqiTpl->assign_by_ref('forumrows', $forumrows);
}
if (!isset($_REQUEST['lpage']) || !is_numeric($_REQUEST['lpage'])) {
    $_REQUEST['lpage'] = 1;
}
$jieqiTpl->assign('lpage', urlencode($_REQUEST['lpage']));
$jieqiTpl->assign('page', urlencode($_REQUEST['page']));
if (isset($_REQUEST['postdisplay'])) {
    $jieqiTpl->assign('postdisplay', intval($_REQUEST['postdisplay']));
} else {
    $jieqiTpl->assign('postdisplay', -1);
}
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
jieqi_topic_addviews($_REQUEST['tid'], jieqi_dbprefix('forum_forumtopics'));
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';