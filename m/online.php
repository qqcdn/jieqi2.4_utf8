<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['system']['viewonline'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once JIEQI_ROOT_PATH . '/header.php';
if (!JIEQI_USE_CACHE || !$jieqiTpl->is_cached(JIEQI_ROOT_PATH . '/templates/online.html')) {
    include_once JIEQI_ROOT_PATH . '/class/online.php';
    $online_handler = JieqiOnlineHandler::getInstance('JieqiOnlineHandler');
    $criteria = new CriteriaCompo(new Criteria('updatetime', JIEQI_NOW_TIME - $jieqiConfigs['system']['onlinetime'], '>'));
    $allnum = $online_handler->getCount($criteria);
    $criteria->add(new Criteria('uid', '0'), '>');
    $criteria->setSort('groupid');
    $criteria->setOrder('ASC');
    $result = $online_handler->queryObjects($criteria);
    $userrows = array();
    $usernum = 0;
    $i = 0;
    if ($result) {
        while ($srow = $online_handler->getRow($result)) {
            $userrows[$i]['uid'] = $srow['uid'];
            $userrows[$i]['siteid'] = $srow['siteid'];
            $userrows[$i]['uname'] = jieqi_htmlstr($srow['uname']);
            $userrows[$i]['name'] = jieqi_htmlstr($srow['name']);
            if (strlen($userrows[$i]['name']) == 0) {
                $userrows[$i]['name'] = $userrows[$i]['uname'];
            }
            $userrows[$i]['logintime'] = $srow['logintime'];
            $userrows[$i]['updatetime'] = $srow['updatetime'];
            $userrows[$i]['location'] = jieqi_htmlstr($srow['location']);
            $i++;
            $usernum++;
        }
    }
    $jieqiTpl->assign('allnum', $allnum);
    $jieqiTpl->assign('usernum', $usernum);
    $jieqiTpl->assign('guestnum', $allnum - $usernum);
    $jieqiTpl->assign_by_ref('userrows', $userrows);
}
if (JIEQI_USE_CACHE) {
    $jieqiTpl->setCaching(1);
}
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/online.html';
include_once JIEQI_ROOT_PATH . '/footer.php';