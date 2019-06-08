<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_loadlang('list', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
include_once $jieqiModules['obook']['path'] . '/include/funochapter.php';
include_once $jieqiModules['obook']['path'] . '/class/ochapter.php';
$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/ochapterlist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
jieqi_getconfigs('article', 'sort');
if (empty($_REQUEST['class'])) {
    $_REQUEST['class'] = 0;
}
$criteria = new CriteriaCompo();
if (!empty($_REQUEST['keyword'])) {
    $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
    if ($_REQUEST['keytype'] == 1) {
        $criteria->add(new Criteria('author', $_REQUEST['keyword'], '='));
    } else {
        if ($_REQUEST['keytype'] == 2) {
            $criteria->add(new Criteria('poster', $_REQUEST['keyword'], '='));
        } else {
            $criteria->add(new Criteria('obookname', $_REQUEST['keyword'], '='));
        }
    }
}
if (!empty($_REQUEST['class'])) {
    $criteria->add(new Criteria('sortid', $_REQUEST['class'], '='));
    $obooktitle = $jieqiSort['article'][$_REQUEST['class']]['caption'];
} else {
    $obooktitle = $jieqiLang['obook']['title_all_obook'];
}
$jieqiTpl->assign('obooktitle', $obooktitle . $jieqiLang['obook']['title_new_vip']);
$criteria->add(new Criteria('chaptertype', 0, '='));
$criteria->add(new Criteria('display', 0, '='));
$criteria->setSort('lastupdate');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$ochapter_handler->queryObjects($criteria);
$ochapterrows = array();
$k = 0;
while ($v = $ochapter_handler->getObject()) {
    $ochapterrows[$k] = jieqi_obook_ochaptervars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('ochapterrows', $ochapterrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $ochapter_handler->getCount($criteria);
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
$jumppage->setlink($obook_dynamic_url . '/chapterlist.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';