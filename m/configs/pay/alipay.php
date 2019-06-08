<?php
//支付宝充值相关参数（alipay）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（不同平台可能称呼不同，如：KEY/APP KEY/APP Scret 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'https://mapi.alipay.com/gateway.do';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['paycheck'] = 'http://notify.alipay.com/trade/notify_query.do';  //支付网站订单验证网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/alipayreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/alipaynotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>100, 50=>100, 100=>100);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'GET';  //提交方式 GET 、POST

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '支付宝'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array('pc'=>'电脑', 'mob'=>手机); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = 'pc'; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['service'] = 'create_direct_pay_by_user';  //交易类型，无需修改
$jieqiPayset[JIEQI_PAY_TYPE]['_input_charset'] = 'GBK';  //字符集，无需修改
$jieqiPayset[JIEQI_PAY_TYPE]['subject'] = JIEQI_EGOLD_NAME;  //商品名称（默认显示虚拟币名）
$jieqiPayset[JIEQI_PAY_TYPE]['body'] = '';  //商品描述，可以留空
$jieqiPayset[JIEQI_PAY_TYPE]['payment_type'] = '1';  // 商品支付类型 1-商品购买 4-捐赠 47-电子卡券，无需修改
$jieqiPayset[JIEQI_PAY_TYPE]['show_url'] = JIEQI_LOCAL_URL;  //商品相关网站公司
$jieqiPayset[JIEQI_PAY_TYPE]['seller_id'] = '';  //卖家支付宝用户号，以2088开头的纯16位数字，默认留空，表示跟上面的签约的支付宝账号相同
$jieqiPayset[JIEQI_PAY_TYPE]['seller_email'] = '';  //卖家支付宝账号，格式为邮箱或手机号，默认留空，表示跟上面的签约的支付宝账号相同
$jieqiPayset[JIEQI_PAY_TYPE]['sign_type'] = 'MD5';  //签名方式
$jieqiPayset[JIEQI_PAY_TYPE]['paymethod'] = ''; //默认支付方式：creditPay=信用支付，directPay-余额支付，可以留空表示默认使用余额支付
$jieqiPayset[JIEQI_PAY_TYPE]['enable_paymethod'] = ''; //支付渠道，可支持多种支付渠道显示，以“^”分隔，如directPay^bankPay^cartoon^cash（留空默认支持多渠道）
//支付渠道选项  directPay-支付宝账户余额，cartoon-卡通，bankPay-网银，cash-现金，creditCardExpress-信用卡快捷，debitCardExpress-借记卡快捷，coupon-红包

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();


?>