<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (!isset($_REQUEST['tid']) && isset($_REQUEST['rid'])) {
    $_REQUEST['tid'] = $_REQUEST['rid'];
}
if (empty($_REQUEST['tid']) || !is_numeric($_REQUEST['tid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (!empty($_POST['act'])) {
    jieqi_checkpost();
}
jieqi_loadlang('review', JIEQI_MODULE_NAME);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('r.topicid', $_REQUEST['tid']));
$criteria->setTables(jieqi_dbprefix('article_reviews') . ' r LEFT JOIN ' . jieqi_dbprefix('article_article') . ' a ON r.ownerid=a.articleid');
$query->queryObjects($criteria);
$topic = $query->getObject();
unset($criteria);
if (!$topic) {
    jieqi_printfail($jieqiLang['article']['review_not_exists']);
} else {
    if (0 < $topic->getVar('islock', 'n')) {
        jieqi_printfail($jieqiLang['article']['review_topic_locked']);
    }
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$ismanager = jieqi_checkpower($jieqiPower['article']['manageallreview'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if ($topic->getVar('display', 'n') != 0) {
    if (!$ismanager) {
        if ($topic->getVar('display', 'n') == 1) {
            jieqi_printfail($jieqiLang['article']['review_not_audit']);
        } else {
            jieqi_printfail($jieqiLang['article']['review_not_exists']);
        }
    }
}
$ownerid = $topic->getVar('ownerid', 'n');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('article', 'action', 'jieqiAction');
if (jieqi_checkpower($jieqiPower['article']['newreview'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
    $enablepost = 1;
} else {
    $enablepost = 0;
}
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    if (empty($_POST['pcontent'])) {
        jieqi_printfail($jieqiLang['article']['review_need_pcontent']);
    }
    if (!$enablepost) {
        jieqi_printfail($jieqiLang['article']['review_noper_post']);
    }
    if (!empty($jieqiAction['article']['reviewadd']['minscore']) && $_SESSION['jieqiUserScore'] < intval($jieqiAction['article']['reviewadd']['minscore'])) {
        jieqi_printfail(sprintf($jieqiLang['article']['review_score_limit'], intval($jieqiAction['article']['reviewadd']['minscore'])));
    }
}
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
$addnewreply = 0;
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    $check_errors = array();
    $post_set = array('module' => JIEQI_MODULE_NAME, 'ownerid' => intval($ownerid), 'topicid' => intval($_REQUEST['tid']), 'postid' => 0, 'posttime' => JIEQI_NOW_TIME, 'topictitle' => &$_POST['ptitle'], 'posttext' => &$_POST['pcontent'], 'attachment' => '', 'isnew' => true, 'istopic' => 0, 'istop' => 0, 'sname' => 'jieqiArticleReviewTime', 'attachfile' => '', 'oldattach' => '', 'checkcode' => $_POST['checkcode']);
    jieqi_post_checkvar($post_set, $jieqiConfigs['article'], $check_errors);
    if (empty($check_errors)) {
        include_once $jieqiModules['article']['path'] . '/class/replies.php';
        $replies_handler = JieqiRepliesHandler::getInstance('JieqiRepliesHandler');
        $newReply = $replies_handler->create();
        jieqi_post_newset($post_set, $newReply);
        $replies_handler->insert($newReply);
        $postdisplay = intval($newReply->getVar('display', 'n'));
        $postresult = 0 < $postdisplay ? $jieqiLang['article']['review_post_needaudit'] : $jieqiLang['article']['review_post_success'];
        $addnewreply = 1;
        $_REQUEST['page'] = 'last';
        jieqi_post_finish();
        $taskmodule = 'article';
        $taskname = 'replyadd';
        jieqi_getconfigs('system', 'tasks', 'jieqiTasks');
        if (!empty($jieqiTasks[$taskmodule][$taskname]['score']) && empty($_SESSION['jieqiUserSet']['tasks'][$taskmodule][$taskname])) {
            include_once JIEQI_ROOT_PATH . '/class/users.php';
            $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
            $jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
            $userset = jieqi_unserialize($jieqiUsers->getVar('setting', 'n'));
            $userset['tasks'][$taskmodule][$taskname] = 1;
            $jieqiUsers->setVar('setting', serialize($userset));
            $jieqiUsers->setVar('score', intval($jieqiUsers->getVar('score', 'n')) + intval($jieqiTasks[$taskmodule][$taskname]['score']));
            $jieqiUsers->saveToSession();
            $users_handler->insert($jieqiUsers);
        }
        include_once $jieqiModules['article']['path'] . '/include/funaction.php';
        $actions = array('actname' => 'replyadd', 'actnum' => 1);
        jieqi_article_actiondo($actions, $article);
        if (!empty($_REQUEST['ajax_request'])) {
            jieqi_msgwin(LANG_DO_SUCCESS, $postresult);
        }
    } else {
        jieqi_printfail(implode('<br />', $check_errors));
    }
}
$canedit = $ismanager;
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    if (!empty($_SESSION['jieqiUserId']) && ($topic->getVar('authorid') == $_SESSION['jieqiUserId'] || $topic->getVar('posterid') == $_SESSION['jieqiUserId'] || $topic->getVar('agentid') == $_SESSION['jieqiUserId'] || $topic->getVar('reviewerid') == $_SESSION['jieqiUserId'])) {
        $canedit = true;
    }
}
if (isset($_POST['act']) && $_POST['act'] == 'del' && !empty($_REQUEST['did'])) {
    $_REQUEST['did'] = intval($_REQUEST['did']);
    include_once $jieqiModules['article']['path'] . '/class/replies.php';
    $replies_handler = JieqiRepliesHandler::getInstance('JieqiRepliesHandler');
    $delReply = $replies_handler->get($_REQUEST['did']);
    if (!$delReply) {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
    if ($delReply->getVar('topicid', 'n') != $topic->getVar('topicid', 'n')) {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
    if (!$canedit && (empty($_SESSION['jieqiUserId']) || $_SESSION['jieqiUserId'] != $delReply->getVar('posterid', 'n'))) {
        jieqi_printfail(LANG_NO_PERMISSION);
    }
    if (!empty($jieqiAction['article']['reviewadd']['earnscore'])) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        $users_handler->changeScore(intval($delReply->getVar('posterid', 'n')), $jieqiAction['article']['reviewadd']['earnscore'], false);
    }
    $replies_handler->delete(intval($_REQUEST['did']));
    $addnewreply = -1;
    jieqi_topic_uppostdel($delReply, jieqi_dbprefix('article_reviews'));
    jieqi_jumppage($jieqiModules['article']['url'] . '/reviewshow.php?tid=' . urlencode($_REQUEST['tid']), '', '', true);
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/reviewshow.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$jieqiTpl->assign('ownerid', $topic->getVar('ownerid'));
$jieqiTpl->assign('articleid', $topic->getVar('articleid'));
$jieqiTpl->assign('articlename', $topic->getVar('articlename'));
$jieqiTpl->assign('topicid', $topic->getVar('topicid'));
$jieqiTpl->assign('title', $topic->getVar('title'));
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    $jieqiTpl->assign('newpost', 1);
    $jieqiTpl->assign('postdisplay', $postdisplay);
    $jieqiTpl->assign('postresult', $postresult);
} else {
    $jieqiTpl->assign('newpost', 0);
}
$jieqiTpl->assign('canedit', intval($canedit));
$jieqiTpl->assign('ismaster', intval($canedit));
$jieqiTpl->assign('ismanager', intval($ismanager));
$jieqiTpl->assign('url_articleinfo', jieqi_geturl('article', 'article', $topic->getVar('ownerid'), 'info', $topic->getVar('articlecode', 'n')));
include_once JIEQI_ROOT_PATH . '/class/users.php';
jieqi_getconfigs('system', 'honors');
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
if (!empty($jieqiModules['badge']['publish']) && is_file($jieqiModules['badge']['path'] . '/include/badgefunction.php')) {
    include_once $jieqiModules['badge']['path'] . '/include/badgefunction.php';
    $jieqi_use_badge = 1;
    $jieqiTpl->assign('jieqi_use_badge', 1);
} else {
    $jieqi_use_badge = 0;
    $jieqiTpl->assign('jieqi_use_badge', 0);
}
$criteria = new CriteriaCompo(new Criteria('r.topicid', $_REQUEST['tid']));
$criteria->add(new Criteria('r.display', 0));
$criteria->setTables(jieqi_dbprefix('article_replies') . ' r LEFT JOIN ' . jieqi_dbprefix('system_users') . ' u ON r.posterid=u.uid');
$criteria->setSort('r.postid');
$criteria->setOrder('ASC');
$criteria->setLimit($jieqiPset['rows']);
$jieqiPset['count'] = $query->getCount($criteria);
if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'last') {
    $_REQUEST['page'] = ceil($jieqiPset['count'] / $jieqiPset['rows']);
    $jieqiPset['page'] = $_REQUEST['page'];
    $jieqiPset['start'] = ($jieqiPset['page'] - 1) * $jieqiPset['rows'];
}
$criteria->setStart($jieqiPset['start']);
$query->queryObjects($criteria);
$replyrows = array();
$k = 0;
while ($review = $query->getObject()) {
    $addvars = array('order' => ($jieqiPset['page'] - 1) * $jieqiPset['rows'] + $k + 1);
    $replyrows[$k] = jieqi_post_vars($review, $jieqiConfigs['article'], $addvars, true);
    $k++;
}
$jieqiTpl->assign_by_ref('replyrows', $replyrows);
$jieqiTpl->assign('enablepost', $enablepost);
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
$jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
if (isset($_POST['act']) && is_string($_POST['act'])) {
    $jieqiTpl->assign('act', jieqi_htmlstr($_POST['act']));
}
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink(jieqi_geturl('article', 'reviewshow', 0, $_REQUEST['tid']));
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
if (0 < $addnewreply) {
    jieqi_topic_uppostadd($newReply, jieqi_dbprefix('article_reviews'));
} else {
    if ($addnewreply < 0) {
        jieqi_topic_uppostdel($delReply, jieqi_dbprefix('article_reviews'));
    } else {
        include_once JIEQI_ROOT_PATH . '/include/funstat.php';
        jieqi_visit_stat($_REQUEST['tid'], jieqi_dbprefix('article_reviews'), 'views', 'topicid', $query);
    }
}
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';