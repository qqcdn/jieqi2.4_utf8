<?php 
/**
 * 易宝支付-返回处理
 *
 * 易宝支付-返回处理 （ http://www.yeepay.com）
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    pay
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: yeecardreturn.php 234 2008-11-28 01:53:06Z juny $
 */

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'yeecard');
require_once('../../global.php');
require_once('yeecardcommon.php'); //易宝支付接口公共函数
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');

$logflag = (empty($jieqiPayset[JIEQI_PAY_TYPE]['payislog'])) ? 0 : 1; // 是否记录日志
if($logflag){
	$logfile = JIEQI_ROOT_PATH . '/files/pay/log/'.JIEQI_PAY_TYPE.'_return.txt';
	jieqi_checkdir(dirname($logfile), true);
	$log = print_r($_REQUEST, true) . "\r\n\r\n";
	jieqi_writefile($logfile, $log, 'ab');
}

#	只有支付成功时易宝支付才会通知商户.

#	解析返回参数.
$return = getCallBackValue($_REQUEST['bizType'],$_REQUEST['result'],$_REQUEST['merchantNo'],$_REQUEST['merchantOrderNo'],$_REQUEST['successAmount'],$_REQUEST['cardCode'],$_REQUEST['noticeType'],$_REQUEST['extInfo'],$_REQUEST['cardNo'],$_REQUEST['cardStatus'],$_REQUEST['cardReturnInfo'],$_REQUEST['cardIsbalance'],$_REQUEST['cardBalance'],$_REQUEST['cardSuccessAmount'],$_REQUEST['hmac']);

#	判断返回签名是否正确（True/False）
$bRet = CheckHmac($_REQUEST['bizType'],$_REQUEST['result'],$_REQUEST['merchantNo'],$_REQUEST['merchantOrderNo'],$_REQUEST['successAmount'],$_REQUEST['cardCode'],$_REQUEST['noticeType'],$_REQUEST['extInfo'],$_REQUEST['cardNo'],$_REQUEST['cardStatus'],$_REQUEST['cardReturnInfo'],$_REQUEST['cardIsbalance'],$_REQUEST['cardBalance'],$_REQUEST['cardSuccessAmount'],$_REQUEST['hmac']);
#	以上代码和变量不需要修改.

#	校验码正确.
if($bRet){
	if(trim(strtolower($_REQUEST['result'])) != 'success') exit('SUCCESS');
	elseif($_REQUEST['merchantNo'] != $jieqiPayset[JIEQI_PAY_TYPE]['payid']) exit('SUCCESS');
	else{
		echo 'SUCCESS';
		$payinfo = array('orderid'=>intval($_REQUEST['merchantOrderNo']), 'retserialno'=>'', 'retaccount'=>$_REQUEST['cardCode'].'||'.$_REQUEST['cardNo'], 'retinfo'=>$_REQUEST['successAmount'], 'return'=>true);
		$payret = jieqi_pay_return($payinfo);
		exit;
	}
}else{
	exit('FAILURE');
}

?>