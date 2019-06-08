<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminmessage'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('message', JIEQI_MODULE_NAME);
jieqi_getconfigs('system', 'configs');
if (empty($_REQUEST['page']) || !is_numeric($_REQUEST['page'])) {
    $_REQUEST['page'] = 1;
}
include_once JIEQI_ROOT_PATH . '/class/message.php';
$message_handler = JieqiMessageHandler::getInstance('JieqiMessageHandler');
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
                    $where = 'messageid = ' . $idary[0];
                } else {
                    $where = 'messageid IN (' . implode(',', $idary) . ')';
                }
            }
            break;
        default:
            jieqi_printfail(LANG_ERROR_PARAMETER);
            break;
    }
    if (!empty($where)) {
        $sql = 'DELETE FROM ' . jieqi_dbprefix('system_message') . ' WHERE ' . $where;
        $message_handler->execute($sql);
    }
    jieqi_jumppage(JIEQI_URL . '/admin/messagelist.php', '', '', true);
    exit;
}
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/messagelist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('url_action', JIEQI_URL . '/admin/message.php');
$jieqiTpl->assign('url_delete', JIEQI_URL . '/admin/message.php?checkaction=2');
$messagerows = array();
$criteria = new CriteriaCompo();
if (isset($_REQUEST['keyword']) && 0 < strlen($_REQUEST['keyword'])) {
    $_REQUEST['keyword'] = trim($_REQUEST['keyword']);
    switch ($_REQUEST['keytype']) {
        case 'fromname':
            $criteria->add(new Criteria('fromname', $_REQUEST['keyword']));
            break;
        case 'toname':
            $criteria->add(new Criteria('toname', $_REQUEST['keyword']));
            break;
        case 'title':
            $criteria->add(new Criteria('title', '%' . $_REQUEST['keyword'] . '%', 'LIKE'));
            break;
    }
}
$criteria->setSort('messageid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$message_handler->queryObjects($criteria);
$k = 0;
while ($v = $message_handler->getObject()) {
    $messagerows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign('messagerows', $messagerows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $message_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';