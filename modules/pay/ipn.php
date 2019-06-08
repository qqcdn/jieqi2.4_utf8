<?php
/**
 * 现在支付手机网页版-提交处理
 *
 * 现在支付手机网页版-提交处理 (http://www.ipaynow.cn)
 *
 * 调用模板：无
 *
 * @category   jieqicms
 * @package    pay
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: ipn.php 326 2009-02-04 00:26:22Z juny $
 */

@define('JIEQI_MODULE_NAME', 'pay');
@define('JIEQI_PAY_TYPE', 'ipn');
require_once('../../global.php');
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_loadlang('ipn', JIEQI_MODULE_NAME);
if(!jieqi_checklogin(true)) jieqi_printfail($jieqiLang['pay']['need_login']);
jieqi_getconfigs('pay', JIEQI_PAY_TYPE, 'jieqiPayset');

if(!empty($jieqiPayset[JIEQI_PAY_TYPE]['payChannelType'])) $payChannelType = $jieqiPayset[JIEQI_PAY_TYPE]['payChannelType'];
elseif(!empty($_REQUEST['payChannelType'])) $payChannelType = $_REQUEST['payChannelType'];
else $payChannelType = '';

$deviceType = (!empty($_REQUEST['deviceType']) && is_numeric($_REQUEST['deviceType'])) ? $_REQUEST['deviceType'] : $jieqiPayset[JIEQI_PAY_TYPE]['deviceType'];

$_REQUEST['subtype'] = $payChannelType;
$_REQUEST['fromtype'] = $deviceType;
//充值通用函数
include($jieqiModules['pay']['path'] . '/include/funpay.php');
//提交后预处理
$paylog = jieqi_pay_start();
//现在支付通用函数
include($jieqiModules['pay']['path'] . '/include/funipn.php');

if($jieqi_charset_map[JIEQI_SYSTEM_CHARSET] != 'utf8'){
	include_once (JIEQI_ROOT_PATH . '/include/changecode.php');
	$charset_convert_payin = 'jieqi_' . $jieqi_charset_map['utf8'] . '2' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET];
	$charset_convert_payout = 'jieqi_' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET] . '2' . $jieqi_charset_map['utf8'];
}


$payvars = array();
$payvars['funcode'] = $jieqiPayset[JIEQI_PAY_TYPE]['funcode']; //功能码
if(isset($jieqiPayset[JIEQI_PAY_TYPE]['version'])) $payvars['version'] = $jieqiPayset[JIEQI_PAY_TYPE]['version']; //版本号
$payvars['appId'] = $jieqiPayset[JIEQI_PAY_TYPE]['payid']; //商户应用唯一标识
$payvars['mhtOrderNo'] = $paylog->getVar('payid'); //商户订单号
$payvars['mhtOrderName'] = empty($jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderName']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderName']; // 商品名称，必填
$payvars['mhtOrderName'] = jieqi_charsetconvert($payvars['mhtOrderName'], JIEQI_SYSTEM_CHARSET, $jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset']);
$payvars['mhtOrderType'] = $jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderType']; //商户交易类型 01普通消费 04 接口退货
$payvars['mhtCurrencyType'] = $jieqiPayset[JIEQI_PAY_TYPE]['mhtCurrencyType']; //商户订单币种类型  156人民币
$payvars['mhtOrderAmt'] = $_REQUEST['money']; //商户订单交易金额 (人民币)：分 整数，无小数点
$payvars['mhtOrderDetail'] = sprintf($jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderDetail'], $_REQUEST['egold'], $_REQUEST['amount']); //商户订单详情
$payvars['mhtOrderDetail'] = jieqi_charsetconvert($payvars['mhtOrderDetail'], JIEQI_SYSTEM_CHARSET, $jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset']);
$payvars['mhtOrderTimeOut'] = $jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderTimeOut']; //商户订单超时时间 3600 秒，默认3600
$payvars['mhtOrderStartTime'] = date('YmdHis', JIEQI_NOW_TIME); //商户订单开始时间 	yyyyMMddHHmmss
$payvars['notifyUrl'] = $jieqiPayset[JIEQI_PAY_TYPE]['paynotify']; //商户后台通知URL	notifyUrl
$payvars['frontNotifyUrl'] = $jieqiPayset[JIEQI_PAY_TYPE]['payreturn']; //商户前台通知URL
$payvars['mhtCharset'] = $jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset']; //商户字符编码 	UTF-8 GBK
$payvars['deviceType'] = $deviceType; //设备类型 0600 公众号, 06 手机网   02-电脑
$payvars['payChannelType'] = $payChannelType; //用户所选渠道类型 11银联 12支付宝 13微信，留空表示下一步选择
if(isset($jieqiPayset[JIEQI_PAY_TYPE]['mhtReserved']) && strlen($jieqiPayset[JIEQI_PAY_TYPE]['mhtReserved']) > 0)$payvars['mhtReserved'] = jieqi_charsetconvert($jieqiPayset[JIEQI_PAY_TYPE]['mhtReserved'], JIEQI_SYSTEM_CHARSET, $jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset']); //商户保留域 使用的字段，商户可以对交易进行标记，现在支付将原样返回

//微信公众号支付情况
if($payvars['deviceType'] == '0600'){
	//如果deviceType为0600，outputType为空或者0，则返回支付页面调起支付，如果outputType为1，则返回支付信息 商户需要自己做页面调起支付。当deviceType=06时，outputType时，没有此字段。
	$payvars['outputType'] = (isset($_REQUEST['outputType']) && is_numeric($_REQUEST['outputType'])) ? $_REQUEST['outputType'] : $jieqiPayset[JIEQI_PAY_TYPE]['outputType'];

	//openId是商户获取到的用户微信唯一标示。如果deviceType为0600，outputType为1，openId必须要传;	当deviceType=06时，没有此字段
	if($payvars['outputType'] == 1){
		if(isset($_SESSION['jieqiUserApi']['wxmp']['openid'])){
			$payvars['mhtSubOpenId'] = $_SESSION['jieqiUserApi']['wxmp']['openid'];
		}else{
			$payvars['outputType'] = 0;
		}
		if($payvars['outputType'] == 1){
			$payvars['mhtSubAppId'] = $jieqiPayset['ipn']['mhtSubAppId'];
		}

	}
}elseif($payvars['deviceType'] == '02' || $payvars['deviceType'] == '08'){
	//这个参数手机站支付可以不要，电脑pc微信扫码又一定要
	$payvars['outputType'] = (isset($_REQUEST['outputType']) && is_numeric($_REQUEST['outputType'])) ? $_REQUEST['outputType'] : $jieqiPayset[JIEQI_PAY_TYPE]['outputType'];
}
//$payvars['consumerId'] = $_SESSION['jieqiUserId']; //消费者在商户系统的ID，非必填，但是推荐填写，以便于辅助数据分析
//$payvars['consumerName'] = jieqi_charsetconvert($_SESSION['jieqiUserName'], JIEQI_SYSTEM_CHARSET, $jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset']); //消费者在商户系统的名称，非必填，但是推荐填写，以便于辅助数据分析

//手机网页，银联支付时必填，需要传银行卡号作为支付账号
if($payvars['deviceType'] == '06' && $payvars['payChannelType'] == '11'){
	if(empty($_REQUEST['payAccNo'])) jieqi_printfail($jieqiLang['pay']['ipn_need_accountno']);
	else $payvars['payAccNo'] = $_REQUEST['payAccNo'];
}elseif(!empty($_REQUEST['payAccNo'])) $payvars['payAccNo'] = $_REQUEST['payAccNo'];

$payvars['mhtSignType'] = $jieqiPayset[JIEQI_PAY_TYPE]['mhtSignType']; //商户签名方法 MD5
//生成签名
$payvars['mhtSignature'] = jieqi_pay_makesign($payvars, $jieqiPayset[JIEQI_PAY_TYPE]['paykey']);

//微信自己发起支付模式
if($payvars['deviceType'] == '0600' && $payvars['outputType'] == 1){
	include_once(JIEQI_ROOT_PATH . '/include/apicommon.php');
	$apirequest = new JieqiApiRequest();
	$posturl = !empty($jieqiPayset[JIEQI_PAY_TYPE]['paywxurl']) ? $jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'] : $jieqiPayset[JIEQI_PAY_TYPE]['payurl'];
	$ret = $apirequest->httpRequest($jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'], $payvars, 'POST');
	if($ret['ret'] < 0) jieqi_printfail(sprintf($jieqiLang['pay']['ipn_getwxmp_failure'], jieqi_htmlstr($ret['msg'])));
	parse_str($ret['msg'], $retary);
	//print_r($retary);
	if(!is_array($retary) || !isset($retary['appId']) || !isset($retary['responseCode']) || !isset($retary['mhtOrderNo']) || !isset($retary['nowPayOrderNo']) || !isset($retary['tn']) || $retary['responseCode'] != 'A001' || $retary['appId'] != $jieqiPayset[JIEQI_PAY_TYPE]['payid']) jieqi_printfail(sprintf($jieqiLang['pay']['ipn_wxmp_reterror'], jieqi_charsetconvert($retary['responseMsg'], $jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset'], JIEQI_SYSTEM_CHARSET)));
	parse_str($retary['tn'], $parary);
	if(!is_array($parary) || !isset($parary['nonceStr']) || !isset($parary['prepay_id']) || !isset($parary['wxAppId']) || !isset($parary['paySign']) || $parary['wxAppId'] != $payvars['mhtSubAppId']) jieqi_printfail($jieqiLang['pay']['ipn_wxmp_parerror']);
	$params = array('appId'=>$payvars['mhtSubAppId'], 'timeStamp'=>$parary['timeStamp'], 'nonceStr'=>$parary['nonceStr'], 'package'=>'prepay_id='.$parary['prepay_id'], 'signType'=>'MD5', 'paySign'=>$parary['paySign']);
	include_once(JIEQI_ROOT_PATH . '/header.php');
    $jieqiTpl->assign('buyname', $_SESSION['jieqiUserName']);
    $jieqiTpl->assign('egold', $_REQUEST['egold']);
    $jieqiTpl->assign('money', $_REQUEST['amount']);
    $subject = empty($jieqiPayset[JIEQI_PAY_TYPE]['subject']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['subject']; // 商品名称，必填
    $jieqiTpl->assign('subject', $subject);
    $jieqiTpl->assign('jsApiParameters', json_encode($params));
    $jumpurl = isset($_REQUEST['jumpurl']) ? jieqi_htmlstr($_REQUEST['jumpurl']) : '';
    $jieqiTpl->assign('jumpurl', $jumpurl);
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/ipnmmp.html';
    $jieqiTpl->setCaching(0);
    include_once(JIEQI_ROOT_PATH . '/footer.php');
	exit();
}elseif($payvars['deviceType'] == '0600'){
	if(!empty($jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'])) $jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = $jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'];
}elseif($payvars['payChannelType'] == '1301'){
	if(!empty($jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'])) $jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = $jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'];

	include_once(JIEQI_ROOT_PATH . '/include/apicommon.php');
	$apirequest = new JieqiApiRequest();
	$posturl = !empty($jieqiPayset[JIEQI_PAY_TYPE]['paywxurl']) ? $jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'] : $jieqiPayset[JIEQI_PAY_TYPE]['payurl'];
	$ret = $apirequest->httpRequest($jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'], $payvars, 'POST');
	if($ret['ret'] < 0) jieqi_printfail(sprintf($jieqiLang['pay']['ipn_getwx_failure'], jieqi_htmlstr($ret['msg'])));
	parse_str($ret['msg'], $retary);
	//print_r($retary);

	if(!is_array($retary) || !isset($retary['appId']) || !isset($retary['responseCode']) || !isset($retary['mhtOrderNo']) || !isset($retary['nowPayOrderNo']) || !isset($retary['tn']) || $retary['responseCode'] != 'A001' || $retary['appId'] != $jieqiPayset[JIEQI_PAY_TYPE]['payid']) jieqi_printfail(sprintf($jieqiLang['pay']['ipn_wx_reterror'], jieqi_charsetconvert($retary['responseMsg'], $jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset'], JIEQI_SYSTEM_CHARSET)));
	$url_query = urldecode($retary['tn']);
	include_once(JIEQI_ROOT_PATH . '/header.php');
	$jieqiTpl->assign('buyname', $_SESSION['jieqiUserName']);
	$jieqiTpl->assign('egold', $_REQUEST['egold']);
	$jieqiTpl->assign('money', $_REQUEST['amount']);
	$subject = empty($jieqiPayset[JIEQI_PAY_TYPE]['subject']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['subject']; // 商品名称，必填
	$jieqiTpl->assign('subject', $subject);
	$jieqiTpl->assign('url_query', $url_query);
	$jumpurl = isset($_REQUEST['jumpurl']) ? jieqi_htmlstr($_REQUEST['jumpurl']) : '';
	$jieqiTpl->assign('jumpurl', $jumpurl);
	$jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/ipnmwx.html';
	$jieqiTpl->setCaching(0);
	include_once(JIEQI_ROOT_PATH . '/footer.php');
	exit();
}

$query = $jieqiPayset[JIEQI_PAY_TYPE]['payurl'] . '?' . jieqi_pay_makequery($payvars, true);

if(($payvars['deviceType'] == '02' || $payvars['deviceType'] == '08') && $payvars['payChannelType'] == '13'){
	//微信扫码支付，只能获取二维码显示，不能跳转
	include_once(JIEQI_ROOT_PATH . '/header.php');
	$jieqiTpl->assign('buyname', $_SESSION['jieqiUserName']);
	$jieqiTpl->assign('egold', $_REQUEST['egold']);
	$jieqiTpl->assign('money', $_REQUEST['amount']);
	$jieqiTpl->assign('url_pay', $jieqiPayset[JIEQI_PAY_TYPE]['payurl']);
	$jieqiTpl->assign('url_query', $query);
	foreach($payvars as $k=>$v) $payvars[$k] = jieqi_htmlchars($v, ENT_QUOTES);
	$jieqiTpl->assign('payvars', $payvars);
	$jieqiTpl->assign('jumpurl', jieqi_htmlstr($_REQUEST['jumpurl']));

	//获取二维码
	//早期是直接返回 callback，后来改成url格式的tn参数
	if($payvars['deviceType'] == '02'){
		$qrdata = trim(file_get_contents($query));
		if(strlen($qrdata) > 12 && substr($qrdata, 0, 10) == 'callback(\'') $qrdata = substr($qrdata, 10, -2);
		else jieqi_printfail(sprintf($jieqiLang['pay']['ipn_getgr_failure'], jieqi_htmlstr($qrdata)));
	}else{
		include_once(JIEQI_ROOT_PATH . '/include/apicommon.php');
		$apirequest = new JieqiApiRequest();
		$posturl = !empty($jieqiPayset[JIEQI_PAY_TYPE]['paywxurl']) ? $jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'] : $jieqiPayset[JIEQI_PAY_TYPE]['payurl'];
		$ret = $apirequest->httpRequest($jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'], $payvars, 'POST');
		if($ret['ret'] < 0) jieqi_printfail(sprintf($jieqiLang['pay']['ipn_getwxmp_failure'], jieqi_htmlstr($ret['msg'])));
		parse_str($ret['msg'], $retary);
		//print_r($retary);
		if(!is_array($retary) || !isset($retary['appId']) || !isset($retary['responseCode']) || !isset($retary['mhtOrderNo']) || !isset($retary['nowPayOrderNo']) || !isset($retary['tn']) || $retary['responseCode'] != 'A001' || $retary['appId'] != $jieqiPayset[JIEQI_PAY_TYPE]['payid']) jieqi_printfail(sprintf($jieqiLang['pay']['ipn_wxmp_reterror'], jieqi_charsetconvert($retary['responseMsg'], $jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset'], JIEQI_SYSTEM_CHARSET)));
		$qrdata = $retary['tn'];
	}
	
	$jieqiTpl->assign('qrdata', jieqi_htmlstr($qrdata));
	$jieqiTset['jieqi_page_template'] = $jieqiModules['pay']['path'] . '/templates/ipnwxsm.html';
	$jieqiTpl->setCaching(0);
	include_once(JIEQI_ROOT_PATH . '/footer.php');
}else{
	//get跳转或者调用post页面
	if(isset($jieqiPayset['ipn']['payrequest']) && strtolower($jieqiPayset['ipn']['payrequest']) == 'get'){
		header('Location: ' . jieqi_headstr($query));
		exit;
	}else{
		include_once(JIEQI_ROOT_PATH . '/header.php');
		$jieqiTpl->assign('buyname', $_SESSION['jieqiUserName']);
		$jieqiTpl->assign('egold', $_REQUEST['egold']);
		$jieqiTpl->assign('money', $_REQUEST['amount']);
		$jieqiTpl->assign('url_pay', $jieqiPayset[JIEQI_PAY_TYPE]['payurl']);
		$jieqiTpl->assign('url_query', $query);
		foreach($payvars as $k=>$v) $payvars[$k] = jieqi_htmlchars($v, ENT_QUOTES);
		$jieqiTpl->assign('payvars', $payvars);
		$jieqiTpl->assign('jumpurl', jieqi_htmlstr($_REQUEST['jumpurl']));

		$jieqiTset['jieqi_page_template'] = $jieqiModules['pay']['path'] . '/templates/ipn.html';
		$jieqiTpl->setCaching(0);
		include_once(JIEQI_ROOT_PATH . '/footer.php');
	}
}
?>