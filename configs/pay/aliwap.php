<?php
//支付宝手机网页版充值相关参数（aliwap）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（不同平台可能称呼不同，如：KEY/APP KEY/APP Scret 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'http://wappaygw.alipay.com/service/rest.htm';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/aliwapreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/aliwapnotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>100, 50=>100, 100=>100);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'GET';  //提交方式 GET 、POST

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '支付宝WAP'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array('pc'=>'电脑', 'mob'=>手机); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = 'mob'; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['service_auth'] = 'alipay.wap.trade.create.direct';  //即时到帐授权接口名
$jieqiPayset[JIEQI_PAY_TYPE]['service_trade'] = 'alipay.wap.auth.authAndExecute';  //即时到帐交易接口名
$jieqiPayset[JIEQI_PAY_TYPE]['format'] = 'xml';  //请求参数格式
$jieqiPayset[JIEQI_PAY_TYPE]['v'] = '2.0';  //接口版本号
$jieqiPayset[JIEQI_PAY_TYPE]['sec_id'] = 'MD5';  //签名的方式
$jieqiPayset[JIEQI_PAY_TYPE]['seller_email'] = '';  //卖家邮箱，必须填写
$jieqiPayset[JIEQI_PAY_TYPE]['subject'] = JIEQI_EGOLD_NAME;  //商品名称（默认显示虚拟币名）
$jieqiPayset[JIEQI_PAY_TYPE]['merchant_url'] = JIEQI_LOCAL_URL;  //交易中断返回地址
$jieqiPayset[JIEQI_PAY_TYPE]['_input_charset'] = 'utf-8';  //字符集
//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中 (目前不用)
// $jieqiPayset[JIEQI_PAY_TYPE]['cacert'] = getcwd().'/cacert.pem';

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();
?>