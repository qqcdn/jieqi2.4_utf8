<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../global.php';
if (empty($_REQUEST['pid']) || !is_numeric($_REQUEST['pid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('post', JIEQI_MODULE_NAME);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('p.postid', $_REQUEST['pid']));
$criteria->setTables(jieqi_dbprefix('forum_forumposts') . ' p LEFT JOIN ' . jieqi_dbprefix('forum_forums') . ' f ON p.ownerid=f.forumid');
$query->queryObjects($criteria);
$post = $query->getObject();
unset($criteria);
if (!$post) {
    jieqi_printfail($jieqiLang['forum']['post_not_exists']);
}
$tid = $post->getVar('topicid');
$fid = $post->getVar('forumid');
include_once $jieqiModules['forum']['path'] . '/include/funforum.php';
if (!jieqi_forum_checkpower($post, 'authdelete', true)) {
    jieqi_printfail($jieqiLang['forum']['noper_delete_post']);
}
$forum_type = intval($post->getVar('forumtype', 'n'));
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if ($post->getVar('istopic') == 1) {
    if (!jieqi_forum_deltopic($post->getVar('topicid'), $post->getVar('forumid'), $jieqiConfigs['forum'])) {
        jieqi_printfail($jieqiLang['forum']['delete_post_failure']);
    }
    if ($forum_type == 1) {
        jieqi_forum_uptoptopic();
    }
    jieqi_jumppage(jieqi_geturl('forum', 'topiclist', 1, $post->getVar('forumid')), LANG_DO_SUCCESS, $jieqiLang['forum']['delete_post_success']);
} else {
    if (!jieqi_forum_delpost($post->getVar('postid'), $post->getVar('topicid'), $post->getVar('forumid'), $jieqiConfigs['forum'])) {
        jieqi_printfail($jieqiLang['forum']['delete_post_failure']);
    }
    jieqi_jumppage(jieqi_geturl('forum', 'showtopic', $post->getVar('topicid')), LANG_DO_SUCCESS, $jieqiLang['forum']['delete_post_success']);
}