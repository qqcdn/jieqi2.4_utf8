<?php
//现在支付手机网页版充值相关参数（ipn）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（不同平台可能称呼不同，如：KEY/APP KEY/APP Scret 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'http://api.ipaynow.cn';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/ipnreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/ipnnotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>100, 50=>100, 100=>100);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元

//支付方式和设备
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '现在'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array('11'=>'银联', '12'=>'支付宝', '13'=>'微信', '25'=>'手Q', '1301'=>'微信'); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array('06'=>'手机', '0600'=>'微信', '02'=>'电脑', '08'=>'主扫'); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = ''; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['funcode'] = 'WP001';  //功能码
$jieqiPayset[JIEQI_PAY_TYPE]['version'] = '1.0.0';  //版本号
$jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderName'] = JIEQI_EGOLD_NAME;  //商品名称，默认用虚拟币名
$jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderType'] = '01';  //商户交易类型 01普通消费
$jieqiPayset[JIEQI_PAY_TYPE]['mhtCurrencyType'] = '156';  //商户订单币种类型  156人民币
$jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderDetail'] = '您选择了充值%s'.JIEQI_EGOLD_NAME.'（%s元）';  //商户订单详情
$jieqiPayset[JIEQI_PAY_TYPE]['mhtOrderTimeOut'] = '3600';  //商户订单超时时间 3600 秒，默认3600
$jieqiPayset[JIEQI_PAY_TYPE]['mhtCharset'] = 'UTF-8';  //商户字符编码 UTF-8 GBK
$jieqiPayset[JIEQI_PAY_TYPE]['deviceType'] = '06';  //设备类型 0600-公众号 06-手机网  02-电脑  08-电脑主扫
$jieqiPayset[JIEQI_PAY_TYPE]['payChannelType'] = '';  //用户所选渠道类型 11银联 12支付宝 13微信 25手Q 1301微信底链，留空表示下一步选择
$jieqiPayset[JIEQI_PAY_TYPE]['mhtReserved'] = '';  //商户保留域 使用的字段，商户可以对交易进行标记，现在支付将原样返回
$jieqiPayset[JIEQI_PAY_TYPE]['outputType'] = 0; //如果deviceType为0600，outputType为空或者0，则返回支付页面调起支付，如果outputType为1，则返回支付信息 商户需要自己做页面调起支付。当deviceType=06时，outputType时，没有此字段。
$jieqiPayset[JIEQI_PAY_TYPE]['consumerId'] = ''; //消费者ID
$jieqiPayset[JIEQI_PAY_TYPE]['mhtSubAppId'] = '';  //appId是商户的微信appId。如果deviceType为0600，outputType为1，consumerId和mhtSubAppId必须要传;当deviceType=06时，没有此字段
$jieqiPayset[JIEQI_PAY_TYPE]['mhtSignType'] = 'MD5';  //商户签名方法 MD5
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'GET';  //提交方式 GET 、POST
$jieqiPayset[JIEQI_PAY_TYPE]['paywxurl'] = 'https://pay.ipaynow.cn';  //微信公众号接口提交网址

if((JIEQI_BROWSER_NAME == 'weixin' || $_POST['deviceType'] == '0600') && $_POST['payChannelType'] == 13){
	$jieqiPayset[JIEQI_PAY_TYPE]['deviceType'] = '0600';  //设备类型 0600-公众号 06-手机网  02-电脑
	$jieqiPayset[JIEQI_PAY_TYPE]['payChannelType'] = '13';  //用户所选渠道类型 11银联 12支付宝 13微信 25手Q 1301微信底链，留空表示下一步选择
	$jieqiPayset[JIEQI_PAY_TYPE]['outputType'] = 0; //如果deviceType为0600，outputType为空或者0，则返回支付页面调起支付，如果outputType为1，则返回支付信息 商户需要自己做页面调起支付。当deviceType=06时，outputType时，没有此字段。
	$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //签约的现在支付账号唯一用户号（PID）
	$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥值（KEY）（请输入实际申请的值）

	$jieqiPayset[JIEQI_PAY_TYPE]['consumerId'] = ''; //消费者ID
	$jieqiPayset[JIEQI_PAY_TYPE]['mhtSubAppId'] = '';  //商户appId
	//appId是商户的微信appId。如果deviceType为0600，outputType为1，consumerId和mhtSubAppId必须要传;当deviceType=06时，没有此字段
}elseif($_POST['payChannelType'] == 12 || $_POST['payChannelType'] == 13){
	$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = $jieqiPayset['ipn']['paywxurl'];
}

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();
?>