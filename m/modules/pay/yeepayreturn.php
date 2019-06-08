<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'yeepay');
require_once '../../global.php';
require_once 'yeepaycommon.php';
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
$logflag = empty($jieqiPayset[JIEQI_PAY_TYPE]['payislog']) ? 0 : 1;
if ($logflag) {
    $logfile = JIEQI_ROOT_PATH . '/files/pay/log/' . JIEQI_PAY_TYPE . '_return.txt';
    jieqi_checkdir(dirname($logfile), true);
    $log = print_r($_REQUEST, true) . '' . "\r\n" . '' . "\r\n" . '';
    jieqi_writefile($logfile, $log, 'ab');
}
$merchantId = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
$keyValue = $jieqiPayset[JIEQI_PAY_TYPE]['paykey'];
$paytype = JIEQI_PAY_TYPE;
if (isset($_REQUEST['rb_BankId'])) {
    $_REQUEST['rb_BankId'] = trim($_REQUEST['rb_BankId']);
}
if (!empty($_REQUEST['rb_BankId']) && isset($jieqiPayset[JIEQI_PAY_TYPE]['paytype'][$_REQUEST['rb_BankId']])) {
    $paytype = $jieqiPayset[JIEQI_PAY_TYPE]['paytype'][$_REQUEST['rb_BankId']];
}
$return = getcallbackvalue($sCmd, $sErrorCode, $sTrxId, $amount, $cur, $productId, $orderId, $userId, $MP, $bType, $svrHmac);
$bRet = checkhmac($sCmd, $sErrorCode, $sTrxId, $orderId, $amount, $cur, $productId, $userId, $MP, $bType, $svrHmac);
include $jieqiModules['pay']['path'] . '/include/funpay.php';
if ($bRet) {
    if ($sErrorCode == '1') {
        if ($bType == '2') {
            echo 'success';
        }
        $payinfo = array('orderid' => intval($orderId), 'retserialno' => $sTrxId, 'retaccount' => $userId, 'retinfo' => '', 'return' => false);
        jieqi_pay_return($payinfo);
    } else {
        jieqi_printfail($jieqiLang['pay']['pay_return_error']);
    }
} else {
    jieqi_printfail($jieqiLang['pay']['return_checkcode_error']);
}