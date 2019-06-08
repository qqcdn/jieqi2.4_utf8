<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_checklogin();
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/buylog.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_getconfigs('obook', 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
include_once $jieqiModules['obook']['path'] . '/class/obuyinfo.php';
$obuyinfo_handler = JieqiObuyinfoHandler::getInstance('JieqiObuyinfoHandler');
$criteria = new CriteriaCompo(new Criteria('userid', $_SESSION['jieqiUserId']));
if (!empty($_REQUEST['oid'])) {
    $criteria->add(new Criteria('obookid', intval($_REQUEST['oid'])));
} else {
    if (!empty($_REQUEST['aid'])) {
        $criteria->add(new Criteria('articleid', intval($_REQUEST['aid'])));
    } else {
        if (!empty($_REQUEST['oname'])) {
            $criteria->add(new Criteria('obookname', $_REQUEST['oname']));
        } else {
            if (!empty($_REQUEST['aname'])) {
                $criteria->add(new Criteria('obookname', $_REQUEST['aname']));
            }
        }
    }
}
$criteria->setSort('obuyinfoid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$obuyinfo_handler->queryObjects($criteria);
$obuyinforows = array();
$k = 0;
$articleid = 0;
$obookid = 0;
$obookname = '';
while ($v = $obuyinfo_handler->getObject()) {
    $obuyinforows[$k] = jieqi_query_rowvars($v, 's');
    if ($obookname == '' && (!empty($_REQUEST['oid']) || !empty($_REQUEST['aid']) || !empty($_REQUEST['oname']) || !empty($_REQUEST['aname']))) {
        $articleid = $obuyinforows[$k]['articleid'];
        $obookid = $obuyinforows[$k]['obookid'];
        $obookname = $obuyinforows[$k]['obookname'];
    }
    $k++;
}
$jieqiTpl->assign_by_ref('obuyinforows', $obuyinforows);
$jieqiTpl->assign_by_ref('articleid', $articleid);
$jieqiTpl->assign_by_ref('obookid', $obookid);
$jieqiTpl->assign_by_ref('obookname', $obookname);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $obuyinfo_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$pagelink = '';
if (!empty($_REQUEST['oid'])) {
    if (empty($pagelink)) {
        $pagelink .= '?';
    } else {
        $pagelink .= '&';
    }
    $pagelink .= 'oid=' . urlencode($_REQUEST['oid']);
} else {
    if (!empty($_REQUEST['aid'])) {
        if (empty($pagelink)) {
            $pagelink .= '?';
        } else {
            $pagelink .= '&';
        }
        $pagelink .= 'aid=' . urlencode($_REQUEST['aid']);
    } else {
        if (!empty($_REQUEST['oname'])) {
            if (empty($pagelink)) {
                $pagelink .= '?';
            } else {
                $pagelink .= '&';
            }
            $pagelink .= 'oname=' . urlencode($_REQUEST['oname']);
        } else {
            if (!empty($_REQUEST['aname'])) {
                if (empty($pagelink)) {
                    $pagelink .= '?';
                } else {
                    $pagelink .= '&';
                }
                $pagelink .= 'aname=' . urlencode($_REQUEST['aname']);
            }
        }
    }
}
if (empty($pagelink)) {
    $pagelink .= '?page=';
} else {
    $pagelink .= '&page=';
}
$jumppage->setlink($obook_dynamic_url . '/buylog.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';