<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
if (empty($_REQUEST['tid']) || !is_numeric($_REQUEST['tid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (!empty($_POST['act'])) {
    jieqi_checkpost();
}
jieqi_loadlang('parlar', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$post_query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('t.topicid', $_REQUEST['tid']));
$criteria->setTables(jieqi_dbprefix('system_ptopics') . ' t LEFT JOIN ' . jieqi_dbprefix('system_users') . ' u ON t.ownerid=u.uid');
$post_query->queryObjects($criteria);
$ptopic = $post_query->getObject();
unset($criteria);
if (!$ptopic) {
    jieqi_printfail($jieqiLang['system']['ptopic_not_exists']);
} else {
    if (0 < $ptopic->getVar('islock', 'n')) {
        jieqi_printfail($jieqiLang['system']['ppost_topic_locked']);
    }
}
$ownerid = $ptopic->getVar('ownerid', 'n');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$ismanager = jieqi_checkpower($jieqiPower['system']['manageallparlor'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if ($ptopic->getVar('display', 'n') != 0) {
    if (!$ismanager) {
        if ($ptopic->getVar('display', 'n') == 1) {
            jieqi_printfail($jieqiLang['system']['ptopic_not_audit']);
        } else {
            jieqi_printfail($jieqiLang['system']['ptopic_not_exists']);
        }
    }
}
if (jieqi_checkpower($jieqiPower['system']['parlorpost'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
    $enablepost = 1;
} else {
    $enablepost = 0;
}
jieqi_getconfigs('system', 'action', 'jieqiAction');
if (!isset($_POST['act'])) {
    $_POST['act'] = NULL;
}
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    if (empty($_POST['pcontent'])) {
        jieqi_printfail($jieqiLang['system']['ppost_need_pcontent']);
    }
    if (!$enablepost) {
        jieqi_printfail($jieqiLang['system']['parlor_noper_post']);
    }
    if (!empty($jieqiAction['system']['ptopic']['minscore']) && $_SESSION['jieqiUserScore'] < intval($jieqiAction['system']['ptopic']['minscore'])) {
        jieqi_printfail(sprintf($jieqiLang['system']['ptopic_score_limit'], intval($jieqiAction['system']['ptopic']['minscore'])));
    }
}
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
$addnewreply = 0;
if (isset($_POST['act']) && $_POST['act'] == 'newpost') {
    $check_errors = array();
    $post_set = array('module' => JIEQI_MODULE_NAME, 'ownerid' => intval($ownerid), 'topicid' => intval($_REQUEST['tid']), 'postid' => 0, 'posttime' => JIEQI_NOW_TIME, 'topictitle' => &$_POST['ptitle'], 'posttext' => &$_POST['pcontent'], 'attachment' => '', 'isnew' => true, 'istopic' => 0, 'istop' => 0, 'sname' => 'jieqiSystemParlorTime', 'attachfile' => '', 'oldattach' => '', 'checkcode' => $_POST['checkcode']);
    jieqi_post_checkvar($post_set, $jieqiConfigs['system'], $check_errors);
    if (empty($check_errors)) {
        include_once JIEQI_ROOT_PATH . '/class/pposts.php';
        $pposts_handler = JieqiPpostsHandler::getInstance('JieqiPpostsHandler');
        $newPost = $pposts_handler->create();
        jieqi_post_newset($post_set, $newPost);
        $pposts_handler->insert($newPost);
        $postdisplay = intval($newPost->getVar('display', 'n'));
        $postresult = 0 < $postdisplay ? $jieqiLang['system']['ppost_post_needaudit'] : $jieqiLang['system']['ppost_post_success'];
        $addnewreply = 1;
        $_REQUEST['page'] = 'last';
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
$canedit = $ismanager;
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    if (!empty($_SESSION['jieqiUserId']) && $_SESSION['jieqiUserId'] == $ownerid) {
        $canedit = true;
    }
}
if (isset($_POST['act']) && $_POST['act'] == 'del' && !empty($_REQUEST['pid'])) {
    $_REQUEST['pid'] = intval($_REQUEST['pid']);
    include_once JIEQI_ROOT_PATH . '/class/pposts.php';
    $pposts_handler = JieqiPpostsHandler::getInstance('JieqiPpostsHandler');
    $delPost = $pposts_handler->get($_REQUEST['pid']);
    if (!$delPost) {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
    if ($delPost->getVar('topicid', 'n') != $ptopic->getVar('topicid', 'n')) {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
    if (!$canedit && (empty($_SESSION['jieqiUserId']) || $_SESSION['jieqiUserId'] != $delPost->getVar('posterid', 'n'))) {
        jieqi_printfail(LANG_NO_PERMISSION);
    }
    if (!empty($jieqiAction['system']['ptopic']['earnscore'])) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        $users_handler->changeScore(intval($delPost->getVar('posterid', 'n')), $jieqiAction['system']['ptopic']['earnscore'], false);
    }
    $pposts_handler->delete(intval($_REQUEST['pid']));
    $addnewreply = -1;
    jieqi_topic_uppostdel($delPost, jieqi_dbprefix('system_ptopics'));
    $post_query->execute('UPDATE ' . jieqi_dbprefix('system_ptopics') . ' SET replies = replies - 1 WHERE topicid = ' . $_REQUEST['tid']);
    jieqi_jumppage(JIEQI_URL . '/ptopicshow.php?tid=' . urlencode($_REQUEST['tid']), '', '', true);
}
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/ptopicshow.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('tid', $_REQUEST['tid']);
$jieqiTpl->assign('ownerid', $ptopic->getVar('ownerid'));
$jieqiTpl->assign('owneruname', $ptopic->getVar('uname'));
$ownername = strlen($ptopic->getVar('name')) == 0 ? $ptopic->getVar('uname') : $ptopic->getVar('name');
$jieqiTpl->assign('ownername', $ownername);
$jieqiTpl->assign('topicid', $ptopic->getVar('topicid'));
$jieqiTpl->assign('title', $ptopic->getVar('title'));
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
jieqi_getconfigs('system', 'honors');
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
if (!empty($jieqiModules['badge']['publish']) && is_file($jieqiModules['badge']['path'] . '/include/badgefunction.php')) {
    include_once $jieqiModules['badge']['path'] . '/include/badgefunction.php';
    $jieqi_use_badge = 1;
    $jieqiTpl->assign('jieqi_use_badge', 1);
} else {
    $jieqi_use_badge = 0;
    $jieqiTpl->assign('jieqi_use_badge', 0);
}
$criteria = new CriteriaCompo(new Criteria('p.topicid', $_REQUEST['tid']));
$criteria->add(new Criteria('p.display', 0));
$criteria->setTables(jieqi_dbprefix('system_pposts') . ' p LEFT JOIN ' . jieqi_dbprefix('system_users') . ' u ON p.posterid=u.uid');
$criteria->setSort('p.postid');
$criteria->setOrder('ASC');
$criteria->setLimit($jieqiPset['rows']);
$jieqiPset['count'] = $post_query->getCount($criteria);
if ($_REQUEST['page'] == 'last') {
    $_REQUEST['page'] = ceil($jieqiPset['count'] / $jieqiPset['rows']);
    $jieqiPset['page'] = $_REQUEST['page'];
    $jieqiPset['start'] = ($jieqiPset['page'] - 1) * $jieqiPset['rows'];
}
$criteria->setStart($jieqiPset['start']);
$post_query->queryObjects($criteria);
$ppostrows = array();
$k = 0;
while ($ppost = $post_query->getObject()) {
    $addvars = array('order' => ($jieqiPset['page'] - 1) * $jieqiPset['rows'] + $k + 1);
    $ppostrows[$k] = jieqi_post_vars($ppost, $jieqiConfigs['system'], $addvars, true);
    $k++;
}
$jieqiTpl->assign_by_ref('ppostrows', $ppostrows);
$jieqiTpl->assign('enablepost', $enablepost);
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
$jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
if (0 < $addnewreply) {
    jieqi_topic_uppostadd($newPost, jieqi_dbprefix('system_ptopics'));
} else {
    if ($addnewreply < 0) {
        jieqi_topic_uppostdel($delPost, jieqi_dbprefix('system_ptopics'));
    } else {
        include_once JIEQI_ROOT_PATH . '/include/funstat.php';
        jieqi_visit_stat($_REQUEST['tid'], jieqi_dbprefix('system_ptopics'), 'views', 'topicid', $post_query);
    }
}
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';