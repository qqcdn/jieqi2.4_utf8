<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['forum']['manageforum'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
if (isset($_POST['act'])) {
    jieqi_checkpost();
    $actionids = array();
    if (!empty($_REQUEST['pid'])) {
        $actionids[] = intval($_REQUEST['pid']);
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
            $where = 'postid = ' . $actionids[0];
        } else {
            $where = 'postid IN (' . implode(', ', $actionids) . ')';
        }
        $sql = 'SELECT topicid FROM ' . jieqi_dbprefix('forum_forumposts') . ' WHERE ' . $where . ' AND istopic = 1';
        $query->execute($sql);
        $topicids = array();
        while ($row = $query->getRow()) {
            $topicids[] = intval($row['topicid']);
        }
        switch ($_POST['act']) {
            case 'audit':
                if (!empty($topicids)) {
                    $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET display = 0 WHERE topicid IN (' . implode(', ', $topicids) . ')');
                }
                $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumposts') . ' SET display = 0 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'delete':
                if (!empty($topicids)) {
                    $query->execute('DELETE FROM ' . jieqi_dbprefix('forum_forumtopics') . ' WHERE topicid IN (' . implode(', ', $topicids) . ')');
                }
                $query->execute('DELETE FROM ' . jieqi_dbprefix('forum_forumposts') . ' WHERE ' . $where);
                $query->execute('SELECT * FROM ' . jieqi_dbprefix('forum_attachs') . ' WHERE ' . $where);
                while ($row = $query->getRow()) {
                    $afname = jieqi_uploadpath($configs['attachdir'], 'forum') . '/' . date('Ymd', $row['uptime']) . '/' . $row['postid'] . '_' . $row['attachid'] . '.' . $row['postfix'];
                    if (file_exists($afname)) {
                        jieqi_delfile($afname);
                    }
                }
                $query->execute('DELETE FROM ' . jieqi_dbprefix('forum_attachs') . ' WHERE ' . $where);
                jieqi_jumppage($jieqiModules['forum']['url'] . '/admin/postlist.php?display=' . urlencode($_REQUEST['diaplay']), '', '', true);
                break;
        }
    }
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/admin/postlist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
$criteria = new CriteriaCompo();
$criteria->setTables(jieqi_dbprefix('forum_forumtopics') . ' t RIGHT JOIN ' . jieqi_dbprefix('forum_forumposts') . ' p ON t.topicid=p.topicid');
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
    $addvars = array('order' => ($jieqiPset['page'] - 1) * $jieqiPset['rows'] + $k + 1);
    $postrows[$k] = jieqi_post_vars($v, $jieqiConfigs['forum'], $addvars, true);
    $k++;
}
$jieqiTpl->assign_by_ref('postrows', $postrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $query->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';