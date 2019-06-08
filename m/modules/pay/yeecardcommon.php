<?php
/*
 * @Description 易宝支付产品通用接口范例
 * @V3.0
 * @Author rui.xin
 */
#时间设置
//date_default_timezone_set('prc');
#	产品通用接口请求地址
//$reqURL_onLine = "http://www.yeeyk.com/yeex-xcard-app/createOrder";//新地址


# 业务类型
# 支付请求，固定值"Buy" .
//$p0_Cmd = "Buy";

#	送货地址
# 为"1": 需要用户将送货地址留在易宝支付系统;为"0": 不需要，默认为 "0".
$p9_SAF = "0";

#签名函数生成签名串
function getReqHmacString($bizType,$merchantNo,$merchantOrderNo,$requestAmount,$url,$cardCode,$productName,$productType,$productDesc,$extInfo)
{
	//global $p0_Cmd;
	global $p9_SAF;
	global $merchantNo, $merchantKey, $logName;
	//require_once ("merchantProperties.php");
	//include 'merchantProperties.php';
	#进行签名处理，一定按照文档中标明的签名顺序进行
	$sbOld = "";
	#加入业务类型
	//$sbOld = $sbOld.$p0_Cmd;
	$sbOld = $sbOld . $bizType;
	#加入商户编号
	$sbOld = $sbOld . $merchantNo;
	#加入商户订单号
	$sbOld = $sbOld . $merchantOrderNo;
	#加入支付金额
	$sbOld = $sbOld . $requestAmount;
	#加入商户接收支付成功数据的地址
	$sbOld = $sbOld . $url;
	#加入支付通道编码
	$sbOld = $sbOld . $cardCode;
	#加入商品名称
	$sbOld = $sbOld . $productName;
	#加入商品分类
	$sbOld = $sbOld . $productType;
	#加入商品描述
	$sbOld = $sbOld . $productDesc;
	#加入商户扩展信息
	$sbOld = $sbOld . $extInfo;
	#加入是否需要应答机制
	//$sbOld = $sbOld . $pr_NeedResponse;
	logstr($p2_Order, $sbOld, HmacMd5($sbOld, $merchantKey));
	return HmacMd5($sbOld, $merchantKey);

}

function getCallbackHmacString($bizType,$result,$merchantNo,$merchantOrderNo,$successAmount,$cardCode,$noticeType,$extInfo,$cardNo,$cardStatus,$cardReturnInfo,$cardIsbalance,$cardBalance,$cardSuccessAmount)
{
	global $merchantNo, $merchantKey, $logName;

	//include 'merchantProperties.php';

	#取得加密前的字符串
	$sbOld = "";
	#业务类型
	$sbOld = $sbOld . $bizType;
	#支付结果
	$sbOld = $sbOld . $result;
	#商户编号
	$sbOld = $sbOld . $merchantNo;
	#商户订单号
	$sbOld = $sbOld . $merchantOrderNo;
	#成功金额
	$sbOld = $sbOld . $successAmount;
	#支付方式
	$sbOld = $sbOld . $cardCode;
	#通知类型
	$sbOld = $sbOld . $noticeType;
	#扩展信息
	$sbOld = $sbOld . $extInfo;
	#卡序列号组
	$sbOld = $sbOld . $cardNo;
	#卡状态组
	$sbOld = $sbOld . $cardStatus;
	#卡处理描述
	$sbOld = $sbOld . $cardReturnInfo;
	#是否为余额卡
	$sbOld = $sbOld . $cardIsbalance;
	#卡余额
	$sbOld = $sbOld . $cardBalance;
	#卡成功金额
	$sbOld = $sbOld . $cardSuccessAmount;

	logstr($r6_Order, $sbOld, HmacMd5($sbOld, $merchantKey));
	return HmacMd5($sbOld, $merchantKey);
}


#	取得返回串中的所有参数
function getCallBackValue(&$bizType,&$result,&$merchantNo,&$merchantOrderNo,&$successAmount,&$cardCode,&$noticeType,&$extInfo,&$cardNo,&$cardStatus,&$cardReturnInfo,&$cardIsbalance,&$cardBalance,&$cardSuccessAmount,&$hmac)
{
	$bizType = $_REQUEST['bizType'];
	$result = $_REQUEST['result'];
	$merchantNo = $_REQUEST['merchantNo'];
	$merchantOrderNo = $_REQUEST['merchantOrderNo'];
	$successAmount = $_REQUEST['successAmount'];
	$cardCode = $_REQUEST['cardCode'];
	$noticeType = $_REQUEST['noticeType'];
	$extInfo = $_REQUEST['extInfo'];
	$cardNo = $_REQUEST['cardNo'];
	$cardStatus = $_REQUEST['cardStatus'];
	$cardReturnInfo = $_REQUEST['cardReturnInfo'];
	$cardIsbalance = $_REQUEST['cardIsbalance'];
	$cardBalance = $_REQUEST['cardBalance'];
	$cardSuccessAmount = $_REQUEST['cardSuccessAmount'];
	$hmac = $_REQUEST['hmac'];

	return null;
}

function CheckHmac($bizType,$result,$merchantNo,$merchantOrderNo,$successAmount,$cardCode,$noticeType,$extInfo,$cardNo,$cardStatus,$cardReturnInfo,$cardIsbalance,$cardBalance,$cardSuccessAmount,$hmac)
{
	if ($hmac == getCallbackHmacString($bizType,$result,$merchantNo,$merchantOrderNo,$successAmount,$cardCode,$noticeType,$extInfo,$cardNo,$cardStatus,$cardReturnInfo,$cardIsbalance,$cardBalance,$cardSuccessAmount))
		return true;
	else
		return false;
}


function HmacMd5($data, $key)
{
	// RFC 2104 HMAC implementation for php.
	// Creates an md5 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Hacked by Lance Rushing(NOTE: Hacked means written)


	$b = 64; // byte length for md5
	if (strlen($key) > $b) {
		$key = pack("H*", md5($key));
	}
	$key = str_pad($key, $b, chr(0x00));
	$ipad = str_pad('', $b, chr(0x36));
	$opad = str_pad('', $b, chr(0x5c));
	$k_ipad = $key ^ $ipad;
	$k_opad = $key ^ $opad;

	return md5($k_opad . pack("H*", md5($k_ipad . $data)));
}

function logstr($orderid, $str, $hmac)
{
	//include 'merchantProperties.php';
	/*
	global $merchantNo, $merchantKey, $logName;

	$james = fopen($logName, "a+");
	fwrite($james, "\r\n" . date("Y-m-d H:i:s") . "|orderid[" . $orderid . "]|str[" . $str . "]|hmac[" . $hmac . "]");
	fclose($james);
	*/
}

?> 