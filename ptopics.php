<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
if (!isset($_REQUEST['uid']) && isset($_REQUEST['oid'])) {
    $_REQUEST['uid'] = $_REQUEST['oid'];
}
if ($_REQUEST['uid'] == 'self') {
    $_REQUEST['uid'] = intval($_SESSION['jieqiUserId']);
}
if (empty($_REQUEST['uid']) && empty($_REQUEST['oname'])) {
    if (!empty($_SESSION['jieqiUserId'])) {
        $_REQUEST['uid'] = $_SESSION['jieqiUserId'];
    } else {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_loadlang('parlar', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (jieqi_checkpower($jieqiPower['system']['parlorpost'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
    $enablepost = 1;
} else {
    $enablepost = 0;
}
jieqi_getconfigs('system', 'action', 'jieqiAction');
if (!empty($_POST['act'])) {
    jieqi_checkpost();
} else {
    $_POST['act'] = NULL;
}
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    if (empty($_POST['pcontent'])) {
        jieqi_printfail($jieqiLang['article']['review_need_pcontent']);
    }
    if (!$enablepost) {
        jieqi_printfail($jieqiLang['system']['parlor_noper_post']);
    }
    if (!empty($jieqiAction['system']['ptopic']['minscore']) && $_SESSION['jieqiUserScore'] < intval($jieqiAction['system']['ptopic']['minscore'])) {
        jieqi_printfail(sprintf($jieqiLang['system']['parlor_score_limit'], intval($jieqiAction['system']['ptopic']['minscore'])));
    }
}
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
if (!empty($_REQUEST['uid'])) {
    $owneruser = $users_handler->get($_REQUEST['uid']);
} else {
    $owneruser = $users_handler->getByname($_REQUEST['oname'], 2);
}
if (!$owneruser) {
    jieqi_printfail($jieqiLang['system']['owner_not_exists']);
}
$_REQUEST['uid'] = $owneruser->getVar('uid', 'n');
$owner_group = $owneruser->getVar('groupid', 'n');
$owner_status = $owner_group == JIEQI_GROUP_ADMIN ? JIEQI_GROUP_ADMIN : JIEQI_GROUP_USER;
if (!jieqi_checkpower($jieqiPower['system']['haveparlor'], $owner_status, $owner_group, true)) {
    jieqi_printfail($jieqiLang['system']['owner_no_parlor']);
}
$ownerid = $owneruser->getVar('uid', 'n');
include_once JIEQI_ROOT_PATH . '/class/ptopics.php';
$ptopics_handler = JieqiPtopicsHandler::getInstance('JieqiPtopicsHandler');
$ismanager = jieqi_checkpower($jieqiPower['system']['manageallparlor'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$canedit = $ismanager;
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    if (!empty($_SESSION['jieqiUserId']) && $_SESSION['jieqiUserId'] == $ownerid) {
        $canedit = true;
    }
}
if (isset($_POST['act']) && !empty($_REQUEST['tid'])) {
    if (!$canedit && $_POST['act'] != 'del') {
        jieqi_printfail(LANG_NO_PERMISSION);
    }
    $_REQUEST['tid'] = intval($_REQUEST['tid']);
    $actparlor = $ptopics_handler->get($_REQUEST['tid']);
    if (is_object($actparlor)) {
        switch ($_POST['act']) {
            case 'top':
                $actparlor->setVar('istop', 1);
                $ptopics_handler->insert($actparlor);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'untop':
                $actparlor->setVar('istop', 0);
                $ptopics_handler->insert($actparlor);
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'good':
                $actparlor->setVar('isgood', 1);
                $ptopics_handler->insert($actparlor);
                if (!empty($jieqiConfigs['system']['scoregoodptopic'])) {
                    include_once JIEQI_ROOT_PATH . '/class/users.php';
                    $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                    $users_handler->changeScore($actparlor->getVar('posterid'), $jieqiConfigs['system']['scoregoodptopic'], true);
                }
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'normal':
                $actparlor->setVar('isgood', 0);
                $ptopics_handler->insert($actparlor);
                if (!empty($jieqiConfigs['system']['scoregoodptopic'])) {
                    include_once JIEQI_ROOT_PATH . '/class/users.php';
                    $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                    $users_handler->changeScore($actparlor->getVar('posterid'), $jieqiConfigs['system']['scoregoodptopic'], false);
                }
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'del':
                $ptopic = $ptopics_handler->get($_REQUEST['tid']);
                if (!$ptopic) {
                    jieqi_printfail(LANG_ERROR_PARAMETER);
                }
                if ($ptopic->getVar('ownerid', 'n') != $ownerid) {
                    jieqi_printfail(LANG_ERROR_PARAMETER);
                }
                if (!$canedit && (empty($_SESSION['jieqiUserId']) || $_SESSION['jieqiUserId'] != $ptopic->getVar('posterid', 'n'))) {
                    jieqi_printfail(LANG_NO_PERMISSION);
                }
                include_once JIEQI_ROOT_PATH . '/class/pposts.php';
                $pposts_handler = JieqiPpostsHandler::getInstance('JieqiPpostsHandler');
                $criteria = new Criteria('topicid', $_REQUEST['tid']);
                if (!empty($jieqiAction['system']['ptopic']['earnscore'])) {
                    $pposts_handler->queryObjects($criteria);
                    $posterary = array();
                    while ($ppostobj = $pposts_handler->getObject()) {
                        $posterid = intval($ppostobj->getVar('posterid'));
                        if (isset($posterary[$posterid])) {
                            $posterary[$posterid] += $jieqiAction['system']['ptopic']['earnscore'];
                        } else {
                            $posterary[$posterid] = $jieqiAction['system']['ptopic']['earnscore'];
                        }
                    }
                    if ($actparlor->getVar('isgood', 'n') == 1 && !empty($jieqiConfigs['system']['scoregoodptopic'])) {
                        $posterid = intval($actparlor->getVar('posterid'));
                        if (isset($posterary[$posterid])) {
                            $posterary[$posterid] += $jieqiConfigs['system']['scoregoodptopic'];
                        } else {
                            $posterary[$posterid] = $jieqiConfigs['system']['scoregoodptopic'];
                        }
                    }
                    include_once JIEQI_ROOT_PATH . '/class/users.php';
                    $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                    foreach ($posterary as $pid => $pscore) {
                        $users_handler->changeScore($pid, $pscore, false);
                    }
                }
                $ptopics_handler->delete($_REQUEST['tid']);
                $pposts_handler->delete($criteria);
                jieqi_jumppage(JIEQI_URL . '/ptopics.php?uid=' . urlencode($_REQUEST['uid']), '', '', true);
                break;
        }
    }
}
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    $check_errors = array();
    $post_set = array('module' => JIEQI_MODULE_NAME, 'ownerid' => intval($_REQUEST['uid']), 'ownername' => $owneruser->getVar('name', 'n'), 'ownercode' => $owneruser->getVar('uname', 'n'), 'topicid' => 0, 'postid' => 0, 'posttime' => JIEQI_NOW_TIME, 'topictitle' => &$_POST['ptitle'], 'posttext' => &$_POST['pcontent'], 'attachment' => '', 'emptytitle' => true, 'isnew' => true, 'istopic' => 1, 'istop' => 0, 'sname' => 'jieqiSystemParlorTime', 'attachfile' => '', 'oldattach' => '', 'checkcode' => $_POST['checkcode']);
    jieqi_post_checkvar($post_set, $jieqiConfigs['system'], $check_errors);
    if (empty($check_errors)) {
        $newTopic = $ptopics_handler->create();
        jieqi_topic_newset($post_set, $newTopic);
        $ptopics_handler->insert($newTopic);
        $post_set['topicid'] = $newTopic->getVar('topicid', 'n');
        include_once JIEQI_ROOT_PATH . '/class/pposts.php';
        $pposts_handler = JieqiPpostsHandler::getInstance('JieqiPpostsHandler');
        $newPost = $pposts_handler->create();
        jieqi_post_newset($post_set, $newPost);
        $pposts_handler->insert($newPost);
        $postdisplay = intval($newPost->getVar('display', 'n'));
        $postresult = 0 < $postdisplay ? $jieqiLang['system']['ppost_post_needaudit'] : $jieqiLang['system']['ppost_post_success'];
        jieqi_post_finish();
        include_once JIEQI_ROOT_PATH . '/include/funaction.php';
        $actions = array('actname' => 'ptopic', 'actnum' => 1);
        jieqi_system_actiondo($actions, $_SESSION['jieqiUserId']);
        if (!empty($_REQUEST['ajax_request'])) {
            jieqi_msgwin(LANG_DO_SUCCESS, $postresult);
        }
    } else {
        jieqi_printfail(implode('<br />', $check_errors));
    }
}
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/ptopics.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('ownerid', $owneruser->getVar('uid'));
$jieqiTpl->assign('oid', $_REQUEST['uid']);
$jieqiTpl->assign('owneruname', $owneruser->getVar('uname'));
$ownername = strlen($owneruser->getVar('name')) == 0 ? $owneruser->getVar('uname') : $owneruser->getVar('name');
$jieqiTpl->assign('ownername', $ownername);
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    $jieqiTpl->assign('newpost', 1);
    $jieqiTpl->assign('postdisplay', $postdisplay);
    $jieqiTpl->assign('postresult', $postresult);
} else {
    $jieqiTpl->assign('newpost', 0);
}
$jieqiTpl->assign('canedit', intval($canedit));
$jieqiTpl->assign('ismaster', intval($canedit));
$jieqiTpl->assign('ismanager', intval($ismanager));
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('display', 0));
$criteria->add(new Criteria('ownerid', $_REQUEST['uid']));
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'good') {
    $jieqiTpl->assign('type', 'good');
    $criteria->add(new Criteria('isgood', 1));
} else {
    $_REQUEST['type'] = 'all';
    $jieqiTpl->assign('type', 'all');
}
$criteria->setSort('istop DESC, replytime');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$ptopics_handler->queryObjects($criteria);
$ptopicrows = array();
$k = 0;
while ($topic = $ptopics_handler->getObject()) {
    $ptopicrows[$k] = jieqi_topic_vars($topic);
    $k++;
}
$jieqiTpl->assign_by_ref('ptopicrows', $ptopicrows);
$jieqiTpl->assign('enablepost', $enablepost);
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
$jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $ptopics_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';