<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['setwriter'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('applywriter', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (!empty($_POST['act'])) {
    jieqi_checkpost();
}
include_once $jieqiModules['article']['path'] . '/class/applywriter.php';
$apply_handler = JieqiApplywriterHandler::getInstance('JieqiApplywriterHandler');
if (isset($_POST['act']) && !empty($_REQUEST['id'])) {
    $apply = $apply_handler->get($_REQUEST['id']);
    if (!is_object($apply)) {
        jieqi_printfail($jieqiLang['article']['apply_not_exists']);
    }
    switch ($_POST['act']) {
        case 'confirm':
            $apply->setVar('authtime', JIEQI_NOW_TIME);
            $apply->setVar('authuid', $_SESSION['jieqiUserId']);
            $apply->setVar('authname', $_SESSION['jieqiUserName']);
            $apply->setVar('applyflag', 1);
            $apply_handler->insert($apply);
            include_once JIEQI_ROOT_PATH . '/class/groups.php';
            $groupid = array_search($jieqiConfigs['article']['writergroup'], $jieqiGroups);
            if ($groupid == false) {
                jieqi_printfail($jieqiLang['article']['no_writer_group']);
            } else {
                if ($groupid == JIEQI_GROUP_ADMIN) {
                    jieqi_printfail($jieqiLang['article']['no_writer_admin']);
                }
            }
            include_once JIEQI_ROOT_PATH . '/class/users.php';
            $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
            $jieqiUsers = $users_handler->get($apply->getVar('applyuid', 'n'));
            if (is_object($jieqiUsers)) {
                $jieqiUsers->setVar('groupid', $groupid);
                $users_handler->insert($jieqiUsers);
                jieqi_includedb();
                $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
                $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_online') . ' WHERE uid = ' . intval($jieqiUsers->getVar('uid', 'n')) . ' ORDER BY updatetime DESC LIMIT 0, 1';
                $query->execute($sql);
                $row = $query->getRow();
                if (is_array($row)) {
                    jieqi_upusersession($row['sid'], array('jieqiUserGroup' => $groupid));
                }
            }
            include_once JIEQI_ROOT_PATH . '/include/funmessage.php';
            jieqi_sendmessage(array('toid' => $apply->getVar('applyuid', 'n'), 'toname' => $apply->getVar('applyname', 'n'), 'title' => $jieqiLang['article']['apply_confirm_title'], 'content' => $jieqiLang['article']['apply_confirm_text'], 'messagetype' => 11));
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'refuse':
            $apply->setVar('authtime', JIEQI_NOW_TIME);
            $apply->setVar('authuid', $_SESSION['jieqiUserId']);
            $apply->setVar('authname', $_SESSION['jieqiUserName']);
            $apply->setVar('applyflag', 2);
            $apply_handler->insert($apply);
            include_once JIEQI_ROOT_PATH . '/include/funmessage.php';
            jieqi_sendmessage(array('toid' => $apply->getVar('applyuid', 'n'), 'toname' => $apply->getVar('applyname', 'n'), 'title' => $jieqiLang['article']['apply_refuse_title'], 'content' => $jieqiLang['article']['apply_refuse_text'], 'messagetype' => 12));
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'delete':
            $apply_handler->delete($_REQUEST['id']);
            jieqi_jumppage($jieqiModules['article']['url'] . '/admin/applylist.php', '', '', true);
            break;
    }
    unset($criteria);
} else {
    if (isset($_POST['act']) && $_POST['act'] == 'batchdel' && is_array($_REQUEST['applyid']) && 0 < count($_REQUEST['applyid'])) {
        $where = '';
        foreach ($_REQUEST['applyid'] as $v) {
            if (is_numeric($v)) {
                $v = intval($v);
                if (!empty($where)) {
                    $where .= ', ';
                }
                $where .= $v;
            }
        }
        if (!empty($where)) {
            $sql = 'DELETE FROM ' . jieqi_dbprefix('article_applywriter') . ' WHERE applyid IN (' . $where . ')';
            $apply_handler->execute($sql);
        }
        jieqi_jumppage($jieqiModules['article']['url'] . '/admin/applylist.php', '', '', true);
    }
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/applylist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
$criteria = new CriteriaCompo();
if (!isset($_REQUEST['display'])) {
    $_REQUEST['display'] = '';
}
switch ($_REQUEST['display']) {
    case 'ready':
        $criteria->add(new Criteria('applyflag', 0));
        break;
    case 'success':
        $criteria->add(new Criteria('applyflag', 1));
        break;
    case 'failure':
        $criteria->add(new Criteria('applyflag', 2));
        break;
}
$criteria->setSort('applyid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$apply_handler->queryObjects($criteria);
$applyrows = array();
$k = 0;
while ($v = $apply_handler->getObject()) {
    $applyrows[$k] = jieqi_query_rowvars($v);
    $applyrows[$k]['applysize_c'] = $v->getVar('applywords');
    if ($applyrows[$k]['applyflag'] == 2) {
        $applyrows[$k]['authstatus'] = $jieqiLang['article']['apply_status_failure'];
    } else {
        if ($applyrows[$k]['applyflag'] == 1) {
            $applyrows[$k]['authstatus'] = $jieqiLang['article']['apply_status_success'];
        } else {
            $applyrows[$k]['authstatus'] = $jieqiLang['article']['apply_status_ready'];
        }
    }
    $k++;
}
$jieqiTpl->assign_by_ref('applyrows', $applyrows);
$jieqiTpl->assign('url_jump', jieqi_addurlvars(array()));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $apply_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$pagelink = '';
if (!empty($_REQUEST['display'])) {
    if (empty($pagelink)) {
        $pagelink .= '?';
    } else {
        $pagelink .= '&';
    }
    $pagelink .= 'display=' . urlencode($_REQUEST['display']);
}
if (empty($pagelink)) {
    $pagelink .= '?page=';
} else {
    $pagelink .= '&page=';
}
$jumppage->setlink($jieqiModules['article']['url'] . '/admin/applylist.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';