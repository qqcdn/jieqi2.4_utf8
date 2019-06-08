<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_loadlang('list', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('article', 'sort');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/obooklist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once $jieqiModules['obook']['path'] . '/include/funobook.php';
jieqi_getconfigs('system', 'besp');
if (empty($_REQUEST['sort'])) {
    $_REQUEST['sort'] = 'lastupdate';
}
if (empty($_REQUEST['class']) || !is_numeric($_REQUEST['class']) || !isset($jieqiSort['article'][$_REQUEST['class']])) {
    $_REQUEST['class'] = 0;
}
if (!isset($_REQUEST['besp']) || 0 < strlen($_REQUEST['besp']) && $_REQUEST['besp'] != 0 && !isset($jieqiBesp[$_REQUEST['besp']])) {
    $_REQUEST['besp'] = '';
} else {
    $_REQUEST['besp'] = intval($_REQUEST['besp']);
}
$content_used_cache = false;
$jieqiTset['jieqi_contents_cacheid'] = 'obooklist_' . $_REQUEST['sort'] . '_' . $_REQUEST['class'] . '_' . $_REQUEST['besp'] . '_' . $_REQUEST['page'];
if (JIEQI_USE_CACHE && $_REQUEST['page'] <= $jieqiConfigs['obook']['topcachenum']) {
    $jieqiTpl->setCaching(1);
    $jieqiTpl->setCachType(1);
    if ($jieqiTpl->is_cached($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_cacheid'], NULL, NULL, NULL, false)) {
        if (in_array($_REQUEST['sort'], array('lastupdate', 'postdate'))) {
            $uptime = jieqi_obook_getuptime();
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
if (!$content_used_cache) {
    $obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
    $obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
    $jieqiTpl->assign('obook_static_url', $obook_static_url);
    $jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
    if (jieqi_getconfigs('article', 'configs')) {
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
    }
    include_once $jieqiModules['obook']['path'] . '/class/obook.php';
    $obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
    $criteria = new CriteriaCompo(new Criteria('display', '0', '='));
    if (!empty($_REQUEST['class'])) {
        $criteria->add(new Criteria('sortid', $_REQUEST['class'], '='));
        $classinfo = ' - ' . $jieqiSort['article'][$_REQUEST['class']]['caption'];
    } else {
        $classinfo = '';
    }
    if (0 < strlen($_REQUEST['monthly'])) {
        $criteria->add(new Criteria('monthly', $_REQUEST['monthly'], '='));
    }
    $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
    $daystart = mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2], (int) $tmpvar[0]);
    $monthstart = mktime(0, 0, 0, (int) $tmpvar[1], 1, (int) $tmpvar[0]);
    $tmpvar = date('w', JIEQI_NOW_TIME);
    if ($tmpvar == 0) {
        $tmpvar = 7;
    }
    $weekstart = $daystart;
    if (1 < $tmpvar) {
        $weekstart -= ($tmpvar - 1) * 86400;
    }
    switch ($_REQUEST['sort']) {
        case 'allsale':
            $criteria->setSort('allsale');
            $jieqiTpl->assign('obooktitle', sprintf($jieqiLang['obook']['top_allvisit_title'], $classinfo));
            break;
        case 'monthsale':
            $criteria->add(new Criteria('lastsale', $monthstart, '>='));
            $criteria->setSort('monthsale');
            $jieqiTpl->assign('obooktitle', sprintf($jieqiLang['obook']['top_monthvisit_title'], $classinfo));
            break;
        case 'weeksale':
            $criteria->add(new Criteria('lastsale', $weekstart, '>='));
            $criteria->setSort('weeksale');
            $jieqiTpl->assign('obooktitle', sprintf($jieqiLang['obook']['top_weekvisit_title'], $classinfo));
            break;
        case 'daysale':
            $criteria->add(new Criteria('lastsale', $daystart, '>='));
            $criteria->setSort('daysale');
            $jieqiTpl->assign('obooktitle', sprintf($jieqiLang['obook']['top_dayvisit_title'], $classinfo));
            break;
        case 'postdate':
            $criteria->setSort('postdate');
            $jieqiTpl->assign('obooktitle', sprintf($jieqiLang['obook']['top_postdate_title'], $classinfo));
            break;
        case 'toptime':
            $criteria->setSort('toptime');
            $jieqiTpl->assign('obooktitle', sprintf($jieqiLang['obook']['top_toptime_title'], $classinfo));
            break;
        case 'goodnum':
            $criteria->setSort('goodnum');
            $jieqiTpl->assign('obooktitle', sprintf($jieqiLang['obook']['top_goodnum_title'], $classinfo));
            break;
        case 'lastupdate':
        default:
            $_REQUEST['sort'] = 'lastupdate';
            $criteria->setSort('lastupdate');
            $jieqiTpl->assign('obooktitle', sprintf($jieqiLang['obook']['top_lastupdate_title'], $classinfo));
            break;
    }
    $criteria->setOrder('DESC');
    $criteria->setLimit($jieqiPset['rows']);
    $criteria->setStart($jieqiPset['start']);
    $obook_handler->queryObjects($criteria);
    $obookrows = array();
    $k = 0;
    while ($v = $obook_handler->getObject()) {
        $obookrows[$k] = jieqi_obook_obookvars($v);
        $k++;
    }
    $jieqiTpl->assign_by_ref('obookrows', $obookrows);
    include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
    $jieqiPset['count'] = $obook_handler->getCount($criteria);
    $jumppage = new JieqiPage($jieqiPset);
    $pagelink = '';
    if (!empty($_REQUEST['class'])) {
        if (empty($pagelink)) {
            $pagelink .= '?';
        } else {
            $pagelink .= '&';
        }
        $pagelink .= 'class=' . urlencode($_REQUEST['class']);
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
    $jumppage->setlink($obook_dynamic_url . '/obooklist.php' . $pagelink, false, true);
    $jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
}
include_once JIEQI_ROOT_PATH . '/footer.php';