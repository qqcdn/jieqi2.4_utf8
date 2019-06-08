<?php
//paypal支付相关参数（paypal）

//基础参数
$jieqiPayset[JIEQI_PAY_TYPE]['payid'] = '';  //收款账号(默认email账号)
$jieqiPayset[JIEQI_PAY_TYPE]['paykey'] = '';  //通讯密钥（默认不需要）
$jieqiPayset[JIEQI_PAY_TYPE]['payurl'] = 'https://www.paypal.com/cgi-bin/webscr';  //支付网站订单创建网址
$jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/paypalreturn.php';  //本站接收前台返回的网址
$jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = JIEQI_LOCAL_URL.'/modules/pay/paypalnotify.php';  //本站接收后台通知的网址

//充值比例
$jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open'=>0, 'min'=>0, 'dec'=>0); //自定义充值金额设置： open-是否允许自定义金额（0-不允许，1-允许） min-最小充值金额（元），dec-金额允许几位小数（0,1,2）
$jieqiPayset[JIEQI_PAY_TYPE]['payrate'] = array(0=>600, 50=>600, 100=>600);  //每充值1元钱兑换虚拟币比例，配合上一条设置使用，可设置成金额越大比例越高，比如 array(0=>100, 50=>110, 100=>120) 表示 默认1:100，大于等于50元1:110，大于等于100元1:120
$jieqiPayset[JIEQI_PAY_TYPE]['paylimit'] = array('6000'=>'10', '12000'=>'20', '30000'=>'50', '60000'=>'100'); //充值虚拟币选项：按“虚拟币=>金额”设置，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset[JIEQI_PAY_TYPE]['paydefault'] = '6000'; //默认选中的虚拟币选项，配合上一条设置使用
$jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 6; //每充值1元钱增加多少会员积分
$jieqiPayset[JIEQI_PAY_TYPE]['moneytype'] = 1;  //现金类型：0-人民币 1-美元
$jieqiPayset[JIEQI_PAY_TYPE]['payrequest'] = 'POST';  //提交方式 GET 、POST

//充值类型
$jieqiPayset[JIEQI_PAY_TYPE]['paytype'] = 'PayPal'; //支付类型
$jieqiPayset[JIEQI_PAY_TYPE]['subtype'] = array(); //支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'] = ''; //默认支付方式
$jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array(); //支付设备
$jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = ''; //默认支付设备

//私有参数
$jieqiPayset[JIEQI_PAY_TYPE]['cmd'] = '_xclick';  //支付命令
$jieqiPayset[JIEQI_PAY_TYPE]['item_name'] = JIEQI_EGOLD_NAME;  //商品名 英文数字 JIEQI_EGOLD_NAME
$jieqiPayset[JIEQI_PAY_TYPE]['charset'] = JIEQI_SYSTEM_CHARSET;  //编码 针对有中文内容
$jieqiPayset[JIEQI_PAY_TYPE]['currency_code'] = 'USD';  //货币类型 USD-美元  HKD-港币 TWD-新台币
$jieqiPayset[JIEQI_PAY_TYPE]['rm'] = '1'; //支付成功返回时候是否返回提交过去的参数（GET方式）1-返回参数 0-不返回
$jieqiPayset[JIEQI_PAY_TYPE]['cancel_return'] = JIEQI_LOCAL_URL.'/modules/pay/buyegold.php?t=paypalpay';  //取消购买返回地址
$jieqiPayset[JIEQI_PAY_TYPE]['no_shipping'] = '1'; //有没有收货地址,1-不需要收货地址 0-需要收货地址
$jieqiPayset[JIEQI_PAY_TYPE]['no_note'] = '1';  //为付款加入提示。如果设为 "1"，则不会提示您的客户输入提示。该变量为可选项；如果省略或设为 "0"，将提示您的客户输入提示。
$jieqiPayset[JIEQI_PAY_TYPE]['image_url'] = ''; //显示本站支付的图片，150*50

//附加参数
$jieqiPayset[JIEQI_PAY_TYPE]['addvars'] = array();
?>