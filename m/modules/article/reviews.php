<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['aid']) || !is_numeric($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['aid'] = intval($_REQUEST['aid']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_loadlang('review', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('article', 'action', 'jieqiAction');
if (jieqi_checkpower($jieqiPower['article']['newreview'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
    $enablepost = 1;
} else {
    $enablepost = 0;
}
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    sleep(1);
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
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['aid']);
if (!$article) {
    if (!empty($_POST['act'])) {
        header('Location: reviewslist.php');
        exit;
    } else {
        jieqi_printfail($jieqiLang['article']['article_not_exists']);
    }
}
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
include_once $jieqiModules['article']['path'] . '/class/reviews.php';
$reviews_handler = JieqiReviewsHandler::getInstance('JieqiReviewsHandler');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$ismanager = jieqi_checkpower($jieqiPower['article']['manageallreview'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$canedit = $ismanager;
if (!$canedit && !empty($_SESSION['jieqiUserId']) && is_object($article)) {
    if (!empty($_SESSION['jieqiUserId']) && ($article->getVar('authorid') == $_SESSION['jieqiUserId'] || $article->getVar('posterid') == $_SESSION['jieqiUserId'] || $article->getVar('agentid') == $_SESSION['jieqiUserId'] || $article->getVar('reviewerid') == $_SESSION['jieqiUserId'])) {
        $canedit = true;
    }
}
if (isset($_POST['act']) && !empty($_REQUEST['rid'])) {
    if (!$canedit && $_POST['act'] != 'del') {
        jieqi_printfail(LANG_NO_PERMISSION);
    }
    $_REQUEST['rid'] = intval($_REQUEST['rid']);
    $actreview = $reviews_handler->get($_REQUEST['rid']);
    if (is_object($actreview)) {
        switch ($_POST['act']) {
            case 'top':
                $actreview->setVar('istop', 1);
                $reviews_handler->insert($actreview);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'untop':
                $actreview->setVar('istop', 0);
                $reviews_handler->insert($actreview);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'good':
                if ($actreview->getVar('isgood') == 0) {
                    $criteria = new CriteriaCompo();
                    $criteria->add(new Criteria('ownerid', $_REQUEST['aid']));
                    $allnum = $reviews_handler->getCount($criteria);
                    $criteria->add(new Criteria('isgood', 1));
                    $goodnum = $reviews_handler->getCount($criteria);
                    unset($criteria);
                    $maxnum = ceil($allnum * $jieqiConfigs['article']['goodreviewpercent'] / 100);
                    if ($maxnum <= $goodnum) {
                        jieqi_printfail(sprintf($jieqiLang['article']['review_rate_limit'], $jieqiConfigs['article']['goodreviewpercent']));
                    }
                    $actreview->setVar('isgood', 1);
                    $reviews_handler->insert($actreview);
                    if (!empty($jieqiAction['article']['reviewgood']['earnscore'])) {
                        include_once JIEQI_ROOT_PATH . '/class/users.php';
                        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                        $users_handler->changeScore($actreview->getVar('posterid'), $jieqiAction['article']['reviewgood']['earnscore'], true);
                    }
                }
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'normal':
                if ($actreview->getVar('isgood') == 1) {
                    $actreview->setVar('isgood', 0);
                    $reviews_handler->insert($actreview);
                    if (!empty($jieqiAction['article']['reviewgood']['earnscore'])) {
                        include_once JIEQI_ROOT_PATH . '/class/users.php';
                        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                        $users_handler->changeScore($actreview->getVar('posterid'), $jieqiAction['article']['reviewgood']['earnscore'], false);
                    }
                }
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'del':
                $review = $reviews_handler->get($_REQUEST['rid']);
                if (!$review) {
                    jieqi_printfail(LANG_ERROR_PARAMETER);
                }
                if ($review->getVar('ownerid', 'n') != $_REQUEST['aid']) {
                    jieqi_printfail(LANG_ERROR_PARAMETER);
                }
                if (!$canedit && (empty($_SESSION['jieqiUserId']) || $_SESSION['jieqiUserId'] != $review->getVar('posterid', 'n'))) {
                    jieqi_printfail(LANG_NO_PERMISSION);
                }
                include_once $jieqiModules['article']['path'] . '/class/replies.php';
                $replies_handler = JieqiRepliesHandler::getInstance('JieqiRepliesHandler');
                $criteria = new Criteria('topicid', $_REQUEST['rid']);
                if (!empty($jieqiAction['article']['reviewadd']['earnscore'])) {
                    $replies_handler->queryObjects($criteria);
                    $posterary = array();
                    while ($replyobj = $replies_handler->getObject()) {
                        $posterid = intval($replyobj->getVar('posterid'));
                        if (isset($posterary[$posterid])) {
                            $posterary[$posterid] += $jieqiAction['article']['reviewadd']['earnscore'];
                        } else {
                            $posterary[$posterid] = $jieqiAction['article']['reviewadd']['earnscore'];
                        }
                    }
                    if ($actreview->getVar('isgood', 'n') == 1 && !empty($jieqiAction['article']['reviewgood']['earnscore'])) {
                        $posterid = intval($actreview->getVar('posterid'));
                        if (isset($posterary[$posterid])) {
                            $posterary[$posterid] += $jieqiAction['article']['reviewgood']['earnscore'];
                        } else {
                            $posterary[$posterid] = $jieqiAction['article']['reviewgood']['earnscore'];
                        }
                    }
                    include_once JIEQI_ROOT_PATH . '/class/users.php';
                    $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                    foreach ($posterary as $pid => $pscore) {
                        $users_handler->changeScore($pid, $pscore, false);
                    }
                }
                $reviews_handler->delete($_REQUEST['rid']);
                $replies_handler->delete($criteria);
                jieqi_jumppage($jieqiModules['article']['url'] . '/reviews.php?aid=' . urlencode($_REQUEST['aid']), '', '', true);
                break;
        }
    }
}
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    $check_errors = array();
    $istopic = empty($_REQUEST['tid']) ? 1 : 0;
    $istop = $forum_type == 1 ? 2 : 0;
    $post_set = array('module' => JIEQI_MODULE_NAME, 'ownerid' => intval($_REQUEST['aid']), 'ownername' => $article->getVar('articlename', 'n'), 'ownercode' => $article->getVar('articlecode', 'n'), 'topicid' => 0, 'postid' => 0, 'posttime' => JIEQI_NOW_TIME, 'topictitle' => &$_POST['ptitle'], 'posttext' => &$_POST['pcontent'], 'attachment' => '', 'emptytitle' => true, 'isnew' => true, 'istopic' => 1, 'istop' => 0, 'sname' => 'jieqiArticleReviewTime', 'attachfile' => '', 'oldattach' => '', 'checkcode' => $_POST['checkcode']);
    if (!empty($_REQUEST['cid'])) {
        include_once $jieqiModules['article']['path'] . '/class/chapter.php';
        $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
        $chapter = $chapter_handler->get($_REQUEST['cid']);
        if (is_object($chapter)) {
            $post_set['targetid'] = intval($chapter->getVar('chapterid', 'n'));
            $post_set['targetname'] = $chapter->getVar('chaptername', 'n');
            $post_set['targetflag'] = intval($chapter->getVar('isvip', 'n'));
        }
    }
    jieqi_post_checkvar($post_set, $jieqiConfigs['article'], $check_errors);
    if (empty($check_errors)) {
        $newReview = $reviews_handler->create();
        jieqi_topic_newset($post_set, $newReview);
        $reviews_handler->insert($newReview);
        $post_set['topicid'] = $newReview->getVar('topicid', 'n');
        include_once $jieqiModules['article']['path'] . '/class/replies.php';
        $replies_handler = JieqiRepliesHandler::getInstance('JieqiRepliesHandler');
        $newReply = $replies_handler->create();
        jieqi_post_newset($post_set, $newReply);
        $replies_handler->insert($newReply);
        $postdisplay = intval($newReply->getVar('display', 'n'));
        $postresult = 0 < $postdisplay ? $jieqiLang['article']['review_post_needaudit'] : $jieqiLang['article']['review_post_success'];
        jieqi_post_finish();
        $taskmodule = 'article';
        $taskname = 'reviewadd';
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
        $actions = array('actname' => 'reviewadd', 'actnum' => 1);
        jieqi_article_actiondo($actions, $article);
        include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
        jieqi_article_updateinfo($article, 'reviewnew');
        if (!empty($_REQUEST['ajax_request'])) {
            jieqi_msgwin(LANG_DO_SUCCESS, $postresult);
        }
    } else {
        jieqi_printfail(implode('<br />', $check_errors));
    }
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/reviews.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$jieqiTpl->assign('ownerid', $article->getVar('articleid'));
$jieqiTpl->assign('articleid', $article->getVar('articleid'));
$jieqiTpl->assign('articlename', $article->getVar('articlename'));
$jieqiTpl->assign('articlecode', $article->getVar('articlecode'));
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
$jieqiTpl->assign('url_articleinfo', jieqi_geturl('article', 'article', $article->getVar('articleid'), 'info', $article->getVar('articlecode', 'n')));
$jieqiTpl->assign('url_articleindex', jieqi_geturl('article', 'article', $article->getVar('articleid'), 'index', $article->getVar('articlecode', 'n')));
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('display', 0));
$criteria->add(new Criteria('ownerid', $_REQUEST['aid']));
if (isset($_REQUEST['cid'])) {
    $_REQUEST['cid'] = intval($_REQUEST['cid']);
}
if (empty($_REQUEST['cid'])) {
    $criteria->add(new Criteria('targetid', 0));
} else {
    if (0 < $_REQUEST['cid']) {
        $criteria->add(new Criteria('targetid', $_REQUEST['cid']));
    }
}
if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'good') {
    $jieqiTpl->assign('type', 'good');
    $criteria->add(new Criteria('isgood', 1));
} else {
    $_REQUEST['type'] = 'all';
    $jieqiTpl->assign('type', 'all');
}
$criteria->setSort('istop DESC, replytime');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$reviews_handler->queryObjects($criteria);
$reviewrows = array();
$k = 0;
while ($v = $reviews_handler->getObject()) {
    $reviewrows[$k] = jieqi_topic_vars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('reviewrows', $reviewrows);
$jieqiTpl->assign('enablepost', $enablepost);
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
$jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
if (isset($_POST['act']) && is_string($_POST['act'])) {
    $jieqiTpl->assign('act', jieqi_htmlstr($_POST['act']));
}
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $reviews_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink(jieqi_geturl('article', 'reviews', 0, $_REQUEST['aid'], $_REQUEST['type']));
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';