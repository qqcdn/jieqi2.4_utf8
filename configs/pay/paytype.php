<?php
//在线支付的类型设定

$jieqiPaytype['manual'] = array('name' => '人工操作', 'shortname' => '人工', 'description'=>'', 'url' => '', 'link'=>'buyegold.php?t=manual', 'publish' => 1);

$jieqiPaytype['bank'] = array('name' => '银行汇款', 'shortname' => '银行', 'description'=>'', 'url' => '', 'link'=>'buyegold.php?t=remitpay', 'publish' => 1);

$jieqiPaytype['post'] = array('name' => '邮局汇款', 'shortname' => '邮局', 'description'=>'', 'url' => '', 'link'=>'buyegold.php?t=remitpay', 'publish' => 1);

$jieqiPaytype['alipay'] = array('name' => '支付宝', 'shortname' => '支付宝', 'description'=>'', 'url' => 'http://www.alipay.com', 'link'=>'buyegold.php?t=alipaypay', 'publish' => 1);

$jieqiPaytype['alibank'] = array('name' => '支付宝网银', 'shortname' => '支付宝网银', 'description'=>'', 'url' => 'http://www.alipay.com', 'link'=>'buyegold.php?t=alibankpay', 'publish' => 1);

$jieqiPaytype['aliwap'] = array('name' => '支付宝WAP', 'shortname' => '支付宝WAP', 'description'=>'', 'url' => 'http://www.alipay.com', 'link'=>'buyegold.php?t=aliwappay', 'publish' => 1);

$jieqiPaytype['aliforex'] = array('name' => '支付宝境外', 'shortname' => '支付宝境外', 'description'=>'', 'url' => 'http://www.alipay.com', 'link'=>'buyegold.php?t=aliforexpay', 'publish' => 1);

$jieqiPaytype['yeepay'] = array('name' => '易宝网银支付', 'shortname' => '易宝网银', 'description'=>'', 'url' => 'http://www.yeepay.com', 'link'=>'buyegold.php?t=yeepaypay', 'publish' => 1);

$jieqiPaytype['yeecard'] = array('name' => '易宝点卡支付', 'shortname' => '易宝点卡', 'description'=>'', 'url' => 'http://www.yeepay.com', 'link'=>'buyegold.php?t=yeecardpay', 'publish' => 1);

$jieqiPaytype['paypal'] = array('name' => '贝宝支付', 'shortname' => '贝宝', 'description'=>'', 'url' => 'http://www.paypal.com', 'link'=>'buyegold.php?t=paypal', 'publish' => 1);

$jieqiPaytype['lottery'] = array('name' => '抽奖红包', 'shortname' => '红包', 'description'=>'', 'url' => '', 'link'=>'', 'publish' => 0);

$jieqiPaytype['wxnative'] = array('name' => '微信扫码', 'shortname' => '微信扫码', 'description'=>'', 'url' => '', 'link'=>'', 'publish' => 0);

$jieqiPaytype['wxjsapi'] = array('name' => '微信钱包', 'shortname' => '微信钱包', 'description'=>'', 'url' => '', 'link'=>'', 'publish' => 0);

$jieqiPaytype['ipn'] = array('name' => '现在支付', 'shortname' => '现在支付', 'description'=>'', 'url' => 'http://www.ipaynow.cn/', 'link'=>'buyegold.php?t=ipnpay', 'publish' => 1, 'subtype'=>array('11'=>'银联', '12'=>'支付宝', '13'=>'微信', '25'=>'手Q', '1301'=>'微信'), 'fromtype'=>array('06'=>'手机', '0600'=>'微信', '02'=>'电脑'));

$jieqiPaytype['exchange'] = array('name' => '兑换', 'shortname' => '兑换', 'description'=>'', 'url' => '', 'link'=>'', 'publish' => 0);

?>