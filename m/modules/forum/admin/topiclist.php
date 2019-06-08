<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['forum']['manageforum'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (!empty($_POST['act'])) {
    jieqi_checkpost();
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
if (isset($_POST['act'])) {
    $actionids = array();
    if (!empty($_REQUEST['tid'])) {
        $actionids[] = intval($_REQUEST['tid']);
    } else {
        if (!empty($_REQUEST['checkid']) && is_array($_REQUEST['checkid'])) {
            foreach ($_REQUEST['checkid'] as $v) {
                if (is_numeric($v)) {
                    $actionids[] = intval($v);
                }
            }
        }
    }
    if (!empty($actionids)) {
        if (count($actionids) == 1) {
            $where = 'topicid = ' . $actionids[0];
        } else {
            $where = 'topicid IN (' . implode(', ', $actionids) . ')';
        }
        switch ($_POST['act']) {
            case 'top':
                $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET istop = 1 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'untop':
                $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET istop = 0 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'good':
                $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET isgood = 1 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'normal':
                $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET isgood = 0 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'delete':
                $query->execute('DELETE FROM ' . jieqi_dbprefix('forum_forumtopics') . ' WHERE ' . $where);
                $query->execute('DELETE FROM ' . jieqi_dbprefix('forum_forumposts') . ' WHERE ' . $where);
                $query->execute('SELECT * FROM ' . jieqi_dbprefix('forum_attachs') . ' WHERE ' . $where);
                while ($row = $query->getRow()) {
                    $afname = jieqi_uploadpath($configs['attachdir'], 'forum') . '/' . date('Ymd', $row['uptime']) . '/' . $row['postid'] . '_' . $row['attachid'] . '.' . $row['postfix'];
                    if (file_exists($afname)) {
                        jieqi_delfile($afname);
                    }
                }
                $query->execute('DELETE FROM ' . jieqi_dbprefix('forum_attachs') . ' WHERE ' . $where);
                jieqi_jumppage($jieqiModules['forum']['url'] . '/admin/topiclist.php', '', '', true);
                break;
        }
    }
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/admin/topiclist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
$criteria = new CriteriaCompo();
$criteria->setFields('t.*, f.forumid, f.forumname');
$criteria->setTables(jieqi_dbprefix('forum_forumtopics') . ' AS t LEFT JOIN ' . jieqi_dbprefix('forum_forums') . ' AS f ON t.ownerid = f.forumid');
if (isset($_REQUEST['display']) && is_numeric($_REQUEST['display'])) {
    $criteria->add(new Criteria('t.display', intval($_REQUEST['display'])));
    $jieqiTpl->assign('display', intval($_REQUEST['display']));
} else {
    $jieqiTpl->assign('display', '');
}
if (!empty($_REQUEST['keyword'])) {
    $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
    if ($_REQUEST['keytype'] == 1) {
        $criteria->add(new Criteria('t.poster', $_REQUEST['keyword'], '='));
    } else {
        if ($_REQUEST['keytype'] == 2) {
            $criteria->add(new Criteria('t.title', '%' . $_REQUEST['keyword'] . '%', 'like'));
        } else {
            $criteria->add(new Criteria('u.name', $_REQUEST['keyword'], '='));
        }
    }
}
if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'good') {
    $criteria->add(new Criteria('t.isgood', 1));
} else {
    $_REQUEST['type'] = 'all';
}
$criteria->setSort('t.topicid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$query->queryObjects($criteria);
$topicrows = array();
$k = 0;
while ($v = $query->getObject()) {
    $topicrows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('topicrows', $topicrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $query->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';