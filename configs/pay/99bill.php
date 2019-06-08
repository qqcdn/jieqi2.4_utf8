<?php
//快钱充值相关参数（99bill）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //商户编号（不同平台可能称呼不同，如：PID/APP ID 等，请输入支付网站实际申请的值）
$jieqiPayset[JIEQI_PAY_TYPE]['privatekey'] = JIEQI_ROOT_PATH . '/configs/pay/99bill/pcarduser.pem';  //通讯私钥文件名
$jieqiPayset[JIEQI_PAY_TYPE]['publickey'] = JIEQI_ROOT_PATH . '/configs/pay/99bill/99bill.cert.rsa.cer';  //通讯公钥文件名
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'https://www.99bill.com/gateway/recvMerchantInfoAction.htm';  //支付网站订单创建网址
//电脑站提交到 https://www.99bill.com/gateway/recvMerchantInfoAction.htm
//手机站提交到 https://www.99bill.com/mobilegateway/recvMerchantInfoAction.htm
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/99billreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/99billnotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>100, 50=>100, 100=>100);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '1000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 0; //金额类型：0-人民币 1-美元
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'GET';  //提交方式 GET 、POST

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = '快钱'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array('pc'=>'电脑', 'mob'=>手机); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = 'pc'; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['inputCharset'] = '2';  //字符集 固定选择值：1、2、3；1代表UTF-8; 2代表GBK; 3代表GB2312
$jieqiPayset[JIEQI_PAY_TYPE]['version'] = 'v2.0';  //网关版本，电脑版：v2.0 ， 手机版：mobile1.0
$jieqiPayset[JIEQI_PAY_TYPE]['mobileGateway'] = '';  //移动网关版本，当version= mobile1.0时有效 phone代表手机版移动网关，pad代表平板移动网关，默认为phone
$jieqiPayset[JIEQI_PAY_TYPE]['language'] = '1';  //诧言种类，固定值：1；1代表中文显示
$jieqiPayset[JIEQI_PAY_TYPE]['signType'] = '4';  //签名类型；4代表DSA戒者RSA签名方式
$jieqiPayset[JIEQI_PAY_TYPE]['productName'] = ''; //商品名称(默认留空使用虚拟币名称)
$jieqiPayset[JIEQI_PAY_TYPE]['productDesc'] = ''; //商品描述(默认留空)
$jieqiPayset[JIEQI_PAY_TYPE]['redoFlag'] = '1';  //同一订单禁止重复提交标志；固定选择值： 1、0 1代表同一订单号只允许提交1次；0表示同一订单号在没有支付成功的前提下可重复提交多次。 默讣为0 建议实物购物车结算类商户采用0；虚拟产品类商户采用1；
$jieqiPayset[JIEQI_PAY_TYPE]['ext1'] = '';  //扩展字段1，默认不需要填写
$jieqiPayset[JIEQI_PAY_TYPE]['ext2'] = '';  //扩展字段2，默认不需要填写
$jieqiPayset[JIEQI_PAY_TYPE]['payType'] = '00';  //支付方式
/*
[电脑版payType参数]
固定选择值：00、10、12、13、14、17、21、22 00代表显示快钱各支付方式列表（默认开通10、12、13三种支付方式）；
10代表只显示银行卡支付方式；
10-1 代表储蓄卡网银支付；10-2 代表信用卡网银支付
12代表只显示快钱账户支付方式；
13代表只显示线下支付方式；
14代表显示企业网银支付；
17预付卡支付;
21 快捷支付
21-1 代表储蓄卡快捷；21-2 代表信用卡快捷；
23 分期支付
23-2代表信用卡快捷分期支付
*其中”-”只允许在半角状态下输入,无字符集限制.
*企业网银支付、信用卡无卡支付/快捷支付、手机语音支付、预付卡支付、分期支付需单独申请，默认不开通。
*/
/*
[手机版payType参数]
固定选择值：00、15、21、21-1、21-2
00代表显示快钱各支付方式列表；
15信用卡无卡支付
21 快捷支付
21-1 代表储蓄卡快捷；21-2 代表信用卡快捷
*其中”-”只允许在半角状态下输入。
*/
//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();


?>