<?php
//易宝点卡支付相关参数（yeecard）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（不同平台可能称呼不同，如：KEY/APP KEY/APP Scret 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'http://www.yeeyk.com/yeex-xcard-app/createOrder';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/yeecardreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/yeecardreturn.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
//每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
if(in_array($_REQUEST['cardtype'], array('SZX', 'UNICOM', 'TELECOM'))){
	$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = $jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>85);
}else{
	$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = $jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>75);
}
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array(); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'GET';  //提交方式 GET 、POST

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '易宝点卡'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array(); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = ''; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['bizType'] = 'STANDARD';  //业务类型 专业版“PROFESSION”,标准版“STANDARD”
$jieqiPayset[JIEQI_PAY_TYPE]['cardCode'] = '';  //支付渠道编码
$jieqiPayset[JIEQI_PAY_TYPE]['productName'] = JIEQI_EGOLD_NAME;  //商品名
$jieqiPayset[JIEQI_PAY_TYPE]['productType'] = '';  //商品类型
$jieqiPayset[JIEQI_PAY_TYPE]['productDesc'] = '';  //商品描述
$jieqiPayset[JIEQI_PAY_TYPE]['extInfo'] = '';  //商户扩展信息

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();

?>