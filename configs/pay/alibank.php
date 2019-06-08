<?php
//支付宝网银充值相关参数（alibank）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（不同平台可能称呼不同，如：KEY/APP KEY/APP Scret 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'https://mapi.alipay.com/gateway.do';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['paycheck'] = 'http://notify.alipay.com/trade/notify_query.do';  //支付网站订单验证网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/alibankreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/alibanknotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>100, 50=>100, 100=>100);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'GET';  //提交方式 GET 、POST

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '支付宝网银'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array(); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = ''; //默认支付设备

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
$jieqiPayset[JIEQI_PAY_TYPE]['paymethod'] = 'bankPay'; //默认支付方式：bankPay=网银支付，directPay-余额支付，纯网银接口必须使用bankPay
$jieqiPayset[JIEQI_PAY_TYPE]['defaultbank'] = 'CMB'; //默认网银
/*
银行简码——混合渠道(B2B代表企业银行)
ICBCBTB=中国工商银行（B2B）
ABCBTB=中国农业银行（B2B）
CCBBTB=中国建设银行（B2B）
SPDBB2B=上海浦东发展银行（B2B）
BOCBTB=中国银行（B2B）
CMBBTB=招商银行（B2B）
BOCB2C=中国银行
ICBCB2C=中国工商银行
CMB=招商银行
CCB=中国建设银行
ABC=中国农业银行
SPDB=上海浦东发展银行
CIB=兴业银行
GDB=广发银行
FDB=富滇银行
HZCBB2C=杭州银行
SHBANK=上海银行
NBBANK=宁波银行
SPABANK=平安银行
POSTGC=中国邮政储蓄银行
abc1003=visa
abc1004=master

银行简码——纯借记卡渠道
CMB-DEBIT=招商银行
CCB-DEBIT=中国建设银行
ICBC-DEBIT=中国工商银行
COMM-DEBIT=交通银行
GDB-DEBIT=广发银行
BOC-DEBIT=中国银行
CEB-DEBIT=中国光大银行
SPDB-DEBIT=上海浦东发展银行
PSBC-DEBIT=中国邮政储蓄银行
BJBANK=北京银行
SHRCB=上海农商银行
WZCBB2C-DEBIT=温州银行
COMM=交通银行
CMBC=中国民生银行
BJRCB=北京农村商业银行
SPA-DEBIT=平安银行
CITIC-DEBIT=中信银行

交通银行简码COMM、COMM-DEBIT都代表纯借记卡渠道，二者没有区别，建议使用COMM-DEBIT。
*/

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();
?>