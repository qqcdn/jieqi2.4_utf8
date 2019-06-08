<?php

function jieqi_system_actiondo($actions, $user)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $users_handler;
    if (is_numeric($user)) {
        if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
            include_once JIEQI_ROOT_PATH . '/class/users.php';
            $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        }
        $user = $users_handler->get(intval($user));
    }
    if (!is_object($user)) {
        return false;
    }
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['system'][$actions['actname']])) {
        return false;
    }
    if (empty($actions['actnum'])) {
        $actions['actnum'] = 1;
    }
    if (!isset($actions['tid'])) {
        $actions['tid'] = $user->getVar('uid', 'n');
    }
    if (!isset($actions['tname'])) {
        $actions['tname'] = $user->getVar('name', 'n') != '' ? $user->getVar('name', 'n') : $user->getVar('uname', 'n');
    }
    if (!isset($actions['no_earn']) || $actions['no_earn'] == false) {
        jieqi_system_actionearn($actions, $user);
    }
    if (!isset($actions['no_record']) || $actions['no_record'] == false) {
        jieqi_system_actionrecord($actions, $user);
    }
    if (!isset($actions['no_message']) || $actions['no_message'] == false) {
        jieqi_system_actionmessage($actions, $user);
    }
    return true;
}
function jieqi_system_actionearn($actions, $user)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $users_handler;
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['system'][$actions['actname']])) {
        return false;
    }
    if (0 < $jieqiAction['system'][$actions['actname']]['earnscore']) {
        jieqi_system_actionscore($actions, $user);
    }
    if (0 < $jieqiAction['system'][$actions['actname']]['earncredit']) {
        jieqi_system_actioncredit($actions, $user);
    }
}
function jieqi_system_actionrecord($actions, $user)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $users_handler;
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['system'][$actions['actname']])) {
        return false;
    }
    if (!empty($jieqiAction['system'][$actions['actname']]['islog'])) {
        jieqi_system_actionlog($actions, $user);
    }
    if (!empty($jieqiAction['system'][$actions['actname']]['isreview'])) {
        jieqi_system_actionreview($actions, $user);
    }
}
function jieqi_system_actionmessage($actions, $user)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $users_handler;
    global $jieqiLang;
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['system'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiLang['system']['action'])) {
        jieqi_loadlang('action', 'system');
    }
    if (!empty($jieqiAction['system'][$actions['actname']]['ismessage'])) {
        $receiverid = $user->getVar('uid', 'n');
        $receiver = $user->getVar('name', 'n');
        $title = isset($actions['message_title']) ? $actions['message_title'] : $jieqiLang['system'][$actions['actname'] . '_message_title'];
        $content = isset($actions['message_content']) ? $actions['message_content'] : $jieqiLang['system'][$actions['actname'] . '_message_content'];
        if (0 < $receiverid && 0 < strlen($content)) {
            include_once JIEQI_ROOT_PATH . '/include/funmessage.php';
            jieqi_sendmessage(array('toid' => $receiverid, 'toname' => $receiver, 'title' => $title, 'content' => $content, 'messagetype' => -20));
        }
    }
}
function jieqi_system_actionscore($actions, $user)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $users_handler;
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['system'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['system'][$actions['actname']]['earnscore'])) {
        return false;
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $uid = isset($actions['uid']) ? intval($actions['uid']) : intval($_SESSION['jieqiUserId']);
    if (empty($jieqiAction['system'][$actions['actname']]['paybase'])) {
        $jieqiAction['system'][$actions['actname']]['paybase'] = 1;
    }
    if (0 < $jieqiAction['system'][$actions['actname']]['earnscore']) {
        $earnscore = floor(intval($actions['actnum']) * $jieqiAction['system'][$actions['actname']]['earnscore'] / $jieqiAction['system'][$actions['actname']]['paybase']);
    } else {
        $earnscore = 0;
    }
    if ($earnscore != 0) {
        $users_handler->changeScore($uid, $earnscore, true);
    }
}
function jieqi_system_actioncredit($actions, $user)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['system'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['system'][$actions['actname']]['earncredit'])) {
        return false;
    }
    $earncredit = 0;
    if (empty($jieqiAction['system'][$actions['actname']]['paybase'])) {
        $jieqiAction['system'][$actions['actname']]['paybase'] = 1;
    }
    if (0 < $jieqiAction['system'][$actions['actname']]['earncredit']) {
        $earncredit = floor(intval($actions['actnum']) * $jieqiAction['system'][$actions['actname']]['earncredit'] / $jieqiAction['system'][$actions['actname']]['paybase']);
    }
    if ($earncredit <= 0) {
        return false;
    }
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $uid = isset($actions['uid']) ? intval($actions['uid']) : intval($_SESSION['jieqiUserId']);
    $uname = isset($actions['uname']) ? $actions['uname'] : $_SESSION['jieqiUserName'];
    $tid = intval($user->getVar('uid', 'n'));
    $tname = $user->getVar('name', 'n');
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_credit') . ' WHERE uid = ' . $uid . ' AND tid = ' . $tid . ' LIMIT 0, 1';
    $ret = $query->execute($sql);
    $row = $query->getRow();
    if (is_array($row)) {
        $vars = jieqi_unserialize($row['vars']);
        $vars[$actions['actname']] = isset($vars[$actions['actname']]) ? $vars[$actions['actname']] + $earncredit : $earncredit;
        $sql = 'UPDATE ' . jieqi_dbprefix('system_credit') . ' SET point = point + ' . $earncredit;
        if (!empty($actions['actegold'])) {
            $sql .= ', payegold = payegold + ' . intval($actions['actegold']);
            if (!empty($actions['actbuy'])) {
                $sql .= ', buyegold = buyegold + ' . intval($actions['actegold']);
            }
        }
        $sql .= ', upnum = upnum + 1, uptime = ' . intval(JIEQI_NOW_TIME) . ', vars = \'' . jieqi_dbslashes(serialize($vars)) . '\' WHERE creditid = ' . intval($row['creditid']);
        $ret = $query->execute($sql);
    } else {
        $fieldrows = array();
        $fieldrows['tid'] = $tid;
        $fieldrows['tname'] = $tname;
        $fieldrows['uid'] = $uid;
        $fieldrows['uname'] = $uname;
        $fieldrows['point'] = $earncredit;
        $fieldrows['payegold'] = 0;
        $fieldrows['buyegold'] = 0;
        if (!empty($actions['actegold'])) {
            $fieldrows['payegold'] = intval($actions['actegold']);
            if (!empty($actions['actbuy'])) {
                $fieldrows['buyegold'] = intval($actions['actegold']);
            }
        }
        $fieldrows['upnum'] = 1;
        $fieldrows['uptime'] = JIEQI_NOW_TIME;
        $vars = array();
        $vars[$actions['actname']] = $earncredit;
        $fieldrows['vars'] = serialize($vars);
        $sql = $query->makeupsql(jieqi_dbprefix('system_credit'), $fieldrows, 'INSERT');
        $ret = $query->execute($sql);
    }
    return $ret;
}
function jieqi_system_actionlog($actions, $user)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['system'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['system'][$actions['actname']]['islog'])) {
        return false;
    }
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $uid = isset($actions['uid']) ? intval($actions['uid']) : intval($_SESSION['jieqiUserId']);
    $uname = isset($actions['uname']) ? $actions['uname'] : $_SESSION['jieqiUserName'];
    $tid = intval($user->getVar('uid', 'n'));
    $tname = $user->getVar('name', 'n');
    $fieldrows = array();
    $fieldrows['tid'] = $tid;
    $fieldrows['tname'] = $tname;
    $fieldrows['uid'] = $uid;
    $fieldrows['uname'] = $uname;
    $fieldrows['linkid'] = isset($actions['linkid']) ? intval($actions['linkid']) : 0;
    $fieldrows['acttype'] = isset($actions['acttype']) ? intval($actions['acttype']) : 0;
    $fieldrows['addtime'] = JIEQI_NOW_TIME;
    $fieldrows['actname'] = $actions['actname'];
    $fieldrows['actnum'] = intval($actions['actnum']);
    $fieldrows['islog'] = empty($jieqiAction['system'][$actions['actname']]['islog']) ? 0 : intval($jieqiAction['system'][$actions['actname']]['islog']);
    $fieldrows['isvip'] = empty($jieqiAction['system'][$actions['actname']]['isvip']) ? 0 : intval($jieqiAction['system'][$actions['actname']]['isvip']);
    if (empty($jieqiAction['system'][$actions['actname']]['paybase'])) {
        $jieqiAction['system'][$actions['actname']]['paybase'] = 1;
    }
    if (0 < $jieqiAction['system'][$actions['actname']]['earncredit']) {
        $earncredit = floor(intval($actions['actnum']) * $jieqiAction['system'][$actions['actname']]['earncredit'] / $jieqiAction['system'][$actions['actname']]['paybase']);
    } else {
        $earncredit = 0;
    }
    $fieldrows['credit'] = $earncredit;
    if (0 < $jieqiAction['system'][$actions['actname']]['earnscore']) {
        $earnscore = floor(intval($actions['actnum']) * $jieqiAction['system'][$actions['actname']]['earnscore'] / $jieqiAction['system'][$actions['actname']]['paybase']);
    } else {
        $earnscore = 0;
    }
    $fieldrows['score'] = $earnscore;
    $fieldrows['egold'] = empty($actions['actegold']) ? 0 : intval($actions['actegold']);
    $channel = $user->getVar('channel', 'n');
    if ($channel !== false) {
        $fieldrows['channel'] = $channel;
    } else {
        if (isset($_SESSION['jieqiUserChannel'])) {
            $fieldrows['channel'] = $_SESSION['jieqiUserChannel'];
        }
    }
    if (defined('JIEQI_DEVICE_FOR')) {
        $fieldrows['device'] = JIEQI_DEVICE_FOR;
    }
    $sql = $query->makeupsql(jieqi_dbprefix('system_actlog'), $fieldrows, 'INSERT');
    $ret = $query->execute($sql);
    return $ret;
}
function jieqi_system_autoreview($userid, $title, $content)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    include_once JIEQI_ROOT_PATH . '/include/funpost.php';
    $check_errors = array();
    $post_set = array('module' => 'system', 'ownerid' => intval($userid), 'topicid' => 0, 'postid' => 0, 'posttime' => JIEQI_NOW_TIME, 'topictitle' => $title, 'posttext' => $content, 'attachment' => '', 'emptytitle' => true, 'isnew' => true, 'istopic' => 1, 'istop' => 0, 'sname' => 'jieqiArticleReviewTime', 'attachfile' => '', 'oldattach' => '', 'checkcode' => false);
    include_once JIEQI_ROOT_PATH . '/class/ptopics.php';
    $ptopics_handler = JieqiPtopicsHandler::getInstance('JieqiPtopicsHandler');
    $newTopic = $ptopics_handler->create();
    jieqi_topic_newset($post_set, $newTopic);
    $ptopics_handler->insert($newTopic);
    $post_set['topicid'] = $newTopic->getVar('topicid', 'n');
    include_once JIEQI_ROOT_PATH . '/class/pposts.php';
    $pposts_handler = JieqiPpostsHandler::getInstance('JieqiPpostsHandler');
    $newPost = $pposts_handler->create();
    jieqi_post_newset($post_set, $newPost);
    $pposts_handler->insert($newPost);
    return true;
}
function jieqi_system_actionreview($actions, $user)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $jieqiLang;
    if (empty($jieqiLang['system']['action'])) {
        jieqi_loadlang('action', 'system');
    }
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['system'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['system'][$actions['actname']]['isreview'])) {
        return false;
    }
    $userid = $user->getVar('uid', 'n');
    $title = isset($actions['review_title']) ? $actions['review_title'] : $jieqiLang['system'][$actions['actname'] . '_review_title'];
    $content = isset($actions['review_content']) ? $actions['review_content'] : $jieqiLang['system'][$actions['actname'] . '_review_content'];
    if (0 < strlen($content)) {
        return jieqi_system_autoreview($userid, $title, $content);
    } else {
        return true;
    }
}