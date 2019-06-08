<?php
/**
 * 后台用户列表
 *
 * 后台用户列表
 * 
 * 调用模板：/templates/admin/users.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: users.php 332 2009-02-23 09:15:08Z juny $
 */

define('JIEQI_MODULE_NAME', 'system');
require_once ('../global.php');
//检查权限
include_once (JIEQI_ROOT_PATH . '/class/power.php');
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
include_once (JIEQI_ROOT_PATH . '/class/users.php');
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');

$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/users.html';
include_once (JIEQI_ROOT_PATH . '/admin/header.php');
$jieqiPset = jieqi_get_pageset(); //分页参数


$criteria = new CriteriaCompo();
$orderary = array('uid', 'regdate', 'lastlogin', 'score', 'monthscore', 'weekscore', 'dayscore', 'experience', 'egold', 'esilver', 'credit');
if(!empty($_REQUEST['order']) && in_array($_REQUEST['order'], $orderary)) $c_sort = $_REQUEST['order'];
else $c_sort = 'uid';

if(!empty($_REQUEST['asc'])) $c_order = 'ASC';
else $c_order = 'DESC';

$jieqiTpl->assign('sort', urlencode($c_sort));
$jieqiTpl->assign('order', urlencode($c_order));
$criteria->setSort(str_replace('lastupdate', 'greatest(lastupdate, postdate)', $c_sort));
$criteria->setOrder($c_order);


if(isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])){
	switch($_REQUEST['keytype']){
		case 'name':
			$criteria->add(new Criteria('name', $_REQUEST['keyword'], '='));
			break;
		case 'uid':
			$criteria->add(new Criteria('uid', intval($_REQUEST['keyword']), '='));
			break;
		case 'email':
			$criteria->add(new Criteria('email', $_REQUEST['keyword'], '='));
			break;
		case 'mobile':
			$criteria->add(new Criteria('mobile', $_REQUEST['keyword'], '='));
			break;
		case 'channel':
			$criteria->add(new Criteria('channel', $_REQUEST['keyword'], '='));
			break;
		case 'uname':
		default:
			$criteria->add(new Criteria('uname', $_REQUEST['keyword'], '='));
			break;
	}
	$_REQUEST['display'] = '';
	$_REQUEST['groupid'] = 0;
}else{

	if(!empty($_REQUEST['groupid'])){
		$criteria->add(new Criteria('groupid', intval($_REQUEST['groupid']), '='));
	}

	if(!empty($_REQUEST['display'])){
		switch($_REQUEST['display']){
			case 'vip':
				$criteria->add(new Criteria('isvip', 0, '>'));
				break;
			case 'free':
				$criteria->add(new Criteria('isvip', 0, '='));
				break;
			case 'monthly':
				$criteria->add(new Criteria('overtime', JIEQI_NOW_TIME, '>'));
				break;
		}
	}
}
if(!isset($_REQUEST['keytype'])) $_REQUEST['keytype'] = '';
if(!isset($_REQUEST['keyword'])) $_REQUEST['keyword'] = '';
if(!isset($_REQUEST['display'])) $_REQUEST['display'] = '';
if(!isset($_REQUEST['groupid'])) $_REQUEST['groupid'] = 0;


$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$users_handler->queryObjects($criteria);
$userrows = array();
$k = 0;
include_once(JIEQI_ROOT_PATH.'/include/funusers.php');
while($v = $users_handler->getObject()){
	$userrows[$k] = jieqi_system_usersvars($v);	
	$k++;
}
$jieqiTpl->assign_by_ref('userrows', $userrows);

$grouprows = array();
$i = 0;
foreach($jieqiGroups as $k => $v){
	if($k > 1){
		$grouprows[$i]['groupid'] = $k;
		$grouprows[$i]['groupname'] = $v;
		$i++;
	}
}
$jieqiTpl->assign_by_ref('grouprows', $grouprows);

$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));

//统计值
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = "SELECT count(*) as cot, sum(egold) as sumegold, sum(esilver) as sumesilver FROM " . jieqi_dbprefix('system_users') . " " . $criteria->renderWhere();
$query->execute($sql);
$userstat = $query->getRow();
$userstat = jieqi_funtoarray('jieqi_htmlstr', $userstat);
$jieqiTpl->assign_by_ref('userstat', $userstat);
$jieqiTpl->assign_by_ref('rowcount', $userstat['cot']);
//处理页面跳转
include_once (JIEQI_ROOT_PATH . '/lib/html/page.php');
$jieqiPset['count'] = $userstat['cot'];
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());

$jieqiTpl->setCaching(0);
include_once (JIEQI_ROOT_PATH . '/admin/footer.php');
