<?php
/**
 * 渠道充值月报表
 *
 * 渠道充值月报表
 *
 * 调用模板：/modules/pay/templates/chcreport.html
 *
 * @category   jieqicms
 * @package    pay
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: chcreport.php 326 2009-02-04 00:26:22Z juny $
 */

define('JIEQI_MODULE_NAME', 'pay');
require_once('../../global.php');
//检查权限
jieqi_checklogin();
jieqi_getconfigs('system', 'channels', 'jieqiChannels');
if(empty($jieqiChannels)) $jieqiChannels = array();

if(!isset($_SESSION['jieqiUserChid'])){
	$_SESSION['jieqiUserChid'] = '';
	foreach($jieqiChannels as $k => $v){
		if(!empty($v['uid']) && $v['uid'] == $_SESSION['jieqiUserId']){
			$_SESSION['jieqiUserChid'] = $k;
			break;
		}
	}
}
if(empty($_SESSION['jieqiUserChid'])) $_SESSION['jieqiUserChid'] = $_SESSION['jieqiUserId'];

jieqi_loadlang('creport', JIEQI_MODULE_NAME);

$jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/chcreport.html';
include_once(JIEQI_ROOT_PATH . '/header.php');
$jieqiPset = jieqi_get_pageset(); //分页参数
jieqi_getconfigs('pay', 'configs');

jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');


$slimit = "channel = '" . jieqi_dbslashes($_SESSION['jieqiUserChid']) . "'";
if(!isset($_REQUEST['reportmonth'])) $_REQUEST['reportmonth'] = '';
if(!isset($_REQUEST['startmonth'])) $_REQUEST['startmonth'] = '';
if(!isset($_REQUEST['endmonth'])) $_REQUEST['endmonth'] = '';
if(!isset($_REQUEST['format'])) $_REQUEST['format'] = '';

if(!empty($_REQUEST['reportmonth'])){
	if(!empty($_REQUEST['reportyear']) && strlen($_REQUEST['reportmonth']) <= 2) $reportmonth = intval($_REQUEST['reportyear'] . sprintf("%02d", $_REQUEST['reportmonth']));
	else $reportmonth = intval(str_replace(array('-', ' ', '/'), '', $_REQUEST['reportmonth']));
	$slimit .= " AND reportmonth = " . $reportmonth;
}
if(!empty($_REQUEST['startmonth'])){
	if(!empty($_REQUEST['startyear']) && strlen($_REQUEST['startmonth']) <= 2) $startmonth = intval($_REQUEST['startyear'] . sprintf("%02d", $_REQUEST['startmonth']));
	else $startmonth = intval(str_replace(array('-', ' ', '/'), '', $_REQUEST['startmonth']));
	$slimit .= " AND reportmonth >= " . $startmonth;
}
if(!empty($_REQUEST['endmonth'])){
	if(!empty($_REQUEST['endyear']) && strlen($_REQUEST['endmonth']) <= 2) $endmonth = intval($_REQUEST['endyear'] . sprintf("%02d", $_REQUEST['endmonth']));
	else $endmonth = intval(str_replace(array('-', ' ', '/'), '', $_REQUEST['endmonth']));
	$slimit .= " AND reportmonth <= " . $endmonth;
}

if(!empty($_REQUEST['format'])){
	//导出excel
	$sql = "SELECT * FROM " . jieqi_dbprefix('pay_creport') . " WHERE {$slimit} ORDER BY reportmonth DESC";
	$res = $query->execute($sql);
	jieqi_getconfigs('pay', 'exportcr', 'jieqiExport');
	include_once(JIEQI_ROOT_PATH . '/include/funexport.php');
	if(!isset($_REQUEST['format'])) $_REQUEST['format'] = 'exceltxt';
	$params = array('res' => $res, 'format' => $_REQUEST['format'], 'fields' => $jieqiExport['creport'], 'filename' => 'paymonth_' . date('Ymd') . '.xls', 'funrow' => 'jieqi_pay_getexpmrrow');
	$ret = jieqi_system_exportfile($params);
	if($ret === false) jieqi_printfail(LANG_ERROR_PARAMETER);

}
else{
	//输出显示
	$sql = "SELECT * FROM " . jieqi_dbprefix('pay_creport') . " WHERE {$slimit} ORDER BY reportmonth DESC LIMIT {$jieqiPset['start']},{$jieqiPset['rows']}";
	$res = $query->execute($sql);
	$creportrows = array();
	$k = 0;
	while($row = $query->getRow()){
		if(isset($jieqiChannels[$row['channel']])) $row['channel'] = $jieqiChannels[$row['channel']]['name'];
		$creportrows[$k] = jieqi_query_rowvars($row, 's', 'pay');
		$k++;
	}

	$jieqiTpl->assign_by_ref('creportrows', $creportrows);
	$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));

	$dyear = intval(date('Y', JIEQI_NOW_TIME));
	$dmonth = intval(date('m', JIEQI_NOW_TIME));
	if($dmonth == 1){
		$dyear--;
		$dmonth = 12;
	}
	else{
		$dmonth--;
	}
	$ryearrows = array();
	for($i = $dyear; $i >= $dyear - 10; $i--) $ryearrows[] = $i;
	$rmonthrows = array();
	for($i = 1; $i <= 12; $i++) $rmonthrows[] = $i;
	$jieqiTpl->assign('ryearrows', $ryearrows);
	$jieqiTpl->assign('rmonthrows', $rmonthrows);
	$jieqiTpl->assign('dyear', $dyear);
	$jieqiTpl->assign('dmonth', $dmonth);

	//处理页面跳转
	include_once(JIEQI_ROOT_PATH . '/lib/html/page.php');

	//总记录数
	$sql = "SELECT count(*) AS cot, sum(sumegold) as sumegold, sum(summoney) as summoney FROM " . jieqi_dbprefix('pay_creport') . " WHERE {$slimit}";
	$query->execute($sql);
	$row = $query->getRow();
	$jieqiTpl->assign('creportstat', jieqi_funtoarray('jieqi_htmlstr', $row));
	$jieqiPset['count'] = intval($row['cot']);
	$jumppage = new JieqiPage($jieqiPset);
	//$jumppage->setlink('', true, true);
	$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());

	if(empty($jieqiChannels)) $jieqiChannels = array();
	$jieqiTpl->assign('jieqi_channels', jieqi_funtoarray('jieqi_htmlstr', $jieqiChannels));
	$jieqiTpl->assign('jieqi_channelnum', count($jieqiChannels));


	jieqi_getconfigs('system', 'configs');
	$channelerate = isset($jieqiConfigs['system']['channelerate']) ? $jieqiConfigs['system']['channelerate'] : 0;
	$jieqiTpl->assign('channelerate', $channelerate);

	$channeldlimit = (isset($jieqiConfigs['system']['channeldlimit']) && is_numeric($jieqiConfigs['system']['channeldlimit'])) ? intval($jieqiConfigs['system']['channeldlimit']) : 0;
	$jieqiTpl->assign('channeldlimit', $channeldlimit);

	$exchangerate = isset($jieqiConfigs['system']['exchangerate']) ? $jieqiConfigs['system']['exchangerate'] : 0;
	$jieqiTpl->assign('exchangerate', $exchangerate);

	$settlemin = (isset($jieqiConfigs['system']['settlemin']) && is_numeric($jieqiConfigs['system']['settlemin'])) ? $jieqiConfigs['system']['settlemin'] : 0;
	$jieqiTpl->assign('settlemin', $settlemin);

	$jieqiTpl->setCaching(0);
	include_once(JIEQI_ROOT_PATH . '/footer.php');
}

function jieqi_pay_getexpmrrow($row){
	global $jieqiChannels;
	if(isset($jieqiChannels[$row['channel']])) $row['channel'] = $jieqiChannels[$row['channel']]['name'];
	$row = jieqi_query_rowvars($row, 's', 'pay');
	$money_fields = array('summoney');
	foreach($money_fields as $f){
		if(isset($row[$f])){
			$row[$f . '_n'] = $row[$f];
			$row[$f] = floatval(intval($row[$f]) / 100);
		}
	}
	return $row;
}

?>