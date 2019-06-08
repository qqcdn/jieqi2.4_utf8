<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/myfriends.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once JIEQI_ROOT_PATH . '/class/friends.php';
$friends_handler = JieqiFriendsHandler::getInstance('JieqiFriendsHandler');
if (!empty($_POST['act']) && in_array($_POST['act'], array('delete'))) {
    jieqi_checkpost();
    $where = '';
    switch ($_POST['act']) {
        case 'delete':
            if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id']) && !is_array($_REQUEST['id'])) {
                jieqi_printfail(LANG_ERROR_PARAMETER);
            }
            $idary = array();
            if (is_numeric($_REQUEST['id'])) {
                $idary[] = intval($_REQUEST['id']);
            } else {
                foreach ($_REQUEST['id'] as $v) {
                    if (is_numeric($v)) {
                        $idary[] = intval($v);
                    }
                }
            }
            if (empty($idary)) {
                jieqi_printfail(LANG_ERROR_PARAMETER);
            } else {
                if (count($idary) == 1) {
                    $where = 'yourid = ' . $idary[0];
                } else {
                    $where = 'yourid IN (' . implode(',', $idary) . ')';
                }
            }
            break;
        default:
            jieqi_printfail(LANG_ERROR_PARAMETER);
            break;
    }
    if (!empty($where)) {
        $where .= ' AND';
    }
    $sql = 'DELETE FROM ' . jieqi_dbprefix('system_friends') . ' WHERE ' . $where . ' myid = ' . $_SESSION['jieqiUserId'];
    $friends_handler->execute($sql);
    jieqi_jumppage(JIEQI_URL . '/myfriends.php', '', '', true);
    exit;
}
$criteria = new CriteriaCompo(new Criteria('myid', $_SESSION['jieqiUserId']));
$criteria->setSort('friendsid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$friends_handler->queryObjects($criteria);
$friendsrows = array();
$k = 0;
while ($v = $friends_handler->getObject()) {
    $friendsrows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('friendsrows', $friendsrows);
$friendsnum = $friends_handler->getCount($criteria);
$jieqiTpl->assign('nowfriends', $friendsnum);
jieqi_getconfigs('system', 'honors');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
$maxfriendsnum = intval($jieqiConfigs['system']['maxfriends']);
$honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
if ($honorid && isset($jieqiRight['system']['maxfriends']['honors'][$honorid]) && is_numeric($jieqiRight['system']['maxfriends']['honors'][$honorid])) {
    $maxfriendsnum = intval($jieqiRight['system']['maxfriends']['honors'][$honorid]);
}
$jieqiTpl->assign('maxfriends', $maxfriendsnum);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $friendsnum;
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';