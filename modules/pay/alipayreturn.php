<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'alipay');
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
if (!empty($_GET['notify_id']) && !empty($_GET['buyer_email']) && !empty($_GET['out_trade_no'])) {
    $getvars = $_GET;
    $showmode = 1;
} else {
    if (!empty($_POST['notify_id']) && !empty($_POST['buyer_email']) && !empty($_POST['out_trade_no'])) {
        $getvars = $_POST;
        $showmode = 0;
        echo 'success';
    } else {
        echo 'fail';
        exit;
    }
}
if (strtoupper($getvars['trade_status']) != 'TRADE_FINISHED' && strtoupper($getvars['trade_status']) != 'TRADE_SUCCESS') {
    if ($showmode) {
        jieqi_printfail($jieqiLang['pay']['pay_return_error'] . '<br /><br />RETCODE:' . $getvars['trade_status']);
    } else {
        exit;
    }
}
include $jieqiModules['pay']['path'] . '/include/funpay.php';
include $jieqiModules['pay']['path'] . '/include/funalipay.php';
$sign_new = jieqi_pay_makesign($getvars, $jieqiPayset[JIEQI_PAY_TYPE]['paykey']);
if (strtolower($getvars['sign']) == strtolower($sign_new)) {
    $return = 0 < $showmode ? false : true;
    $payinfo = array('orderid' => intval($getvars['out_trade_no']), 'retserialno' => $getvars['trade_no'], 'retaccount' => $getvars['buyer_email'], 'retinfo' => $getvars['buyer_id'], 'return' => $return);
    jieqi_pay_return($payinfo);
} else {
    if ($showmode) {
        $orderinfo['message'] = $jieqiLang['pay']['return_checkcode_error'];
        jieqi_pay_failure($orderinfo);
    }
}