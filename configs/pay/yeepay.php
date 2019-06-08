<?php
//易宝网银支付相关参数（yeepay）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（不同平台可能称呼不同，如：KEY/APP KEY/APP Scret 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'https://www.yeepay.com/app-merchant-proxy/node';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/yeepayreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/yeepaynotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>100, 50=>100, 100=>100);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '易宝'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array(); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = ''; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['addressFlag'] = '0';  //需要填写送货信息 0：不需要  1:需要
$jieqiPayset[JIEQI_PAY_TYPE]['messageType'] = 'Buy';  //业务类型
$jieqiPayset[JIEQI_PAY_TYPE]['cur'] = 'CNY';  //货币单位
$jieqiPayset[JIEQI_PAY_TYPE]['productId'] = JIEQI_EGOLD_NAME;  //商品名
$jieqiPayset[JIEQI_PAY_TYPE]['productDesc'] = JIEQI_EGOLD_NAME;  //商品描述
$jieqiPayset[JIEQI_PAY_TYPE]['productCat'] = '';  //商品种类
$jieqiPayset[JIEQI_PAY_TYPE]['sMctProperties'] = '';  //附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['frpId'] = 'ICBC-NET-B2C';  //支付通道，默认工行
$jieqiPayset[JIEQI_PAY_TYPE]['needResponse'] = '0';  //是否需要应答机制，默认或"0"为不需要,"1"为需要

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();

?>