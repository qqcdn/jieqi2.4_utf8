<?php
/**
 * 渠道用户增长统计
 *
 * 渠道用户增长统计
 * 
 * 调用模板：/templates/chusergrowth.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: chusergrowth.php 332 2009-02-23 09:15:08Z juny $
 */

define('JIEQI_MODULE_NAME', 'system');
require_once ('global.php');
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

jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');

$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/chusergrowth.html';
include_once (JIEQI_ROOT_PATH . '/header.php');


jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');

$slimit = "channel = '" . jieqi_dbslashes($_SESSION['jieqiUserChid']) . "'";
$datefield = '';
if(!empty($_REQUEST['datestart'])){
	$_REQUEST['datestart'] = trim(str_replace(array('-', '/', ' '), '', $_REQUEST['datestart']));
	if(strlen($_REQUEST['datestart']) == 6) $datefield = 'joinmonth';
	elseif(strlen($_REQUEST['datestart']) == 8) $datefield = 'joindate';
}
if(empty($datefield)){
	$datefield = 'joindate';
	$tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
	$_REQUEST['datestart'] = date('Ymd', mktime(0, 0, 0, (int)$tmpvar[1], (int)$tmpvar[2] - 30, (int)$tmpvar[0]));
}
if(!empty($_REQUEST['dateend'])){
	$_REQUEST['dateend'] = trim(str_replace(array('-', '/', ' '), '', $_REQUEST['dateend']));
}
if(empty($_REQUEST['dateend'])){
	$_REQUEST['dateend'] = $datefield == 'joindate' ? date('Ymd', JIEQI_NOW_TIME) : date('Ym', JIEQI_NOW_TIME);
}
$_REQUEST['datestart'] = intval($_REQUEST['datestart']);
$_REQUEST['dateend'] = intval($_REQUEST['dateend']);
$slimit .= " AND {$datefield} >= {$_REQUEST['datestart']} AND {$datefield} <= {$_REQUEST['dateend']}";
$sql = "SELECT {$datefield}, count(*) AS growthnum FROM " . jieqi_dbprefix('system_activity') . " WHERE {$slimit} GROUP BY {$datefield} ORDER BY {$datefield} DESC LIMIT 0, 100";
$query->execute($sql);
$growthrows = array();
$k = 0;
$growthmax = 0;
$growthsum = array('cot'=>0, 'sumgrowth'=>0);
while($row = $query->getRow()){
	$growthrows[$k] = jieqi_query_rowvars($row);
	$growthrows[$k]['datevalue'] = $growthrows[$k][$datefield];
	if($growthrows[$k]['growthnum'] > $growthmax) $growthmax = $growthrows[$k]['growthnum'];
	$growthsum['cot']++;
	$growthsum['sumgrowth'] += $growthrows[$k]['growthnum'];
	$k++;
}
foreach($growthrows as $k => $v){
	$growthrows[$k]['growthpercent'] = round($growthrows[$k]['growthnum'] * 100 / $growthmax, 2);
}
$jieqiTpl->assign_by_ref('growthrows', $growthrows);
//总记录数
$jieqiTpl->assign('growthsum', jieqi_funtoarray('jieqi_htmlstr', $growthsum));

$jieqiTpl->assign('datefield', $datefield);
$jieqiTpl->assign('growthmax', $growthmax);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));


$jieqiTpl->assign('jieqi_channels', jieqi_funtoarray('jieqi_htmlstr', $jieqiChannels));
$jieqiTpl->assign('jieqi_channelnum', count($jieqiChannels));

$channelerate = isset($jieqiConfigs['system']['channelerate']) ? $jieqiConfigs['system']['channelerate'] : 0;
$jieqiTpl->assign('channelerate', $channelerate);;

$channeldlimit = (isset($jieqiConfigs['system']['channeldlimit']) && is_numeric($jieqiConfigs['system']['channeldlimit'])) ? intval($jieqiConfigs['system']['channeldlimit']) : 0;
$jieqiTpl->assign('channeldlimit', $channeldlimit);

$exchangerate = isset($jieqiConfigs['system']['exchangerate']) ? $jieqiConfigs['system']['exchangerate'] : 0;
$jieqiTpl->assign('exchangerate', $exchangerate);

$settlemin = (isset($jieqiConfigs['system']['settlemin']) && is_numeric($jieqiConfigs['system']['settlemin'])) ? $jieqiConfigs['system']['settlemin'] : 0;
$jieqiTpl->assign('settlemin', $settlemin);

$jieqiTpl->setCaching(0);
include_once (JIEQI_ROOT_PATH . '/footer.php');
