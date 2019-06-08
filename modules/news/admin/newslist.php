<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower[JIEQI_MODULE_NAME]['newslist'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs', 'jieqiConfigs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
jieqi_loadlang('news', JIEQI_MODULE_NAME);
$canaudit = jieqi_checkpower($jieqiPower['news']['newsaudit'], $jieqiUsersStatus, $jieqiUsersGroup, true, true);
$canedit = jieqi_checkpower($jieqiPower['news']['newsedit'], $jieqiUsersStatus, $jieqiUsersGroup, true, true);
$candel = jieqi_checkpower($jieqiPower['news']['newsdel'], $jieqiUsersStatus, $jieqiUsersGroup, true, true);
include_once $jieqiModules['news']['path'] . '/class/topic.php';
$topic_handler = JieqiNewstopicHandler::getInstance('JieqiNewstopicHandler');
if (isset($_POST['act']) && !empty($_REQUEST['id'])) {
    jieqi_checkpost();
    $_REQUEST['id'] = intval($_REQUEST['id']);
    $criteria = new CriteriaCompo(new Criteria('topicid', $_REQUEST['id']));
    jieqi_getcachevars('news', 'newsuplog');
    if (!is_array($jieqiNewsuplog)) {
        $jieqiNewsuplog = array('newsuptime' => 0);
    }
    switch ($_POST['act']) {
        case 'show':
            if ($canaudit) {
                $topic_handler->updatefields(array('display' => 0), $criteria);
                $jieqiNewsuplog['newsuptime'] = JIEQI_NOW_TIME;
                jieqi_setcachevars('newsuplog', 'jieqiNewsuplog', $jieqiNewsuplog, 'news');
            }
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'hide':
            if ($canaudit) {
                $topic_handler->updatefields(array('display' => 2), $criteria);
                $jieqiNewsuplog['newsuptime'] = JIEQI_NOW_TIME;
                jieqi_setcachevars('newsuplog', 'jieqiNewsuplog', $jieqiNewsuplog, 'news');
            }
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'ready':
            if ($canaudit) {
                $topic_handler->updatefields(array('display' => 1), $criteria);
                $jieqiNewsuplog['newsuptime'] = JIEQI_NOW_TIME;
                jieqi_setcachevars('newsuplog', 'jieqiNewsuplog', $jieqiNewsuplog, 'news');
            }
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'del':
            if ($candel) {
                $topic = $topic_handler->get($_REQUEST['id']);
                if (is_object($topic)) {
                    $topic_handler->delete($_REQUEST['id']);
                    include_once $jieqiModules['news']['path'] . '/class/content.php';
                    $content_handler = JieqiNewscontentHandler::getInstance('JieqiNewscontentHandler');
                    $content_handler->delete($criteria);
                    $cover = $topic->getVar('cover', 'n');
                    if (0 < $cover && isset($jieqi_image_type[$cover])) {
                        if (empty($jieqiConfigs['news']['coverdir'])) {
                            $jieqiConfigs['news']['coverdir'] = $jieqiConfigs['news']['imagedir'];
                        }
                        jieqi_delfile(jieqi_uploadpath($jieqiConfigs['news']['coverdir'], 'news') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . $jieqi_image_type[$cover]);
                    }
                    include_once JIEQI_ROOT_PATH . '/include/funtag.php';
                    $tags = jieqi_tag_clean($topic->getVar('tags', 'n'));
                    jieqi_tag_delete($tags, $_REQUEST['id'], array('tag' => jieqi_dbprefix('news_tag'), 'taglink' => jieqi_dbprefix('news_taglink')));
                }
            }
            jieqi_jumppage($jieqiModules['news']['url'] . '/admin/newslist.php', '', '', true);
            break;
    }
    unset($criteria);
}
if (isset($_POST['act']) && !empty($_POST['checkid'])) {
    jieqi_checkpost();
    $ids = array();
    foreach ($_POST['checkid'] as $v) {
        $v = intval($v);
        if (0 < $v) {
            $ids[] = $v;
        }
    }
    if (!empty($ids)) {
        switch ($_POST['act']) {
            case 'del':
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('topicid', '(' . implode(',', $ids) . ')', 'IN'));
                $res = $topic_handler->queryObjects($criteria);
                if (empty($jieqiConfigs['news']['coverdir'])) {
                    $jieqiConfigs['news']['coverdir'] = $jieqiConfigs['news']['imagedir'];
                }
                while ($v = $topic_handler->getObject($res)) {
                    $topicid = $v->getVar('topicid', 'n');
                    $cover = $v->getVar('cover', 'n');
                    $topic_handler->execute('DELETE FROM ' . jieqi_dbprefix('news_topic') . '  WHERE topicid = ' . $topicid);
                    $topic_handler->execute('DELETE FROM ' . jieqi_dbprefix('news_content') . '  WHERE topicid = ' . $topicid);
                    if (0 < $cover && isset($jieqi_image_type[$cover])) {
                        jieqi_delfile(jieqi_uploadpath($jieqiConfigs['news']['coverdir'], 'news') . jieqi_getsubdir($topicid) . '/' . $topicid . $jieqi_image_type[$cover]);
                    }
                }
                jieqi_jumppage($jieqiModules['news']['url'] . '/admin/newslist.php', '', '', true);
                break;
            case 'show':
                $topic_handler->execute('UPDATE ' . jieqi_dbprefix('news_topic') . ' SET display = 0 WHERE topicid IN (' . implode(',', $ids) . ')');
                jieqi_jumppage($jieqiModules['news']['url'] . '/admin/newslist.php', '', '', true);
                break;
            case 'ready':
                $topic_handler->execute('UPDATE ' . jieqi_dbprefix('news_topic') . ' SET display = 1 WHERE topicid IN (' . implode(',', $ids) . ')');
                jieqi_jumppage($jieqiModules['news']['url'] . '/admin/newslist.php', '', '', true);
                break;
        }
    }
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/templates/admin/newslist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
if (empty($_REQUEST['sortid'])) {
    $_REQUEST['sortid'] = 0;
}
$criteria = new CriteriaCompo();
if (isset($_REQUEST['keyword'])) {
    $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
}
if (!empty($_REQUEST['keyword'])) {
    if ($_REQUEST['keytype'] == 1) {
        $criteria->add(new Criteria('poster', $_REQUEST['keyword'], '='));
    } else {
        $criteria->add(new Criteria('title', '%' . $_REQUEST['keyword'] . '%', 'LIKE'));
    }
}
$sortname = '';
if (!empty($_REQUEST['sortid'])) {
    $criteria->add(new Criteria('sortid', $_REQUEST['sortid'], '='));
    $sortname = $jieqiSort['news'][$_REQUEST['sortid']]['sortname'];
}
$jieqiTpl->assign('sortname', $sortname);
if (!empty($_REQUEST['display'])) {
    switch ($_REQUEST['display']) {
        case 'unshow':
            $criteria->add(new Criteria('display', 0, '>'));
            break;
        case 'ready':
            $criteria->add(new Criteria('display', 1, '='));
            break;
        case 'hide':
            $criteria->add(new Criteria('display', 2, '='));
            break;
        case 'show':
            $criteria->add(new Criteria('display', 0, '='));
            break;
    }
}
$criteria->setSort('topicid');
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
$pagelink = '';
if (!empty($_REQUEST['sortid'])) {
    if (empty($pagelink)) {
        $pagelink .= '?';
    } else {
        $pagelink .= '&';
    }
    $pagelink .= 'sortid=' . urlencode($_REQUEST['sortid']);
} else {
    if (!empty($_REQUEST['display'])) {
        if (empty($pagelink)) {
            $pagelink .= '?';
        } else {
            $pagelink .= '&';
        }
        $pagelink .= 'display=' . urlencode($_REQUEST['display']);
    }
}
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
$jumppage->setlink($jieqiModules['news']['url'] . '/admin/newslist.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';