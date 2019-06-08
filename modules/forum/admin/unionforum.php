<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['forum']['manageforum'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_POST['act'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
} else {
    jieqi_checkpost();
}
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
if (empty($_REQUEST['fromid']) || empty($_REQUEST['toid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
} else {
    if ($_REQUEST['fromid'] == $_REQUEST['toid']) {
        jieqi_printfail($jieqiLang['forum']['fromto_is_same']);
    }
}
include_once $jieqiModules['forum']['path'] . '/class/forums.php';
$forums_handler = JieqiForumsHandler::getInstance('JieqiForumsHandler');
$fromforum = $forums_handler->get($_REQUEST['fromid']);
if (!is_object($fromforum)) {
    jieqi_printfail($jieqiLang['forum']['fromforum_not_exists']);
}
$toforum = $forums_handler->get($_REQUEST['toid']);
if (!is_object($toforum)) {
    jieqi_printfail($jieqiLang['forum']['toforum_not_exists']);
}
include_once $jieqiModules['forum']['path'] . '/class/forumtopics.php';
$topics_handler = JieqiForumtopicsHandler::getInstance('JieqiForumtopicsHandler');
$criteria = new CriteriaCompo(new Criteria('ownerid', $_REQUEST['fromid'], '='));
$topics_handler->updatefields(array('ownerid' => $_REQUEST['toid']), $criteria);
include_once $jieqiModules['forum']['path'] . '/class/forumposts.php';
$posts_handler = JieqiForumpostsHandler::getInstance('JieqiForumpostsHandler');
$posts_handler->updatefields(array('ownerid' => $_REQUEST['toid']), $criteria);
$toforum->setVar('forumtopics', $toforum->getVar('forumtopics') + $fromforum->getVar('forumtopics'));
$toforum->setVar('forumposts', $toforum->getVar('forumposts') + $fromforum->getVar('forumposts'));
$forums_handler->insert($toforum);
$forums_handler->delete($_REQUEST['fromid']);
include_once $jieqiModules['forum']['path'] . '/include/upforumset.php';
jieqi_jumppage($jieqiModules['forum']['url'] . '/admin/forumlist.php', LANG_DO_SUCCESS, $jieqiLang['forum']['union_forum_success']);