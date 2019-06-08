<?php

function jieqi_article_joinobook_replace($sql)
{
    $freeorders = array('display', 'fullflag', 'siteid', 'sourceid', 'sortid', 'typeid', 'rgroup', 'original', 'authorid', 'progress', 'isvip', 'issign', 'articleid', 'initial', 'lastupdate', 'infoupdate', 'freetime', 'viptime', 'postdate', 'toptime', 'goodnum', 'reviewsnum', 'ratenum', 'ratesum', 'allwords', 'monthwords', 'weekwords', 'daywords', 'allvipvote', 'monthvipvote', 'weekvipvote', 'dayvipvote', 'previpvote', 'allvisit', 'monthvisit', 'weekvisit', 'dayvisit', 'allvote', 'monthvote', 'weekvote', 'dayvote', 'allflower', 'monthflower', 'weekflower', 'dayflower', 'allegg', 'monthegg', 'weekegg', 'dayegg', 'alldown', 'monthdown', 'weekdown', 'daydown');
    $viporders = array('lastsale', 'allsale', 'monthsale', 'weeksale', 'daysale', 'normalsale', 'vipsale', 'freesale', 'bespsale', 'totalsale', 'sumegold', 'sumesilver', 'sumtip', 'sumhurry', 'sumbesp', 'sumaward', 'sumagent', 'sumgift', 'sumother', 'sumemoney', 'summoney');
    $orderfrom = array();
    $orderto = array();
    foreach ($freeorders as $order) {
        $orderfrom[] = $order;
        $orderto[] = 'a.' . $order;
    }
    foreach ($viporders as $order) {
        $orderfrom[] = $order;
        $orderto[] = 'o.' . $order;
    }
    $sql = preg_replace('/(^|[^a-z])words($|[^a-z])/i', '$1allwords$2', $sql);
    $sql = str_replace($orderfrom, $orderto, $sql);
    $sql = str_replace('allwords', 'words', $sql);
    return $sql;
}
define('JIEQI_MODULE_NAME', 'article');
if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
jieqi_loadlang('list', JIEQI_MODULE_NAME);
jieqi_getconfigs('article', 'configs');
jieqi_getconfigs('article', 'sort');
jieqi_getconfigs('article', 'top');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/toplist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
if (!isset($_GET['order']) && isset($_GET['sort'])) {
    $_GET['order'] = $_GET['sort'];
}
if (empty($_GET['order']) || !isset($jieqiTop['article'][$_GET['order']])) {
    $_GET['order'] = '';
    foreach ($jieqiTop['article'] as $k => $v) {
        if ($_GET['order'] == '') {
            $_GET['order'] = $k;
        }
        if (0 < $v['default']) {
            $_GET['order'] = $k;
            break;
        }
    }
}
if (empty($_GET['order'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (!isset($jieqiTop['article'][$_GET['order']]['title'])) {
    $jieqiTop['article'][$_GET['order']]['title'] = $jieqiTop['article'][$_GET['order']]['caption'];
}
$jieqiTpl->assign('ordername', jieqi_htmlstr($jieqiTop['article'][$_GET['order']]['title']));
$jieqiTpl->assign('order', $_GET['order']);
$orderfield = trim($jieqiTop['article'][$_GET['order']]['sort']);
$jieqiTpl->assign('orderfield', $orderfield);
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
include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
$jieqiTset['jieqi_contents_cacheid'] = $_GET['order'] . '_' . $int_sortid . '_' . $_GET['fullflag'];
$pagecacheid = $jieqiTset['jieqi_contents_cacheid'];
$jieqiTset['jieqi_contents_cacheid'] .= '_' . $_REQUEST['page'];
if (!isset($jieqiConfigs['article']['topcachenum'])) {
    $jieqiConfigs['article']['topcachenum'] = $jieqiConfigs['article']['cachenum'];
}
$content_used_cache = false;
if (JIEQI_USE_CACHE && $_REQUEST['page'] <= $jieqiConfigs['article']['topcachenum']) {
    $jieqiTpl->setCaching(1);
    $jieqiTpl->setCachType(1);
    if ($jieqiTpl->is_cached($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_cacheid'], NULL, NULL, NULL, false)) {
        if (in_array($jieqiTop['article'][$_GET['order']]['sort'], array('lastupdate', 'postdate'))) {
            $uptime = jieqi_article_getuptime();
            $cachedtime = $jieqiTpl->get_cachedtime($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_cacheid']);
            if ($uptime < $cachedtime || JIEQI_NOW_TIME - $cachedtime < 15) {
                $content_used_cache = true;
            } else {
                $jieqiTpl->setCaching(2);
            }
        } else {
            $content_used_cache = true;
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
    $jieqiTpl->assign_by_ref('toprows', $jieqiTop['article']);
    include_once $jieqiModules['article']['path'] . '/class/article.php';
    $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
    $topsql = ' WHERE display = 0 AND words > 0';
    if (!empty($_GET['fullflag']) && is_numeric($_GET['fullflag'])) {
        $topsql .= ' AND fullflag = ' . $_GET['fullflag'];
        if ($articletitle != '') {
            $articletitle .= ' - ';
        }
        $articletitle .= $jieqiLang['article']['list_full_title'];
    }
    if (!empty($_GET['order'])) {
        if ($articletitle != '') {
            $articletitle .= ' - ';
        }
        $articletitle .= $jieqiTop['article'][$_GET['order']]['caption'];
    }
    if (!empty($int_sortid) && is_numeric($int_sortid)) {
        $topsql .= ' AND sortid = ' . $int_sortid;
        if ($articletitle != '') {
            $articletitle .= ' - ';
        }
        $articletitle .= $jieqiSort['article'][$int_sortid]['caption'];
    }
    if ($articletitle == '') {
        $articletitle = $jieqiLang['article']['list_article_title'];
    }
    $jieqiTpl->assign('articletitle', $articletitle);
    $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
    $daystart = mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2], (int) $tmpvar[0]);
    $monthstart = mktime(0, 0, 0, (int) $tmpvar[1], 1, (int) $tmpvar[0]);
    $monthdays = date('t', $monthstart);
    $prestart = mktime(0, 0, 0, (int) $tmpvar[1] - 1, 1, (int) $tmpvar[0]);
    $predays = date('t', $prestart);
    $tmpvar = date('w', JIEQI_NOW_TIME);
    if ($tmpvar == 0) {
        $tmpvar = 7;
    }
    $weekstart = $daystart;
    if (1 < $tmpvar) {
        $weekstart -= ($tmpvar - 1) * 86400;
    }
    $repfrom = array('<{$daystart}>', '<{$weekstart}>', '<{$monthstart}>', '<{$prestart}>', '<{$monthdays}>', '<{$predays}>');
    $repto = array($daystart, $weekstart, $monthstart, $prestart, $monthdays, $predays);
    if ($jieqiTop['article'][$_GET['order']]['where'] != '') {
        $topsql .= ' AND ' . str_replace($repfrom, $repto, $jieqiTop['article'][$_GET['order']]['where']);
    }
    if (strtoupper($jieqiTop['article'][$_GET['order']]['order']) == 'ASC' || strtoupper($jieqiTop['article'][$_GET['order']]['order']) == 'DESC') {
        $jieqiTop['article'][$_GET['order']]['order'] = $jieqiTop['article'][$_GET['order']]['sort'] . ' ' . $jieqiTop['article'][$_GET['order']]['order'];
    }
    if (!empty($jieqiTop['article'][$_GET['order']]['isvip'])) {
        $cotsql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('obook_obook') . ' o LEFT JOIN ' . jieqi_dbprefix('article_article') . ' a ON o.articleid = a.articleid' . jieqi_article_joinobook_replace($topsql);
        if ($jieqiTop['article'][$_GET['order']]['order'] != '') {
            $topsql .= ' ORDER BY ' . $jieqiTop['article'][$_GET['order']]['order'];
        }
        $topsql = jieqi_article_joinobook_replace($topsql);
        $topsql .= ' LIMIT ' . intval($jieqiPset['start']) . ', ' . intval($jieqiPset['rows']);
        $topsql = 'SELECT *, ' . jieqi_article_joinobook_replace($jieqiTop['article'][$_GET['order']]['sort']) . ' AS ordervalue FROM ' . jieqi_dbprefix('obook_obook') . ' o LEFT JOIN ' . jieqi_dbprefix('article_article') . ' a ON o.articleid = a.articleid' . $topsql;
    } else {
        $cotsql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_article') . $topsql;
        if ($jieqiTop['article'][$_GET['order']]['order'] != '') {
            $topsql .= ' ORDER BY ' . $jieqiTop['article'][$_GET['order']]['order'];
        }
        $topsql .= ' LIMIT ' . intval($jieqiPset['start']) . ', ' . intval($jieqiPset['rows']);
        $topsql = 'SELECT *, ' . $jieqiTop['article'][$_GET['order']]['sort'] . ' AS ordervalue FROM ' . jieqi_dbprefix('article_article') . $topsql;
    }
    $article_handler->execute($topsql);
    $articlerows = array();
    $k = 0;
    while ($v = $article_handler->getObject()) {
        $articlerows[$k] = jieqi_article_vars($v);
        if (is_numeric($articlerows[$k]['ordervalue'])) {
            $articlerows[$k]['ordervalue'] = round($articlerows[$k]['ordervalue']);
        }
        $wordsary = array('words', 'freewords', 'vipwords', 'monthwords', 'prewords', 'weekwords', 'daywords');
        if (in_array($orderfield, $wordsary)) {
            $articlerows[$k]['ordervalue_n'] = $articlerows[$k]['ordervalue'];
            $articlerows[$k]['ordervalue'] = $articlerows[$k]['ordervalue'];
            $articlerows[$k]['ordertype'] = 'words';
        } else {
            if ($orderfield == 'lastupdate' || $orderfield == 'postdate' || $orderfield == 'toptime') {
                $articlerows[$k]['ordervalue_n'] = $articlerows[$k]['ordervalue'];
                $articlerows[$k]['ordervalue'] = date('Y-m-d', $articlerows[$k]['ordervalue']);
                $articlerows[$k]['ordertype'] = 'date';
            } else {
                $articlerows[$k]['ordertype'] = 'int';
            }
        }
        $k++;
    }
    $jieqiTpl->assign_by_ref('articlerows', $articlerows);
    include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
    if (JIEQI_USE_CACHE) {
        jieqi_getcachevars('article', 'toplistlog');
        if (!is_array($jieqiToplistlog)) {
            $jieqiToplistlog = array();
        }
        if (!isset($jieqiToplistlog[$pagecacheid]) || JIEQI_CACHE_LIFETIME < JIEQI_NOW_TIME - $jieqiToplistlog[$pagecacheid]['time']) {
            $article_handler->execute($cotsql);
            $row = $article_handler->getRow();
            $jieqiToplistlog[$pagecacheid] = array('rows' => intval($row['cot']), 'time' => JIEQI_NOW_TIME);
            jieqi_setcachevars('toplistlog', 'jieqiToplistlog', $jieqiToplistlog, 'article');
        }
        $toplistrows = $jieqiToplistlog[$pagecacheid]['rows'];
    } else {
        $article_handler->execute($cotsql);
        $row = $article_handler->getRow();
        $toplistrows = intval($row['cot']);
    }
    $jieqiPset['count'] = $toplistrows;
    $jumppage = new JieqiPage($jieqiPset);
    if (!$use_sortcode) {
        $jumppage->setlink(jieqi_geturl('article', 'toplist', 0, $_GET['order'], $_GET['sortid'], $_GET['fullflag']));
    } else {
        $jumppage->setlink(jieqi_geturl('article', 'toplist', 0, $_GET['order'], $_GET['sortcode'], $_GET['fullflag']));
    }
    $jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
}
include_once JIEQI_ROOT_PATH . '/footer.php';