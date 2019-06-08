<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'tenpay');
require_once '../../global.php';
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
$logflag = empty($jieqiPayset[JIEQI_PAY_TYPE]['payislog']) ? 0 : 1;
if ($logflag) {
    $logfile = JIEQI_ROOT_PATH . '/files/pay/log/' . JIEQI_PAY_TYPE . '_return.txt';
    jieqi_checkdir(dirname($logfile), true);
    $log = print_r($_REQUEST, true) . '' . "\r\n" . '' . "\r\n" . '';
    jieqi_writefile($logfile, $log, 'ab');
}
$payid = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
$payvars = array();
foreach ($_GET as $k => $v) {
    $payvars[$k] = $v;
}
foreach ($_POST as $k => $v) {
    $payvars[$k] = $v;
}
$sign = jieqi_pay_getsign($payvars, array('key' => $jieqiPayset[JIEQI_PAY_TYPE]['paykey'], 'kname' => 'key', 'urlencode' => false, 'case' => 'upper'));
$partner = $payvars['partner'];
$notify_id = $payvars['notify_id'];
$out_trade_no = $payvars['out_trade_no'];
$transaction_id = $payvars['transaction_id'];
$total_fee = $payvars['total_fee'];
$discount = $payvars['discount'];
$trade_state = $payvars['trade_state'];
$trade_mode = $payvars['trade_mode'];
if (strtoupper($payvars['sign']) != strtoupper($sign)) {
    jieqi_printfail($jieqiLang['pay']['return_checkcode_error']);
} else {
    if ($partner != $jieqiPayset[JIEQI_PAY_TYPE]['payid']) {
        jieqi_printfail($jieqiLang['pay']['customer_id_error']);
    } else {
        if ($trade_state != '0') {
            jieqi_printfail($jieqiLang['pay']['pay_return_error']);
        } else {
            include $jieqiModules['pay']['path'] . '/include/funpay.php';
            $orderinfo = array('orderid' => $out_trade_no);
            $paylog = jieqi_pay_getpaylog($orderinfo['orderid']);
            if (is_object($paylog)) {
                $orderinfo = array('orderid' => $_GET['pay_id'], 'buyid' => $paylog->getVar('buyid'), 'buyname' => $paylog->getVar('buyname'), 'egold' => $paylog->getVar('egold'), 'money' => $paylog->getVar('money'), 'payflag' => $paylog->getVar('payflag'));
                $orderinfo['message'] = $jieqiLang['pay']['buy_return_success'];
                jieqi_pay_success($orderinfo);
            } else {
                $orderinfo['message'] = $jieqiLang['pay']['no_buy_record'];
                jieqi_pay_failure($orderinfo);
            }
        }
    }
}