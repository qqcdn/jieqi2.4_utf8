<?php
//财付通支付相关参数（tenpay）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（不同平台可能称呼不同，如：KEY/APP KEY/APP Scret 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'https://gw.tenpay.com/gateway/pay.htm';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['paycheck'] = 'https://gw.tenpay.com/gateway/verifynotifyid.xml';  //支付网站订单验证网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/tenpayreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/tenpaynotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>100, 50=>100, 100=>100);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'GET';  //提交方式 GET 、POST

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '财付通'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array(); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = ''; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['sign_type'] = 'MD5'; //签名类型，取值：MD5、RSA，默认：MD5
$jieqiPayset[JIEQI_PAY_TYPE]['service_version'] = '1.0'; //版本号，默认为1.0
$jieqiPayset[JIEQI_PAY_TYPE]['input_charset'] = 'GBK'; //字符编码,取值：GBK、UTF-8，默认：GBK。
$jieqiPayset[JIEQI_PAY_TYPE]['sign_key_index'] = '1'; //多密钥支持的密钥序号，默认1
$jieqiPayset[JIEQI_PAY_TYPE]['body'] = '';  //商品描述，默认留空即使用虚拟币名称
$jieqiPayset[JIEQI_PAY_TYPE]['bank_type'] = 'DEFAULT'; //银行类型，默认为“DEFAULT”－财付通支付中心。银行直连编码及额度请与技术支持联系 交易模式为中介担保时此参数无效
$jieqiPayset[JIEQI_PAY_TYPE]['attach'] = ''; //附加数据，原样返回
$jieqiPayset[JIEQI_PAY_TYPE]['fee_type'] = '1'; //现金支付币种,取值：1（人民币）,默认值是1，暂只支持1

$jieqiPayset[JIEQI_PAY_TYPE]['trade_mode'] = '1'; //交易模式:1即时到账(默认) 2中介担保 3后台选择（买家进支付中心列表选择）
$jieqiPayset[JIEQI_PAY_TYPE]['trans_type'] = '2'; //交易类型：1、实物交易 2、虚拟交易 交易模式为中介担保时此参数有效

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();
?>