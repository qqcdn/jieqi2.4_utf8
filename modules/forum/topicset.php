<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../global.php';
if (empty($_REQUEST['tid']) || !is_numeric($_REQUEST['tid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (!empty($_POST['act'])) {
    jieqi_checkpost();
}
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('t.topicid', $_REQUEST['tid']));
$criteria->setTables(jieqi_dbprefix('forum_forumtopics') . ' t LEFT JOIN ' . jieqi_dbprefix('forum_forums') . ' f ON t.ownerid=f.forumid');
$query->queryObjects($criteria);
$topic = $query->getObject();
unset($criteria);
if (!$topic) {
    jieqi_printfail($jieqiLang['forum']['post_not_exists']);
}
$editpower['groups'] = jieqi_unserialize($topic->getVar('authedit', 'n'));
if (!is_array($editpower['groups'])) {
    $editpower['groups'] = array();
}
$canedit = jieqi_checkpower($editpower, $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$canedit && !empty($_SESSION['jieqiUserName'])) {
    $tmpary = jieqi_unserialize($topic->getVar('master', 'n'));
    if (is_array($tmpary) && 0 < count($tmpary)) {
        $masterary = '';
        foreach ($tmpary as $v) {
            if ($v['uname'] != '') {
                $masterary[] = $v['uname'];
            }
        }
        if (in_array($_SESSION['jieqiUserName'], $masterary)) {
            $canedit = true;
        }
    }
}
if (!$canedit) {
    jieqi_printfail(LANG_NO_PERMISSION);
}
$topicfid = $topic->getVar('forumid', 'n');
switch ($_POST['act']) {
    case 'top':
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET istop=1 WHERE topicid=' . $_REQUEST['tid']);
        break;
    case 'untop':
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET istop=0 WHERE topicid=' . $_REQUEST['tid']);
        break;
    case 'good':
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET isgood=1 WHERE topicid=' . $_REQUEST['tid']);
        break;
    case 'nogood':
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET isgood=0 WHERE topicid=' . $_REQUEST['tid']);
        break;
    case 'lock':
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET islock=1 WHERE topicid=' . $_REQUEST['tid']);
        break;
    case 'unlock':
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET islock=0 WHERE topicid=' . $_REQUEST['tid']);
        break;
    case 'push':
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET replytime=' . JIEQI_NOW_TIME . ' WHERE topicid=' . $_REQUEST['tid']);
        break;
    case 'move':
        $fromfid = intval($topic->getVar('forumid', 'n'));
        $tofid = intval($_REQUEST['tofid']);
        if (empty($tofid)) {
            jieqi_printfail($jieqiLang['forum']['forum_not_exists']);
        }
        include_once $jieqiModules['forum']['path'] . '/class/forums.php';
        $forums_handler = JieqiForumsHandler::getInstance('JieqiForumsHandler');
        $forum = $forums_handler->get($tofid);
        if (!$forum) {
            jieqi_printfail($jieqiLang['forum']['forum_not_exists']);
        }
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forumtopics') . ' SET ownerid=' . $tofid . ' WHERE topicid=' . $_REQUEST['tid']);
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forums') . ' SET forumtopics = forumtopics - 1, forumposts = forumposts - ' . (intval($topic->getVar('replies', 'n')) + 1) . ' WHERE forumid=' . $fromfid);
        $query->execute('UPDATE ' . jieqi_dbprefix('forum_forums') . ' SET forumtopics = forumtopics + 1, forumposts = forumposts + ' . (intval($topic->getVar('replies', 'n')) + 1) . ' WHERE forumid=' . $tofid);
        $topicfid = $tofid;
        break;
}
if (empty($_REQUEST['ajax_request'])) {
    header('Location: ' . jieqi_headstr(jieqi_geturl('forum', 'topiclist', 1, $topicfid)));
} else {
    echo '1';
}
exit;