<?php

function jieqi_article_upscache($aids)
{
    global $searchcache_handler;
    global $searchcache;
    global $hashid;
    global $intstype;
    global $allresults;
    global $cachetime;
    global $usecache;
    if (!$usecache) {
        if (is_array($aids)) {
            sort($aids);
            $aids = implode(',', $aids);
        } else {
            $aids = strval($aids);
        }
        $cleancache = false;
        if (is_object($searchcache)) {
            if ($aids == $searchcache->getVar('aids', 'n')) {
                $searchcache->setVar('lasttime', JIEQI_NOW_TIME);
                $searchcache->setVar('searchnum', $searchcache->getVar('searchnum', 'n') + 1);
            } else {
                $searchcache->setVar('uptime', JIEQI_NOW_TIME);
                $searchcache->setVar('lasttime', JIEQI_NOW_TIME);
                $searchcache->setVar('searchnum', $searchcache->getVar('searchnum', 'n') + 1);
                $searchcache->setVar('results', $allresults);
                $searchcache->setVar('aids', $aids);
            }
            if (date('s', JIEQI_NOW_TIME) == '00') {
                $cleancache = true;
            }
        } else {
            $searchcache = $searchcache_handler->create();
            $searchcache->setVar('addtime', JIEQI_NOW_TIME);
            $searchcache->setVar('lasttime', JIEQI_NOW_TIME);
            $searchcache->setVar('uptime', JIEQI_NOW_TIME);
            $searchcache->setVar('searchnum', 0);
            $searchcache->setVar('hashid', $hashid);
            $searchcache->setVar('keywords', $_REQUEST['searchkey']);
            $searchcache->setVar('searchtype', $intstype);
            $searchcache->setVar('results', $allresults);
            $searchcache->setVar('aids', $aids);
        }
        $searchcache_handler->insert($searchcache);
        if ($cleancache) {
            $criteria = new CriteriaCompo(new Criteria('uptime', JIEQI_NOW_TIME - $cachetime, '<'));
            $searchcache_handler->delete($criteria);
        }
    } else {
        $sql = 'UPDATE ' . jieqi_dbprefix('article_searchcache') . ' SET searchnum = searchnum + 1 WHERE cacheid = ' . intval($searchcache->getVar('cacheid', 'n'));
        $searchcache_handler->execute($sql);
    }
}
define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_loadlang('search', JIEQI_MODULE_NAME);
if (isset($_REQUEST['searchkey'])) {
    $_REQUEST['searchkey'] = trim($_REQUEST['searchkey']);
}
if (!isset($_REQUEST['searchkey']) || strlen($_REQUEST['searchkey']) == 0) {
    include_once JIEQI_ROOT_PATH . '/header.php';
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/search.html';
    include_once JIEQI_ROOT_PATH . '/footer.php';
    exit;
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (!empty($jieqiConfigs['article']['minsearchlen']) && strlen($_REQUEST['searchkey']) < intval($jieqiConfigs['article']['minsearchlen'])) {
    jieqi_printfail(sprintf($jieqiLang['article']['search_minsize_limit'], $jieqiConfigs['article']['minsearchlen']));
}
if (!empty($jieqiConfigs['article']['minsearchtime']) && empty($_REQUEST['page'])) {
    $jieqi_visit_time = jieqi_strtosary($_COOKIE['jieqiVisitTime']);
    if (!empty($_SESSION['jieqiArticlesearchTime'])) {
        $logtime = $_SESSION['jieqiArticlesearchTime'];
    } else {
        if (!empty($jieqi_visit_time['jieqiArticlesearchTime'])) {
            $logtime = $jieqi_visit_time['jieqiArticlesearchTime'];
        } else {
            $logtime = 0;
        }
    }
    if (0 < $logtime && JIEQI_NOW_TIME - $logtime < intval($jieqiConfigs['article']['minsearchtime'])) {
        jieqi_printfail(sprintf($jieqiLang['article']['search_time_limit'], $jieqiConfigs['article']['minsearchtime']));
    }
    $_SESSION['jieqiArticlesearchTime'] = JIEQI_NOW_TIME;
    $jieqi_visit_time['jieqiArticlesearchTime'] = JIEQI_NOW_TIME;
    setcookie('jieqiVisitTime', jieqi_sarytostr($jieqi_visit_time), JIEQI_NOW_TIME + 3600, '/', JIEQI_COOKIE_DOMAIN, 0);
}
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
if (is_numeric($_REQUEST['searchkey'])) {
    $article = $article_handler->get($_REQUEST['searchkey']);
    if (is_object($article)) {
        $url_articleinfo = jieqi_geturl('article', 'article', $article->getVar('articleid'), 'info', $article->getVar('articlecode', 'n'));
        header('Location: ' . jieqi_headstr($url_articleinfo));
        jieqi_freeresource();
        exit;
    }
}
$stypeary = array('all' => 0, 'articlename' => 1, 'author' => 2, 'keywords' => 4);
if (!isset($_REQUEST['searchtype']) || !isset($stypeary[$_REQUEST['searchtype']])) {
    $_REQUEST['searchtype'] = 'articlename';
}
$intstype = intval($stypeary[$_REQUEST['searchtype']]);
$_REQUEST['searchkey'] = str_replace('&', ' ', $_REQUEST['searchkey']);
$searchstring = $_REQUEST['searchkey'] . '&&' . $_REQUEST['searchtype'];
$hashid = md5($searchstring);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/searchresult.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once $jieqiModules['article']['path'] . '/class/searchcache.php';
$searchcache_handler = JieqiSearchcacheHandler::getInstance('JieqiSearchcacheHandler');
$criteria = new CriteriaCompo(new Criteria('hashid', $hashid, '='));
$criteria->setLimit(1);
$criteria->setStart(0);
$searchcache_handler->queryObjects($criteria);
$searchcache = $searchcache_handler->getObject();
$usecache = false;
if (is_object($searchcache)) {
    if ($searchcache->getVar('results', 'n') == 1) {
        $cachetime = 86400;
    } else {
        if ($searchcache->getVar('results', 'n') == 0) {
            $cachetime = 1800;
        } else {
            $cachetime = 7200;
        }
    }
    if (JIEQI_NOW_TIME - $searchcache->getVar('uptime') < $cachetime) {
        $usecache = true;
    }
}
if ($usecache) {
    $allresults = $searchcache->getVar('results', 'n');
    $aids = $searchcache->getVar('aids', 'n');
    if (empty($aids)) {
        $aids = 0;
    } else {
        if ($allresults == 1) {
            $aids = intval($aids);
        } else {
            $aids = trim($aids);
        }
    }
    if ($jieqiPset['rows'] < $allresults) {
        $maxpage = ceil($allresults / $jieqiPset['rows']);
        if ($maxpage < $_REQUEST['page']) {
            $_REQUEST['page'] = $maxpage;
            $jieqiPset['page'] = $_REQUEST['page'];
            $jieqiPset['start'] = ($jieqiPset['page'] - 1) * $jieqiPset['rows'];
        }
        if ($maxpage <= $jieqiPset['page']) {
            $rescount = $allresults % $jieqiPset['rows'];
        } else {
            $rescount = $jieqiPset['rows'];
        }
    } else {
        $_REQUEST['page'] = 1;
        $jieqiPset['page'] = 1;
        $jieqiPset['start'] = 0;
        $rescount = $allresults;
    }
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE articleid IN (' . jieqi_dbslashes($aids) . ') ORDER BY lastupdate DESC LIMIT ' . $jieqiPset['start'] . ', ' . $jieqiPset['rows'];
    $res = $article_handler->execute($sql);
    $truecount = $article_handler->db->getRowsNum($res);
}
if (!$usecache) {
    $_REQUEST['page'] = 1;
    $jieqiPset['page'] = 1;
    $jieqiPset['start'] = 0;
    $jieqiConfigs['article']['maxsearchres'] = intval($jieqiConfigs['article']['maxsearchres']);
    if (empty($jieqiConfigs['article']['maxsearchres'])) {
        $jieqiConfigs['article']['maxsearchres'] = 200;
    }
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE display = 0 AND words > 0';
    if (!empty($_REQUEST['searchkey'])) {
        if ($jieqiConfigs['article']['searchtype'] == 1) {
            $tmpvar = 'LIKE \'' . jieqi_dbslashes($_REQUEST['searchkey']) . '%\'';
        } else {
            if ($jieqiConfigs['article']['searchtype'] == 2) {
                $tmpvar = '= \'' . jieqi_dbslashes($_REQUEST['searchkey']) . '\'';
            } else {
                $tmpvar = 'LIKE \'%' . jieqi_dbslashes($_REQUEST['searchkey']) . '%\'';
            }
        }
        if ($_REQUEST['searchtype'] == 'all') {
            $sql .= ' AND (articlename ' . $tmpvar . ' OR author ' . $tmpvar . ' OR keywords ' . $tmpvar . ')';
        } else {
            $sql .= ' AND ' . $_REQUEST['searchtype'] . ' ' . $tmpvar;
        }
    }
    $sql .= ' ORDER BY lastupdate DESC LIMIT 0, ' . $jieqiConfigs['article']['maxsearchres'];
    $res = $article_handler->execute($sql);
    $allresults = $article_handler->db->getRowsNum($res);
    if ($allresults <= $jieqiPset['rows']) {
        $rescount = $allresults;
    } else {
        $rescount = $jieqiPset['rows'];
    }
}
if ($rescount == 1 && (empty($_REQUEST['page']) || $_REQUEST['page'] <= 1)) {
    $article = $article_handler->getObject();
    if (!is_object($article)) {
        jieqi_printfail($jieqiLang['article']['no_search_result']);
    }
    $url_articleinfo = jieqi_geturl('article', 'article', $article->getVar('articleid'), 'info', $article->getVar('articlecode', 'n'));
    header('Location: ' . jieqi_headstr($url_articleinfo));
    jieqi_article_upscache($article->getVar('articleid'));
    jieqi_freeresource();
    exit;
} else {
    include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
    $jieqiTpl->assign('article_static_url', $article_static_url);
    $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
    $jieqiTpl->assign('resultcount', $rescount);
    $jieqiTpl->assign('articletitle', $jieqiLang['article']['search_result']);
    $articlerows = array();
    $k = 0;
    $aidary = array();
    while ($v = $article_handler->getObject()) {
        $aidary[$k] = $v->getVar('articleid', 'n');
        $articlerows[$k] = jieqi_article_vars($v);
        $searchkey_html = jieqi_htmlstr($_REQUEST['searchkey']);
        $articlerows[$k]['articlename_hl'] = $_REQUEST['searchtype'] == 'articlename' || $_REQUEST['searchtype'] == 'all' ? str_replace($searchkey_html, '<span class="hot">' . $searchkey_html . '</span>', $articlerows[$k]['articlename']) : $articlerows[$k]['articlename'];
        $articlerows[$k]['author_hl'] = $_REQUEST['searchtype'] == 'author' || $_REQUEST['searchtype'] == 'all' ? str_replace($searchkey_html, '<span class="hot">' . $searchkey_html . '</span>', $articlerows[$k]['author']) : $articlerows[$k]['author'];
        $articlerows[$k]['keywords_hl'] = $_REQUEST['searchtype'] == 'keywords' || $_REQUEST['searchtype'] == 'all' ? str_replace($searchkey_html, '<span class="hot">' . $searchkey_html . '</span>', $articlerows[$k]['keywords']) : $articlerows[$k]['keywords'];
        $k++;
        if ($jieqiPset['rows'] <= $k) {
            break;
        }
    }
    $jieqiTpl->assign_by_ref('articlerows', $articlerows);
    if (!$usecache) {
        while ($v = $article_handler->getObject()) {
            $aidary[$k] = $v->getVar('articleid', 'n');
            $k++;
        }
    }
    jieqi_article_upscache($aidary);
    $jieqiTpl->assign('searchkey', jieqi_htmlstr($_REQUEST['searchkey']));
    $jieqiTpl->assign('searchtype', jieqi_htmlstr($_REQUEST['searchtype']));
    $jieqiTpl->assign('allresults', jieqi_htmlstr($allresults));
    include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
    if (!empty($jieqiConfigs['article']['maxsearchres']) && intval($jieqiConfigs['article']['maxsearchres']) < $allresults) {
        $allresults = intval($jieqiConfigs['article']['maxsearchres']);
    }
    if ($_REQUEST['page'] != $jieqiPset['page']) {
        $jieqiPset['page'] = $_REQUEST['page'];
        $jieqiPset['start'] = ($jieqiPset['page'] - 1) * $jieqiPset['rows'];
    }
    $jieqiPset['count'] = $allresults;
    $jumppage = new JieqiPage($jieqiPset);
    $jumppage->setlink(jieqi_geturl('article', 'search', 0, $_REQUEST['searchkey'], $_REQUEST['searchtype']));
    $jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
    $jieqiTpl->setCaching(0);
    include_once JIEQI_ROOT_PATH . '/footer.php';
}