<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallreview'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (!empty($_POST['act'])) {
    jieqi_checkpost();
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('article', 'action', 'jieqiAction');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
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
                $query->execute('UPDATE ' . jieqi_dbprefix('article_reviews') . ' SET istop = 1 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'untop':
                $query->execute('UPDATE ' . jieqi_dbprefix('article_reviews') . ' SET istop = 0 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'good':
                $query->execute('UPDATE ' . jieqi_dbprefix('article_reviews') . ' SET isgood = 1 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'normal':
                $query->execute('UPDATE ' . jieqi_dbprefix('article_reviews') . ' SET isgood = 0 WHERE ' . $where);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'delete':
                $query->execute('DELETE FROM ' . jieqi_dbprefix('article_reviews') . ' WHERE ' . $where);
                $query->execute('DELETE FROM ' . jieqi_dbprefix('article_replies') . ' WHERE ' . $where);
                $url = $jieqiModules['article']['url'] . '/admin/reviews.php';
                if (isset($_REQUEST['display']) && is_numeric($_REQUEST['display'])) {
                    $url .= '?display=' . intval($_REQUEST['display']);
                }
                jieqi_jumppage($url, '', '', true);
                break;
        }
    }
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/reviews.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
$criteria = new CriteriaCompo();
$criteria->setFields('t.*, a.articleid, a.articlename');
$criteria->setTables(jieqi_dbprefix('article_reviews') . ' AS t LEFT JOIN ' . jieqi_dbprefix('article_article') . ' AS a ON t.ownerid = a.articleid');
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
            $criteria->add(new Criteria('a.articlename', $_REQUEST['keyword'], '='));
        }
    }
}
if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'good') {
    $criteria->add(new Criteria('isgood', 1));
} else {
    $_REQUEST['type'] = 'all';
}
$criteria->setSort('t.topicid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$query->queryObjects($criteria);
$reviewrows = array();
$k = 0;
while ($v = $query->getObject()) {
    $reviewrows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('reviewrows', $reviewrows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $query->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';