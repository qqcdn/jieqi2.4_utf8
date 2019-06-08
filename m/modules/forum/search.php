<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../global.php';
jieqi_checklogin();
jieqi_loadlang('search', JIEQI_MODULE_NAME);
if (isset($_REQUEST['searchkey'])) {
    $_REQUEST['searchkey'] = trim($_REQUEST['searchkey']);
}
if (!isset($_REQUEST['searchkey']) || strlen($_REQUEST['searchkey']) == 0) {
    include_once JIEQI_ROOT_PATH . '/header.php';
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/search.html';
    $jieqiTpl->assign('form_action', $jieqiModules['forum']['url'] . '/search.php');
    include_once $jieqiModules['forum']['path'] . '/class/forumcat.php';
    include_once $jieqiModules['forum']['path'] . '/class/forums.php';
    $criteria = new CriteriaCompo();
    $criteria->setSort('catorder');
    $criteria->setOrder('ASC');
    $forumcat_handler = JieqiForumcatHandler::getInstance('JieqiForumcatHandler');
    $forumcat_handler->queryObjects($criteria);
    $forumcats = array();
    $i = 0;
    while ($v = $forumcat_handler->getObject()) {
        $forumcats[$i]['catid'] = $v->getVar('catid');
        $forumcats[$i]['cattitle'] = $v->getVar('cattitle');
        $forumcats[$i]['cattitle_e'] = $v->getVar('cattitle', 'e');
        $i++;
    }
    unset($criteria);
    $criteria = new CriteriaCompo();
    $criteria->setSort('catid ASC, forumorder');
    $criteria->setOrder('ASC');
    $forums_handler = JieqiForumsHandler::getInstance('JieqiForumsHandler');
    $forums_handler->queryObjects($criteria);
    $forums = array();
    $i = 0;
    while ($v = $forums_handler->getObject()) {
        $forums[$i]['catid'] = $v->getVar('catid');
        $forums[$i]['forumid'] = $v->getVar('forumid');
        $forums[$i]['forumname_e'] = $v->getVar('forumname', 'e');
        $forums[$i]['authview_n'] = $v->getVar('authview', 'n');
        $i++;
    }
    $forumselect = array();
    foreach ($forumcats as $forumcat) {
        $forumselect[] = array('title' => $forumcat['cattitle_e'], 'value' => $forumcat['catid'] . '|0');
        foreach ($forums as $forum) {
            if ($forum['catid'] == $forumcat['catid']) {
                $viewpower['groups'] = jieqi_unserialize($forum['authview_n']);
                if (!is_array($viewpower['groups'])) {
                    $viewpower['groups'] = array();
                }
                if (jieqi_checkpower($viewpower, $jieqiUsersStatus, $jieqiUsersGroup, true)) {
                    $forumselect[] = array('title' => '&nbsp;&gt; ' . $forum['forumname_e'], 'value' => $forumcat['catid'] . '|' . $forum['forumid']);
                }
            }
        }
    }
    $jieqiTpl->assign_by_ref('forumcats', $forumcats);
    $jieqiTpl->assign_by_ref('forums', $forums);
    $jieqiTpl->assign_by_ref('forumselect', $forumselect);
    $jieqiTpl->setCaching(0);
    include_once JIEQI_ROOT_PATH . '/footer.php';
} else {
    if (empty($_REQUEST['searchkey'])) {
        jieqi_printfail($jieqiLang['forum']['need_search_keywords']);
    }
    jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
    if (!empty($jieqiConfigs['forum']['minsearchlen']) && strlen($_REQUEST['searchkey']) < intval($jieqiConfigs['forum']['minsearchlen']) && $_REQUEST['searchtype'] != 2) {
        jieqi_printfail(sprintf($jieqiLang['forum']['min_search_keywords'], $jieqiConfigs['forum']['minsearchlen']));
    }
    if (!empty($jieqiConfigs['forum']['minsearchtime']) && empty($_REQUEST['page'])) {
        $jieqi_visit_time = jieqi_strtosary($_COOKIE['jieqiVisitTime']);
        if (!empty($_SESSION['jieqiForumsearchTime'])) {
            $logtime = $_SESSION['jieqiForumsearchTime'];
        } else {
            if (!empty($jieqi_visit_time['jieqiForumsearchTime'])) {
                $logtime = $jieqi_visit_time['jieqiForumsearchTime'];
            } else {
                $logtime = 0;
            }
        }
        if (0 < $logtime && JIEQI_NOW_TIME - $logtime < intval($jieqiConfigs['forum']['minsearchtime'])) {
            jieqi_printfail(sprintf($jieqiLang['forum']['search_time_limit'], $jieqiConfigs['forum']['minsearchtime']));
        }
        $_SESSION['jieqiForumsearchTime'] = JIEQI_NOW_TIME;
        $jieqi_visit_time['jieqiForumsearchTime'] = JIEQI_NOW_TIME;
        setcookie('jieqiVisitTime', jieqi_sarytostr($jieqi_visit_time), JIEQI_NOW_TIME + 3600, '/', JIEQI_COOKIE_DOMAIN, 0);
    }
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/searchlist.html';
    include_once JIEQI_ROOT_PATH . '/header.php';
    $jieqiPset = jieqi_get_pageset();
    jieqi_includedb();
    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    $criteria = new CriteriaCompo();
    $criteria->setSort('t.replytime');
    $criteria->setOrder('DESC');
    $criteria->setLimit($jieqiPset['rows']);
    $criteria->setStart($jieqiPset['start']);
    $criteria->setTables(jieqi_dbprefix('forum_forumtopics') . ' t LEFT JOIN ' . jieqi_dbprefix('forum_forums') . ' f ON t.ownerid=f.forumid');
    if (!empty($_REQUEST['searcharea'])) {
        $tmpary = explode('|', $_REQUEST['searcharea']);
        if (isset($tmpary[1]) && !empty($tmpary[1])) {
            $criteria->add(new Criteria('t.ownerid', $tmpary[1]));
        } else {
            if (isset($tmpary[0]) && !empty($tmpary[0])) {
                $criteria->add(new Criteria('f.catid', $tmpary[0]));
            }
        }
    }
    if (isset($_REQUEST['searchtype']) && $_REQUEST['searchtype'] == 2) {
        $criteria->add(new Criteria('posterid', intval($_REQUEST['searchkey'])));
    } else {
        if (isset($_REQUEST['searchtype']) && $_REQUEST['searchtype'] == 1) {
            $criteria->add(new Criteria('poster', $_REQUEST['searchkey']));
        } else {
            if ($jieqiConfigs['forum']['searchtype'] == 1) {
                $criteria->add(new Criteria('title', $_REQUEST['searchkey'] . '%', 'like'));
            } else {
                if ($jieqiConfigs['forum']['searchtype'] == 2) {
                    $criteria->add(new Criteria('title', $_REQUEST['searchkey'], '='));
                } else {
                    $criteria->add(new Criteria('title', '%' . $_REQUEST['searchkey'] . '%', 'like'));
                }
            }
        }
    }
    $query->queryObjects($criteria);
    include_once JIEQI_ROOT_PATH . '/include/funpost.php';
    $topicrows = array();
    $k = 0;
    while ($topic = $query->getObject()) {
        $topicrows[$k] = jieqi_topic_vars($topic);
        $k++;
    }
    $jieqiTpl->assign_by_ref('topicrows', $topicrows);
    $page_rowcount = $query->getCount($criteria);
    include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
    if (!empty($jieqiConfigs['forum']['maxsearchres']) && intval($jieqiConfigs['forum']['maxsearchres']) < $page_rowcount) {
        $page_rowcount = intval($jieqiConfigs['forum']['maxsearchres']);
    }
    $jieqiPset['count'] = $page_rowcount;
    $jumppage = new JieqiPage($jieqiPset);
    $jumppage->setlink('', true, true);
    $jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
    $jieqiTpl->setCaching(0);
    include_once JIEQI_ROOT_PATH . '/footer.php';
}