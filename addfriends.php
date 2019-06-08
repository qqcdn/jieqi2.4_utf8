<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
if (empty($_POST['act']) || $_POST['act'] != 'add') {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (empty($_REQUEST['id']) && empty($_REQUEST['username'])) {
    jieqi_printfail(LANG_NO_USER);
}
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
if (!empty($_REQUEST['id'])) {
    $userobj = $users_handler->get($_REQUEST['id']);
} else {
    $_REQUEST['username'] = trim($_REQUEST['username']);
    $userobj = $users_handler->getByname($_REQUEST['username'], 3);
}
if (is_object($userobj)) {
    jieqi_loadlang('users', JIEQI_MODULE_NAME);
    if ($userobj->getVar('uid', 'n') == $_SESSION['jieqiUserId']) {
        jieqi_printfail($jieqiLang['system']['add_friends_self']);
    }
    include_once JIEQI_ROOT_PATH . '/class/friends.php';
    $friends_handler = JieqiFriendsHandler::getInstance('JieqiFriendsHandler');
    jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
    jieqi_getconfigs('system', 'honors');
    jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
    $maxfriendsnum = intval($jieqiConfigs['system']['maxfriends']);
    $honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
    if ($honorid && isset($jieqiRight['system']['maxfriends']['honors'][$honorid]) && is_numeric($jieqiRight['system']['maxfriends']['honors'][$honorid])) {
        $maxfriendsnum = intval($jieqiRight['system']['maxfriends']['honors'][$honorid]);
    }
    if (is_numeric($maxfriendsnum)) {
        $criteria = new CriteriaCompo(new Criteria('myid', $_SESSION['jieqiUserId']));
        $friendsnum = $friends_handler->getCount($criteria);
        if ($maxfriendsnum <= $friendsnum) {
            jieqi_printfail(sprintf($jieqiLang['system']['too_manay_friends'], $maxfriendsnum));
        }
    }
    unset($criteria);
    $criteria = new CriteriaCompo(new Criteria('myid', $_SESSION['jieqiUserId']));
    $criteria->add(new Criteria('yourid', $userobj->getVar('uid', 'n')));
    $isexist = $friends_handler->getCount($criteria);
    if (0 < $isexist) {
        jieqi_printfail($jieqiLang['system']['has_been_friends']);
    }
    $newFriends = $friends_handler->create();
    $newFriends->setVar('adddate', JIEQI_NOW_TIME);
    $newFriends->setVar('myid', $_SESSION['jieqiUserId']);
    $newFriends->setVar('myname', $_SESSION['jieqiUserName']);
    $newFriends->setVar('yourid', $userobj->getVar('uid', 'n'));
    if (0 < strlen($userobj->getVar('name', 'n'))) {
        $newFriends->setVar('yourname', $userobj->getVar('name', 'n'));
    } else {
        $newFriends->setVar('yourname', $userobj->getVar('uname', 'n'));
    }
    $newFriends->setVar('teamid', 0);
    $newFriends->setVar('team', '');
    $newFriends->setVar('fset', '');
    $newFriends->setVar('state', 0);
    $newFriends->setVar('flag', 0);
    if (!$friends_handler->insert($newFriends)) {
        jieqi_printfail($jieqiLang['system']['add_friends_failure']);
    } else {
        if (!empty($_REQUEST['jumpurl']) && preg_match('/^(\\/\\w+|https?:\\/\\/)/i', $_REQUEST['jumpurl'])) {
            jieqi_jumppage($_REQUEST['jumpurl'], LANG_DO_SUCCESS, $jieqiLang['system']['add_friends_success']);
        } else {
            jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['add_friends_success']);
        }
    }
} else {
    jieqi_printfail(LANG_NO_USER);
}