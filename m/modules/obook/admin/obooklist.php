<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('list', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
include_once $jieqiModules['obook']['path'] . '/include/funobook.php';
include_once $jieqiModules['obook']['path'] . '/include/actobook.php';
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/admin/obooklist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
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
            if ($_REQUEST['keytype'] == 3) {
                $criteria->add(new Criteria('agent', $_REQUEST['keyword'], '='));
            } else {
                $criteria->add(new Criteria('obookname', $_REQUEST['keyword'], '='));
            }
        }
    }
}
if (!empty($_REQUEST['class'])) {
    $criteria->add(new Criteria('sortid', $_REQUEST['class'], '='));
    $obooktitle = $jieqiSort['article'][$_REQUEST['class']]['caption'];
} else {
    $obooktitle = $jieqiLang['obook']['all_obook_title'];
    switch ($_REQUEST['display']) {
        case 'self':
            $criteria->add(new Criteria('siteid', 0, '='));
            $obooktitle = $jieqiLang['obook']['local_obook_title'];
            break;
    }
}
$jieqiTpl->assign('obooktitle', $obooktitle);
$jieqiTpl->assign('url_obook', $obook_dynamic_url . '/admin/obooklist.php');
$criteria->setSort('obookid');
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
$jumppage->setlink($obook_dynamic_url . '/admin/obooklist.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';