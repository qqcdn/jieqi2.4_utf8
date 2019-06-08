<?php
/**
 * 易宝支付-提交处理
 *
 * 易宝支付-提交处理 （ http://www.yeepay.com）
 * 
 * 调用模板：/modules/pay/templates/yeecard.html
 * 
 * @category   jieqicms
 * @package    pay
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: yeepay.php 326 2009-02-04 00:26:22Z juny $
 */

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'yeecard');
require_once ('../../global.php');
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_loadlang('yeecard', JIEQI_MODULE_NAME);
if(!jieqi_checklogin(true)) jieqi_printfail($jieqiLang['pay']['need_login']);
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');

//充值通用函数
include($jieqiModules['pay']['path'] . '/include/funpay.php');
//提交后预处理
$paylog = jieqi_pay_start();

$merchantNo = $jieqiPayset[JIEQI_PAY_TYPE]['payid']; //商户编号
$merchantKey = $jieqiPayset[JIEQI_PAY_TYPE]['paykey']; //密钥
$logName = "YeePay_CARD.log"; //日志文件

require_once ('yeecardcommon.php'); //易宝支付接口公共函数

if($jieqi_charset_map[JIEQI_SYSTEM_CHARSET] != 'utf8'){
	include_once (JIEQI_ROOT_PATH . '/include/changecode.php');
	$charset_convert_payin = 'jieqi_' . $jieqi_charset_map['utf8'] . '2' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET];
	$charset_convert_payout = 'jieqi_' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET] . '2' . $jieqi_charset_map['utf8'];
}

$payvars = array();

#业务类型
$payvars['bizType'] = $jieqiPayset[JIEQI_PAY_TYPE]['bizType'];
#商户编号
$payvars['merchantNo'] = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
#	商户订单号,选填.
##若不为""，提交的订单号必须在自身账户交易中唯一;为""时，易宝支付会自动生成随机的商户订单号.
$payvars['merchantOrderNo'] = $paylog->getVar('payid');
#	支付金额,必填.
##单位:元，精确到分.
$payvars['requestAmount'] = $_REQUEST['amount'];

#回调地址.
$payvars['url'] = $jieqiPayset[JIEQI_PAY_TYPE]['paynotify'];

#支付渠道编码
$payvars['cardCode'] = trim($_REQUEST['cardtype']) != '' ? trim($_REQUEST['cardtype']) : $jieqiPayset[JIEQI_PAY_TYPE]['cardCode'];

#产品名称
$payvars['productName'] = empty($jieqiPayset[JIEQI_PAY_TYPE]['productName']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['productName'];
if($jieqi_charset_map[JIEQI_SYSTEM_CHARSET] != 'utf8') $payvars['productName'] = $charset_convert_payout($payvars['productName']);

#产品类型
$payvars['productType'] = $jieqiPayset[JIEQI_PAY_TYPE]['productType'];
if($jieqi_charset_map[JIEQI_SYSTEM_CHARSET] != 'utf8') $payvars['productType'] = $charset_convert_payout($payvars['productType']);

#产品描述
$payvars['productDesc'] = $jieqiPayset[JIEQI_PAY_TYPE]['productDesc'];
if($jieqi_charset_map[JIEQI_SYSTEM_CHARSET] != 'utf8') $payvars['productDesc'] = $charset_convert_payout($payvars['productDesc']);

#商户扩展信息
$payvars['extInfo'] = $jieqiPayset[JIEQI_PAY_TYPE]['extInfo'];
if($jieqi_charset_map[JIEQI_SYSTEM_CHARSET] != 'utf8') $payvars['extInfo'] = $charset_convert_payout($payvars['extInfo']);


#调用签名函数生成签名串
$payvars['hmac'] = getReqHmacString($payvars['bizType'],$payvars['merchantNo'],$payvars['merchantOrderNo'],$payvars['requestAmount'],$payvars['url'],$payvars['cardCode'],$payvars['productName'],$payvars['productType'],$payvars['productDesc'],$payvars['extInfo']);



$query = $jieqiPayset[JIEQI_PAY_TYPE]['payurl'] . '?' . jieqi_pay_makequery($payvars, true);
$jieqiTset['jieqi_page_template'] = $jieqiModules['pay']['path'] . '/templates/' . JIEQI_PAY_TYPE . '.html';

if(isset($jieqiPayset[JIEQI_PAY_TYPE]['payrequest']) && strtoupper($jieqiPayset[JIEQI_PAY_TYPE]['payrequest']) == 'POST'){
	include_once(JIEQI_ROOT_PATH . '/header.php');
	$jieqiTpl->assign('buyname', $_SESSION['jieqiUserName']);
	$jieqiTpl->assign('egold', $_REQUEST['egold']);
	$jieqiTpl->assign('money', $_REQUEST['amount']);
	$jieqiTpl->assign('url_pay', $jieqiPayset[JIEQI_PAY_TYPE]['payurl']);
	$jieqiTpl->assign('url_query', $query);
	foreach($payvars as $k => $v) $payvars[$k] = jieqi_htmlchars($v, ENT_QUOTES);
	$jieqiTpl->assign('payvars', $payvars);
	$jieqiTpl->assign('jumpurl', jieqi_htmlstr($_REQUEST['jumpurl']));
	$jieqiTpl->setCaching(0);
	include_once(JIEQI_ROOT_PATH . '/footer.php');
}
else{
	header('Location: ' . jieqi_headstr($query));
	exit;
}

?>