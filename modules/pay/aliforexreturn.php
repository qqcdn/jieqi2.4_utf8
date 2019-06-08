<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'aliforex');
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
$mymerchant_id = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
$key = $jieqiPayset[JIEQI_PAY_TYPE]['paykey'];
if (!empty($_GET['trade_no']) && !empty($_GET['trade_status']) && !empty($_GET['out_trade_no'])) {
    $getvars = $_GET;
    $showmode = 1;
} else {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (strtoupper($getvars['trade_status']) != 'TRADE_FINISHED' && strtoupper($getvars['trade_status']) != 'TRADE_SUCCESS') {
    jieqi_printfail($jieqiLang['pay']['pay_return_error'] . '<br /><br />RETCODE:' . $getvars['trade_status']);
}
include $jieqiModules['pay']['path'] . '/include/funpay.php';
include $jieqiModules['pay']['path'] . '/include/funalipay.php';
$sign_new = jieqi_pay_makesign($getvars, $jieqiPayset[JIEQI_PAY_TYPE]['paykey']);
if (strtolower($getvars['sign']) == strtolower($sign_new)) {
    $return = 0 < $showmode ? false : true;
    $payinfo = array('orderid' => intval($getvars['out_trade_no']), 'retserialno' => $getvars['trade_no'], 'retaccount' => '', 'retinfo' => $getvars['total_fee'], 'return' => $return);
    jieqi_pay_return($payinfo);
} else {
    if ($showmode) {
        $orderinfo['message'] = $jieqiLang['pay']['return_checkcode_error'];
        jieqi_pay_failure($orderinfo);
    }
}