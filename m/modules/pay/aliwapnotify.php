<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'aliwap');
require_once '../../global.php';
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
$logflag = empty($jieqiPayset[JIEQI_PAY_TYPE]['payislog']) ? 0 : 1;
if ($logflag) {
    $logfile = JIEQI_ROOT_PATH . '/files/pay/log/' . JIEQI_PAY_TYPE . '_notify.txt';
    jieqi_checkdir(dirname($logfile), true);
    $log = print_r($_REQUEST, true) . '' . "\r\n" . '' . "\r\n" . '';
    jieqi_writefile($logfile, $log, 'ab');
}
$mymerchant_id = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
$key = $jieqiPayset[JIEQI_PAY_TYPE]['paykey'];
if (empty($_POST) || !isset($_POST['sign']) || !isset($_POST['notify_data'])) {
    exit('fail');
}
include $jieqiModules['pay']['path'] . '/include/funpay.php';
include $jieqiModules['pay']['path'] . '/include/funalipay.php';
$doc = new DOMDocument();
$doc->loadXML($_POST['notify_data']);
$notify_id = $doc->getElementsByTagName('notify_id')->item(0)->nodeValue;
if (!empty($jieqiPayset[JIEQI_PAY_TYPE]['cacert']) && is_file($jieqiPayset[JIEQI_PAY_TYPE]['cacert'])) {
    $responseTxt = 'true';
    $veryfy_url = $jieqiPayset[JIEQI_PAY_TYPE]['veryfy_url'];
    $veryfy_url .= strpos($veryfy_url, '?') === false ? '?' : '&';
    $veryfy_url .= 'partner=' . $jieqiPayset[JIEQI_PAY_TYPE]['payid'] . '&notify_id=' . $notify_id;
    $responseTxt = jieqi_pay_verifyget($veryfy_url, $jieqiPayset[JIEQI_PAY_TYPE]['cacert']);
    if ($responseTxt != 'true') {
        exit('fail');
    }
}
$sign_new = jieqi_pay_makesign(jieqi_pay_signvars($_POST), $jieqiPayset[JIEQI_PAY_TYPE]['paykey'], false);
if (strtolower($_POST['sign']) != strtolower($sign_new)) {
    exit('fail');
}
if (!empty($doc->getElementsByTagName('notify')->item(0)->nodeValue)) {
    $out_trade_no = $doc->getElementsByTagName('out_trade_no')->item(0)->nodeValue;
    $trade_no = $doc->getElementsByTagName('trade_no')->item(0)->nodeValue;
    $buyer_email = $doc->getElementsByTagName('buyer_email')->item(0)->nodeValue;
    $buyer_id = $doc->getElementsByTagName('buyer_id')->item(0)->nodeValue;
    $trade_status = $doc->getElementsByTagName('trade_status')->item(0)->nodeValue;
    if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
        $payinfo = array('orderid' => intval($out_trade_no), 'retserialno' => $trade_no, 'retaccount' => $buyer_email, 'retinfo' => $buyer_id, 'return' => true);
        jieqi_pay_return($payinfo);
        echo 'success';
    } else {
        exit('fail');
    }
} else {
    exit('fail');
}