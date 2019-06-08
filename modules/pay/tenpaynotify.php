<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'tenpay');
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
    exit('fail');
} else {
    if ($partner != $jieqiPayset[JIEQI_PAY_TYPE]['payid']) {
        exit('fail');
    } else {
        if ($trade_state != '0') {
            exit('fail');
        } else {
            $paycheck = isset($jieqiPayset[JIEQI_PAY_TYPE]['paycheck']) ? $jieqiPayset[JIEQI_PAY_TYPE]['paycheck'] : (isset($jieqiPayset[JIEQI_PAY_TYPE]['payverify']) ? $jieqiPayset[JIEQI_PAY_TYPE]['payverify'] : '');
            if (!empty($paycheck)) {
                $payvars = array();
                $payvars['partner'] = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
                $payvars['notify_id'] = $notify_id;
                $payvars['seller_id'] = '';
                $payvars['sign_type'] = $jieqiPayset[JIEQI_PAY_TYPE]['sign_type'];
                $payvars['service_version'] = $jieqiPayset[JIEQI_PAY_TYPE]['service_version'];
                $payvars['input_charset'] = $jieqiPayset[JIEQI_PAY_TYPE]['input_charset'];
                $payvars['sign_key_index'] = $jieqiPayset[JIEQI_PAY_TYPE]['sign_key_index'];
                $payvars['sign'] = jieqi_pay_getsign($payvars, array('key' => $jieqiPayset[JIEQI_PAY_TYPE]['paykey'], 'kname' => 'key', 'urlencode' => false, 'case' => 'upper'));
                $query = $paycheck . '?' . jieqi_pay_makequery($payvars, true, false);
                $retxml = jieqi_readfile($query);
                if ($logflag) {
                    $logfile = JIEQI_ROOT_PATH . '/files/pay/log/' . JIEQI_PAY_TYPE . '_notify.txt';
                    jieqi_checkdir(dirname($logfile), true);
                    $log = "\r\n" . $query . "\r\n" . $retxml . "\r\n";
                    jieqi_writefile($logfile, $log, 'ab');
                }
                if ($retxml == false) {
                    exit('fail');
                }
                $matches = array();
                if (!preg_match('/<retcode>([^<>]+)<\\/retcode>/is', $retxml, $matches)) {
                    exit('fail');
                }
                $retcode = trim($matches[1]);
                if ($retcode != 0) {
                    exit('fail');
                }
            }
            include $jieqiModules['pay']['path'] . '/include/funpay.php';
            $payinfo = array('orderid' => intval($out_trade_no), 'retserialno' => $transaction_id, 'retaccount' => $notify_id, 'retinfo' => $total_fee, 'return' => true);
            jieqi_pay_return($payinfo);
            exit('success');
        }
    }
}