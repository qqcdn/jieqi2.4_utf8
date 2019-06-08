<?php

define('JIEQI_MODULE_NAME', 'article');
if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
if (empty($_REQUEST['author']) && empty($_REQUEST['authorid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (!empty($_REQUEST['authorid'])) {
    $_REQUEST['authorid'] = intval($_REQUEST['authorid']);
}
jieqi_loadlang('list', JIEQI_MODULE_NAME);
jieqi_getconfigs('article', 'configs');
jieqi_getconfigs('article', 'sort');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/authorarticle.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
$jieqiTset['jieqi_contents_cacheid'] = !empty($_REQUEST['authorid']) ? 'id_' . $_REQUEST['authorid'] : base64_encode($_REQUEST['author']);
$jieqiTset['jieqi_contents_cacheid'] .= '_p' . $_REQUEST['page'];
$content_used_cache = false;
if (JIEQI_USE_CACHE && $_REQUEST['page'] <= $jieqiConfigs['article']['cachenum']) {
    $jieqiTpl->setCaching(1);
    $jieqiTpl->setCachType(1);
    if ($jieqiTpl->is_cached($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_cacheid'], NULL, NULL, NULL, false)) {
        $uptime = jieqi_article_getuptime();
        $cachedtime = $jieqiTpl->get_cachedtime($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_cacheid']);
        if ($uptime < $cachedtime || JIEQI_NOW_TIME - $cachedtime < 15) {
            $content_used_cache = true;
        } else {
            $jieqiTpl->setCaching(2);
        }
    }
} else {
    $jieqiTpl->setCaching(0);
}
if (!$content_used_cache) {
    $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
    $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
    $jieqiTpl->assign('article_static_url', $article_static_url);
    $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
    include_once $jieqiModules['article']['path'] . '/class/article.php';
    $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
    $criteria = new CriteriaCompo(new Criteria('display', '0', '='));
    $criteria->add(new Criteria('words', '0', '>'));
    if (!empty($_REQUEST['authorid'])) {
        $criteria->add(new Criteria('authorid', $_REQUEST['authorid'], '='));
    } else {
        $criteria->add(new Criteria('author', $_REQUEST['author'], '='));
    }
    $criteria->setSort('lastupdate');
    $criteria->setOrder('DESC');
    $criteria->setLimit($jieqiPset['rows']);
    $criteria->setStart($jieqiPset['start']);
    $article_handler->queryObjects($criteria);
    $articlerows = array();
    $k = 0;
    while ($v = $article_handler->getObject()) {
        $articlerows[$k] = jieqi_article_vars($v);
        $k++;
    }
    $jieqiTpl->assign_by_ref('articlerows', $articlerows);
    $jieqiTpl->assign('url_initial', $article_dynamic_url . '/articlelist.php?initial=');
    include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
    $jieqiPset['count'] = $article_handler->getCount($criteria);
    $jumppage = new JieqiPage($jieqiPset);
    $jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
    $authorid = !empty($_REQUEST['authorid']) ? $_REQUEST['authorid'] : (isset($articlerows[0]) ? $articlerows[0]['authorid'] : 0);
    $jieqiTpl->assign('authorid', $authorid);
    $author = isset($articlerows[0]) ? $articlerows[0]['author'] : (!empty($_REQUEST['author']) ? jieqi_htmlstr($_REQUEST['author']) : '');
    $jieqiTpl->assign('author', $author);
}
include_once JIEQI_ROOT_PATH . '/footer.php';