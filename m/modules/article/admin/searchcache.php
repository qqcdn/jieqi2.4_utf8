<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('search', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
include_once $jieqiModules['article']['path'] . '/class/searchcache.php';
$searchcache_handler = JieqiSearchcacheHandler::getInstance('JieqiSearchcacheHandler');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/searchcache.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$criteria = new CriteriaCompo();
if (!empty($_REQUEST['searchtype'])) {
    $_REQUEST['searchtype'] = intval($_REQUEST['searchtype']);
    $criteria->add(new Criteria('searchtype', $_REQUEST['searchtype'], '='));
}
if (isset($_REQUEST['results'])) {
    $_REQUEST['results'] = intval($_REQUEST['results']);
    if ($_REQUEST['results'] == 0) {
        $criteria->add(new Criteria('results', 0, '='));
    } else {
        if ($_REQUEST['results'] == 1) {
            $criteria->add(new Criteria('results', 1, '='));
        } else {
            if (1 < $_REQUEST['results']) {
                $criteria->add(new Criteria('results', 1, '>'));
            }
        }
    }
}
if (isset($_REQUEST['order'])) {
    if (in_array($_REQUEST['order'], array('searchnum', 'cacheid', 'addtime', 'lasttime', 'uptime'))) {
        $criteria->setSort($_REQUEST['order']);
    } else {
        if ($_REQUEST['order'] == 'hot') {
            $criteria->setSort('searchnum / (lasttime - addtime + 600)');
        } else {
            $criteria->setSort('lasttime');
        }
    }
} else {
    $criteria->setSort('lasttime');
}
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$searchcache_handler->queryObjects($criteria);
$cacherows = array();
$k = 0;
while ($v = $searchcache_handler->getObject()) {
    $cacherows[$k] = jieqi_query_rowvars($v);
    $cacherows[$k]['searchtime'] = date(JIEQI_DATE_FORMAT . ' ' . JIEQI_TIME_FORMAT, $v->getVar('lasttime'));
    $cacherows[$k]['searchtype_n'] = $cacherows[$k]['searchtype'];
    if ($cacherows[$k]['searchtype_n'] == 1) {
        $cacherows[$k]['searchtype'] = $jieqiLang['article']['search_with_article'];
    } else {
        if ($cacherows[$k]['searchtype_n'] == 2) {
            $cacherows[$k]['searchtype'] = $jieqiLang['article']['search_with_author'];
        } else {
            $cacherows[$k]['searchtype'] = $jieqiLang['article']['search_with_all'];
        }
    }
    $k++;
}
$jieqiTpl->assign_by_ref('cacherows', $cacherows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $searchcache_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';