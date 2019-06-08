<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
if ($_REQUEST['uid'] == 'self') {
    $_REQUEST['uid'] = intval($_SESSION['jieqiUserId']);
}
if (empty($_REQUEST['uid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['uid'] = intval($_REQUEST['uid']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/userfriends.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once JIEQI_ROOT_PATH . '/class/friends.php';
$friends_handler = JieqiFriendsHandler::getInstance('JieqiFriendsHandler');
$criteria = new CriteriaCompo(new Criteria('myid', $_REQUEST['uid']));
$criteria->setSort('friendsid');
$criteria->setOrder('ASC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$friends_handler->queryObjects($criteria);
$friendsrows = array();
$k = 0;
$ownername = '';
while ($v = $friends_handler->getObject()) {
    $friendsrows[$k]['myid'] = $v->getVar('myid');
    $friendsrows[$k]['myname'] = $v->getVar('myname');
    if (empty($ownername) && !empty($friendsrows[$k]['myname'])) {
        $ownername = $v->getVar('myname');
    }
    $friendsrows[$k]['yourid'] = $v->getVar('yourid');
    $friendsrows[$k]['yourname'] = $v->getVar('yourname');
    $friendsrows[$k]['adddate'] = date(JIEQI_DATE_FORMAT, $v->getVar('adddate'));
    $k++;
}
$jieqiTpl->assign_by_ref('friendsrows', $friendsrows);
$jieqiTpl->assign('owner', $ownername);
$jieqiTpl->assign('ownerid', $_REQUEST['uid']);
$friendsnum = $friends_handler->getCount($criteria);
$jieqiTpl->assign('nowfriends', $friendsnum);
jieqi_getconfigs('system', 'honors');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
$maxfriendsnum = intval($jieqiConfigs['system']['maxfriends']);
$honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
if ($honorid && isset($jieqiRight['system']['maxfriends']['honors'][$honorid]) && is_numeric($jieqiRight['system']['maxfriends']['honors'][$honorid])) {
    $maxfriendsnum = intval($jieqiRight['system']['maxfriends']['honors'][$honorid]);
}
$jieqiTpl->assign('maxfriends', $maxfriendsnum);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $friendsnum;
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';