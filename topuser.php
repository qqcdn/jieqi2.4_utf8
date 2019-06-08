<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['system']['viewuser'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_loadlang('users', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('system', 'honors');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/topuser.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$criteria = new CriteriaCompo();
switch ($_REQUEST['sort']) {
    case 'experience':
        $jieqiTpl->assign('sort', 'experience');
        $jieqiTpl->assign('usertitle', $jieqiLang['system']['top_user_experience']);
        $criteria->setSort('experience');
        $criteria->setOrder('DESC');
        break;
    case 'score':
        $jieqiTpl->assign('sort', 'score');
        $jieqiTpl->assign('usertitle', $jieqiLang['system']['top_user_score']);
        $criteria->setSort('score');
        $criteria->setOrder('DESC');
        break;
    case 'monthscore':
        $monthstart = mktime(0, 0, 0, intval(date('m', JIEQI_NOW_TIME)), 1, intval(date('Y', JIEQI_NOW_TIME)));
        $criteria->add(new Criteria('lastlogin', $monthstart, '>='));
        $jieqiTpl->assign('sort', 'monthscore');
        $jieqiTpl->assign('usertitle', $jieqiLang['system']['top_user_monthscore']);
        $criteria->setSort('monthscore');
        $criteria->setOrder('DESC');
        break;
    case 'credit':
        $jieqiTpl->assign('sort', 'credit');
        $jieqiTpl->assign('usertitle', $jieqiLang['system']['top_user_credit']);
        $criteria->setSort('credit');
        $criteria->setOrder('DESC');
        break;
    case 'regdate':
    default:
        $jieqiTpl->assign('sort', 'regdate');
        $jieqiTpl->assign('usertitle', $jieqiLang['system']['top_user_join']);
        $criteria->setSort('regdate');
        $criteria->setOrder('DESC');
        break;
}
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$users_handler->queryObjects($criteria);
$userrows = array();
$k = 0;
include_once JIEQI_ROOT_PATH . '/include/funusers.php';
while ($v = $users_handler->getObject()) {
    $userrows[$k] = jieqi_system_usersvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('userrows', $userrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $users_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';