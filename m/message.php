<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_loadlang('message', JIEQI_MODULE_NAME);
jieqi_getconfigs('system', 'configs');
if (!isset($_REQUEST['box']) || $_REQUEST['box'] != 'outbox') {
    $_REQUEST['box'] = 'inbox';
}
include_once JIEQI_ROOT_PATH . '/class/message.php';
$message_handler = JieqiMessageHandler::getInstance('JieqiMessageHandler');
if (!empty($_POST['act']) && in_array($_POST['act'], array('delete', 'clear', 'read'))) {
    jieqi_checkpost();
    $where = '';
    switch ($_POST['act']) {
        case 'delete':
        case 'read':
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
        case 'clear':
            break;
        default:
            jieqi_printfail(LANG_ERROR_PARAMETER . '|' . $_POST['act']);
            break;
    }
    if (!empty($where)) {
        $where .= ' AND';
    }
    switch ($_REQUEST['box']) {
        case 'inbox':
            if (isset($_POST['act']) && $_POST['act'] == 'read') {
                $sql = 'UPDATE ' . jieqi_dbprefix('system_message') . ' SET isread = 1 WHERE ' . $where . ' toid = ' . $_SESSION['jieqiUserId'] . ' AND isread = 0';
                $message_handler->execute($sql);
            } else {
                $sql = 'UPDATE ' . jieqi_dbprefix('system_message') . ' SET todel = 1 WHERE ' . $where . ' toid = ' . $_SESSION['jieqiUserId'] . ' AND fromdel = 0';
                $message_handler->execute($sql);
                $sql = 'DELETE FROM ' . jieqi_dbprefix('system_message') . ' WHERE ' . $where . ' toid = ' . $_SESSION['jieqiUserId'] . ' AND fromdel = 1';
                $message_handler->execute($sql);
            }
            break;
        case 'outbox':
            $sql = 'UPDATE ' . jieqi_dbprefix('system_message') . ' SET fromdel = 1 WHERE ' . $where . ' fromid = ' . $_SESSION['jieqiUserId'] . ' AND todel = 0';
            $message_handler->execute($sql);
            $sql = 'DELETE FROM ' . jieqi_dbprefix('system_message') . ' WHERE ' . $where . ' fromid = ' . $_SESSION['jieqiUserId'] . ' AND todel = 1';
            $message_handler->execute($sql);
            break;
        default:
            jieqi_printfail(LANG_ERROR_PARAMETER);
            break;
    }
    jieqi_jumppage(JIEQI_URL . '/message.php?box=' . urlencode($_REQUEST['box']), '', '', true);
    exit;
}
jieqi_getconfigs('system', 'honors');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
$maxmessages = isset($jieqiConfigs['system']['maxmessages']) ? intval($jieqiConfigs['system']['maxmessages']) : 0;
$honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
if ($honorid && isset($jieqiRight['system']['maxmessages']['honors'][$honorid]) && is_numeric($jieqiRight['system']['maxmessages']['honors'][$honorid])) {
    $maxmessages = intval($jieqiRight['system']['maxmessages']['honors'][$honorid]);
}
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/' . $_REQUEST['box'] . '.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('box', $_REQUEST['box']);
$jieqiTpl->assign('url_action', JIEQI_URL . '/message.php?box=' . $_REQUEST['box']);
$sql = 'SELECT COUNT(*) AS msgnum FROM ' . jieqi_dbprefix('system_message') . ' WHERE (fromid=' . $_SESSION['jieqiUserId'] . ' AND fromdel=0) OR (toid=' . $_SESSION['jieqiUserId'] . ' AND todel=0)';
$res = $message_handler->execute($sql);
$row = $message_handler->getRow($res);
$msgnum = (int) $row['msgnum'];
$jieqiTpl->assign('maxmessage', $maxmessages);
$jieqiTpl->assign('nowmessage', $msgnum);
$messagerows = array();
switch ($_REQUEST['box']) {
    case 'inbox':
        $jieqiTpl->assign('boxname', $jieqiLang['system']['message_receive_box']);
        $jieqiTpl->assign('usertitle', $jieqiLang['system']['table_message_sender']);
        $criteria = new CriteriaCompo(new Criteria('toid', $_SESSION['jieqiUserId']));
        $criteria->add(new Criteria('todel', 0));
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
        if (isset($_SESSION['jieqiNewMessage']) && 0 < $_SESSION['jieqiNewMessage']) {
            $_SESSION['jieqiNewMessage'] = 0;
            $jieqi_user_info = array();
            if (!empty($_COOKIE['jieqiUserInfo'])) {
                $jieqi_user_info = jieqi_strtosary($_COOKIE['jieqiUserInfo']);
            } else {
                $jieqi_user_info = array();
            }
            if (isset($jieqi_user_info['jieqiNewMessage']) && 0 < $jieqi_user_info['jieqiNewMessage']) {
                $jieqi_user_info['jieqiNewMessage'] = 0;
            }
            if (!empty($jieqi_user_info['jieqiUserPassword'])) {
                $cookietime = JIEQI_NOW_TIME + 22118400;
            } else {
                $cookietime = 0;
            }
            @setcookie('jieqiUserInfo', jieqi_sarytostr($jieqi_user_info), $cookietime, '/', JIEQI_COOKIE_DOMAIN, 0);
        }
        break;
    case 'outbox':
        $jieqiTpl->assign('boxname', $jieqiLang['system']['message_send_box']);
        $jieqiTpl->assign('usertitle', $jieqiLang['system']['table_message_receiver']);
        $criteria = new CriteriaCompo(new Criteria('fromid', $_SESSION['jieqiUserId']));
        $criteria->add(new Criteria('fromdel', 0));
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
        break;
    default:
        break;
}
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';