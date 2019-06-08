<?php
//支付宝境外充值相关参数（aliforex）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（不同平台可能称呼不同，如：KEY/APP KEY/APP Scret 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'https://mapi.alipay.com/gateway.do';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['paycheck'] = 'http://notify.alipay.com/trade/notify_query.do';  //支付网站订单验证网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/aliforexreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/aliforexnotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>100, 50=>100, 100=>100);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'GET';  //提交方式 GET 、POST

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '支付宝境外'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array(); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = ''; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['service'] = 'create_forex_trade';  //交易类型 create_direct_pay_by_user 即时到帐，create_forex_trade 境外收款
$jieqiPayset[JIEQI_PAY_TYPE]['_input_charset'] = 'GBK';  //字符集
$jieqiPayset[JIEQI_PAY_TYPE]['subject'] = JIEQI_EGOLD_NAME;  //商品名称（默认显示虚拟币名）
$jieqiPayset[JIEQI_PAY_TYPE]['body'] = '';  //商品描述，可以留空
$jieqiPayset[JIEQI_PAY_TYPE]['currency'] = 'USD'; // 货币类型 
//GBP 英镑,HKD 港币,USD 美元,CHF 瑞士法郎,SGD 新加坡元,SEK 瑞典克朗,DKK 丹麦克朗,NOK 挪威克朗,JPY 日元,CAD 加拿大元,AUD 澳大利亚元,EUR 欧元,NZD 新西兰元,RUB 俄罗斯卢布,MOP 澳门元
$jieqiPayset[JIEQI_PAY_TYPE]['payrmb'] = '0'; // 是否使用人民币金额，0-使用以上货币金额，1-使用人民币金额
$jieqiPayset[JIEQI_PAY_TYPE]['timeout_rule'] = ''; //交易超时规则 可选值有 5m 10m 15m 30m 1h 2h 3h 5h 10h 12h。 （忽略大小写）默认为12h
$jieqiPayset[JIEQI_PAY_TYPE]['specified_pay_channel'] = 'debitcard-cmb-mb2c'; //网银前置,可留空
//中国银行 debitcard-boc-mb2c, 中国建设银行 debitcard-ccb-mb2c,中国光大银行 debitcard-ceb-mb2c, 兴业银行 debitcard-cib-mb2c,中信银行 debitcard-citic-mb2c, 招商银行 debitcard-cmb-mb2c, 交通银行 debitcard-comm-mb2c, 广东发展银行, debitcard-gdb-mb2c, 杭州银行 debitcard-hzcb-mb2c, 中国工商银行 debitcard-icbc-mb2c, 宁波银行 debitcard-nbbank-mb2c, 深圳发展银行 debitcard-sdb-mb2c, 上海银行 debitcard-shbank-mb2c, 上海浦东发展银行 debitcard-spdb-mb2c

//$jieqiPayset[JIEQI_PAY_TYPE]['payment_type'] = '1';  // 商品支付类型 1＝商品购买 2＝服务购买 3＝网络拍卖 4＝捐赠 5＝邮费补偿 6＝奖金
//$jieqiPayset[JIEQI_PAY_TYPE]['show_url'] = JIEQI_LOCAL_URL;  //商品相关网站公司
//$jieqiPayset[JIEQI_PAY_TYPE]['seller_email'] = '';  //卖家邮箱，必须填写
$jieqiPayset[JIEQI_PAY_TYPE]['sign_type'] = 'MD5';  //签名方式

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();
?>