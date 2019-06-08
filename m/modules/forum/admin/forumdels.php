<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['forum']['manageforum'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_loadlang('forumdels', JIEQI_MODULE_NAME);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/admin/forumdels.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
include_once $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/class/forumtopics.php';
$forumtopic_handler = JieqiForumtopicsHandler::getInstance('JieqiForumtopicsHandler');
if (isset($_POST['act']) && $_POST['act'] == 1 && is_array($_POST['checkid']) && 0 < intval(count($_POST['checkid']))) {
    jieqi_checkpost();
    jieqi_includedb();
    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    $where = '';
    foreach ($_POST['checkid'] as $v) {
        if (is_numeric($v)) {
            if (!empty($where)) {
                $where .= ', ';
            }
            $where .= intval($v);
        }
    }
    $sql = 'DELETE FROM ' . jieqi_dbprefix('forum_forumtopics') . ' WHERE topicid IN (' . $where . ')';
    $ret = $query->execute($sql);
    if ($ret === false) {
        jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['forum_del_failure']);
    }
    $sql = 'DELETE FROM ' . jieqi_dbprefix('forum_forumposts') . ' WHERE topicid IN (' . $where . ')';
    $query->execute($sql);
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('forum_attachs') . ' WHERE topicid IN (' . $where . ')';
    $query->execute($sql);
    while ($row = $query->getRow()) {
        $afname = jieqi_uploadpath($jieqiConfigs['forum']['attachdir'], 'forum') . '/' . date('Ymd', $row['uptime']) . '/' . $row['postid'] . '_' . $row['attachid'] . '.' . $row['postfix'];
        if (file_exists($afname)) {
            jieqi_delfile($afname);
        }
    }
    $sql = 'DELETE FROM ' . jieqi_dbprefix('forum_attachs') . ' WHERE topicid IN (' . $where . ')';
    $query->execute($sql);
    jieqi_jumppage($jieqiModules[JIEQI_MODULE_NAME]['url'] . '/admin/forumdels.php?keyword=' . $_REQUEST['keywordurl'] . '&keytype=' . $_REQUEST['keytypeurl'] . '', LANG_DO_SUCCESS, $jieqiLang[JIEQI_MODULE_NAME]['forum_del_success']);
}
$criteria = new CriteriaCompo();
$_REQUEST['keyword'] = trim($_REQUEST['keyword']);
if (!empty($_REQUEST['keyword'])) {
    switch ($_REQUEST['keytype']) {
        case '0':
            $criteria->add(new Criteria('title', '%' . $_REQUEST['keyword'] . '%', 'like'));
            $forumtitle = $jieqiLang['forum']['forum_name'];
            break;
        case '1':
            $criteria->add(new Criteria('poster', $_REQUEST['keyword'], '='));
            $forumtitle = $jieqiLang['forum']['forum_author'];
            break;
        case '2':
            $today = strtotime($_REQUEST['keyword']);
            $end = $today + 86400;
            $criteria->add(new Criteria('posttime', $today, '>='));
            $criteria->add(new Criteria('posttime', $end, '<='));
            $forumtitle = $jieqiLang['forum']['forum_posttime'];
            break;
        default:
            jieqi_printfail(LANG_ERROR_PARAMETER);
            break;
    }
}
if (empty($_REQUEST['keyword'])) {
    $forumtitle = $jieqiLang['forum']['forum_manage'];
}
$jieqiTpl->assign('forumtitle', $forumtitle);
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
$criteria->setSort('posttime');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$forumtopic_handler->queryObjects($criteria);
$forumrows = array();
$k = 0;
while ($v = $forumtopic_handler->getObject()) {
    $forumrows[$k] = jieqi_topic_vars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('forumrows', $forumrows);
$jieqiTpl->assign('page', intval($_REQUEST['page']));
$jieqiTpl->assign('keywordurl', jieqi_htmlstr($_REQUEST['keyword']));
$jieqiTpl->assign('keytypeurl', jieqi_htmlstr($_REQUEST['keytype']));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $forumtopic_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$pagelink = '';
if (!empty($_REQUEST['keyword'])) {
    if (empty($pagelink)) {
        $pagelink .= '?';
    } else {
        $pagelink .= '&';
    }
    $pagelink .= 'keyword=' . urlencode($_REQUEST['keyword']);
    $pagelink .= '&keytype=' . urlencode($_REQUEST['keytype']);
}
if (empty($pagelink)) {
    $pagelink .= '?page=';
} else {
    $pagelink .= '&page=';
}
$jumppage->setlink($jieqiModules['forum']['url'] . '/admin/forumdels.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';