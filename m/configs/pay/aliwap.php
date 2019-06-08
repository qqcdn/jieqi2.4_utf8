<?php
//支付宝手机网页版充值相关参数

$jieqiPayset['aliwap']['payid'] = '123456';  //签约的支付宝账号唯一用户号（PID），以2088开头的16位纯数字组成（请输入实际申请的值）

$jieqiPayset['aliwap']['paykey'] = '000000';  //通讯密钥值（KEY）（请输入实际申请的值）

$jieqiPayset['aliwap']['payurl'] = 'http://wappaygw.alipay.com/service/rest.htm';  //提交到支付网站的网址

$jieqiPayset['aliwap']['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/aliwapreturn.php';  //本站接收返回的网址

$jieqiPayset['aliwap']['payrate'] = 100; //默认充值1元钱兑换虚拟币的值
$jieqiPayset['aliwap']['paycustom'] = 0; //是否允许自定义购买金额，0-不允许，1-允许
$jieqiPayset['aliwap']['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //允许选择的 虚拟币=>金额 选项，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset['aliwap']['paydefault'] = '1000'; //默认充值虚拟币

//以下私有参数
$jieqiPayset['aliwap']['service_auth'] = 'alipay.wap.trade.create.direct';  //即时到帐授权接口名
$jieqiPayset['aliwap']['service_trade'] = 'alipay.wap.auth.authAndExecute';  //即时到帐交易接口名
$jieqiPayset['aliwap']['format'] = 'xml';  //请求参数格式
$jieqiPayset['aliwap']['v'] = '2.0';  //接口版本号
$jieqiPayset['aliwap']['sec_id'] = 'MD5';  //签名的方式

$jieqiPayset['aliwap']['notify_url'] = JIEQI_LOCAL_URL.'/modules/pay/aliwapnotify.php'; //本站接收异步返回的网址

//https://mapi.alipay.com/gateway.do?service=notify_verify
//http://notify.alipay.com/trade/notify_query.do
$jieqiPayset['aliwap']['verify_url'] = 'http://notify.alipay.com/trade/notify_query.do'; //HTTP形式消息验证地址

$jieqiPayset['aliwap']['seller_email'] = '';  //卖家邮箱，必须填写
$jieqiPayset['aliwap']['subject'] = JIEQI_EGOLD_NAME;  //商品名称（默认显示虚拟币名）
$jieqiPayset['aliwap']['merchant_url'] = JIEQI_LOCAL_URL;  //交易中断返回地址
$jieqiPayset['aliwap']['_input_charset'] = 'utf-8';  //字符集

//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中 (目前不用)
// $jieqiPayset['aliwap']['cacert'] = getcwd().'/cacert.pem';

$jieqiPayset['aliwap']['addvars'] = array();  //附加参数
?>