<?php

define('JIEQI_MODULE_NAME', 'article');
if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
jieqi_loadlang('list', JIEQI_MODULE_NAME);
jieqi_getconfigs('article', 'configs');
jieqi_getconfigs('article', 'sort');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/articlelist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
if (!isset($_GET['sortid']) && isset($_GET['class'])) {
    $_GET['sortid'] = $_GET['class'];
    unset($_GET['class']);
}
$int_sortid = 0;
$use_sortcode = false;
if (!empty($_GET['sortid']) && is_numeric($_GET['sortid']) && isset($jieqiSort['article'][$_GET['sortid']])) {
    $_GET['sortid'] = intval($_GET['sortid']);
    $int_sortid = $_GET['sortid'];
}
if (empty($int_sortid)) {
    if (isset($_GET['sortcode'])) {
        $_GET['sortcode'] = trim($_GET['sortcode']);
    } else {
        if (isset($_GET['sortid']) && !is_numeric($_GET['sortid'])) {
            $_GET['sortcode'] = trim($_GET['sortid']);
        } else {
            $_GET['sortcode'] = '';
        }
    }
    if (0 < strlen($_GET['sortcode'])) {
        foreach ($jieqiSort['article'] as $k => $v) {
            if (isset($v['code']) && $v['code'] == $_GET['sortcode']) {
                $int_sortid = intval($k);
                $use_sortcode = true;
                break;
            }
        }
    }
}
if (empty($int_sortid)) {
    $_GET['sortid'] = '';
    $_GET['sortcode'] = '';
    $jieqiTpl->assign('sortid', 0);
    $jieqiTpl->assign('sort', '');
    $jieqiTpl->assign('sortcode', '');
} else {
    $_GET['sortcode'] = isset($jieqiSort['article'][$int_sortid]['code']) ? $jieqiSort['article'][$int_sortid]['code'] : '';
    $jieqiTpl->assign('sortid', $int_sortid);
    $jieqiTpl->assign('sort', jieqi_htmlstr($jieqiSort['article'][$int_sortid]['caption']));
    $jieqiTpl->assign('sortcode', jieqi_htmlstr($_GET['sortcode']));
}
if (empty($_GET['fullflag'])) {
    $_GET['fullflag'] = '';
} else {
    $_GET['fullflag'] = 1;
}
if (!empty($_GET['fullflag'])) {
    $jieqiTpl->assign('fullflag', 1);
} else {
    $jieqiTpl->assign('fullflag', 0);
}
if (!isset($_GET['initial']) || !preg_match('/^[A-Z01]$/i', $_GET['initial'])) {
    $_GET['initial'] = '';
}
if (!empty($_GET['initial'])) {
    $jieqiTpl->assign('initial', $_GET['initial']);
} else {
    $jieqiTpl->assign('initial', '');
}
include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
$jieqiTset['jieqi_contents_cacheid'] = $int_sortid . '_' . $_GET['fullflag'] . '_' . $_GET['initial'];
$pagecacheid = $jieqiTset['jieqi_contents_cacheid'];
$jieqiTset['jieqi_contents_cacheid'] .= '_' . $_REQUEST['page'];
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
$articletitle = '';
if (!$content_used_cache) {
    $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
    $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
    $jieqiTpl->assign('article_static_url', $article_static_url);
    $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
    $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));
    include_once $jieqiModules['article']['path'] . '/class/article.php';
    $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
    $criteria = new CriteriaCompo(new Criteria('display', '0', '='));
    $criteria->add(new Criteria('words', '0', '>'));
    if (!empty($_GET['initial'])) {
        $criteria->add(new Criteria('initial', strtoupper($_GET['initial']), '='));
        $articletitle = sprintf($jieqiLang['article']['list_initial_title'], strtoupper($_GET['initial']));
        if (!empty($_GET['fullflag'])) {
            $criteria->add(new Criteria('fullflag', '1', '='));
            if ($articletitle != '') {
                $articletitle .= ' - ';
            }
            $articletitle .= $jieqiLang['article']['list_full_title'];
        }
    } else {
        if (!empty($_GET['fullflag'])) {
            $criteria->add(new Criteria('fullflag', '1', '='));
            if ($articletitle != '') {
                $articletitle .= ' - ';
            }
            $articletitle .= $jieqiLang['article']['list_full_title'];
        }
        if (!empty($int_sortid)) {
            $criteria->add(new Criteria('sortid', $int_sortid, '='));
            if ($articletitle != '') {
                $articletitle .= ' - ';
            }
            $articletitle .= $jieqiSort['article'][$int_sortid]['caption'];
        }
    }
    if ($articletitle == '') {
        $articletitle = $jieqiLang['article']['list_article_title'];
    }
    $jieqiTpl->assign('articletitle', $articletitle);
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
    if (JIEQI_USE_CACHE) {
        jieqi_getcachevars('article', 'articlelistlog');
        if (!is_array($jieqiArticlelistlog)) {
            $jieqiArticlelistlog = array();
        }
        if (!isset($jieqiArticlelistlog[$pagecacheid]) || JIEQI_CACHE_LIFETIME < JIEQI_NOW_TIME - $jieqiArticlelistlog[$pagecacheid]['time']) {
            $jieqiArticlelistlog[$pagecacheid] = array('rows' => $article_handler->getCount($criteria), 'time' => JIEQI_NOW_TIME);
            jieqi_setcachevars('articlelistlog', 'jieqiArticlelistlog', $jieqiArticlelistlog, 'article');
        }
        $toplistrows = $jieqiArticlelistlog[$pagecacheid]['rows'];
    } else {
        $toplistrows = $article_handler->getCount($criteria);
    }
    $jieqiPset['count'] = $toplistrows;
    $jumppage = new JieqiPage($jieqiPset);
    if (!empty($_GET['initial'])) {
        $jumppage->setlink(jieqi_geturl('article', 'initial', 0, $_GET['initial'], $_GET['fullflag']));
    } else {
        if (!$use_sortcode) {
            $jumppage->setlink(jieqi_geturl('article', 'articlelist', 0, $_GET['sortid'], $_GET['fullflag']));
        } else {
            $jumppage->setlink(jieqi_geturl('article', 'articlelist', 0, $_GET['sortcode'], $_GET['fullflag']));
        }
    }
    $jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
}
include_once JIEQI_ROOT_PATH . '/footer.php';