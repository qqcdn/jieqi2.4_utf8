<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'ipn');
$request = file_get_contents('php://input');
parse_str($request, $_POST);
require_once '../../global.php';
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
$logflag = empty($jieqiPayset[JIEQI_PAY_TYPE]['payislog']) ? 0 : 1;
if ($logflag) {
    $logfile = JIEQI_ROOT_PATH . '/files/pay/log/' . JIEQI_PAY_TYPE . '_notify.txt';
    jieqi_checkdir(dirname($logfile), true);
    $log = print_r($_POST, true) . '' . "\r\n" . '' . "\r\n" . '';
    jieqi_writefile($logfile, $log, 'ab');
}
if (empty($_POST) || !isset($_POST['funcode']) || !isset($_POST['appId']) || !isset($_POST['mhtOrderNo']) || !isset($_POST['tradeStatus']) || !isset($_POST['signature']) || $_POST['funcode'] != 'N001' || $_POST['tradeStatus'] != 'A001' || $_POST['appId'] != $jieqiPayset[JIEQI_PAY_TYPE]['payid']) {
    exit('success=N');
}
include $jieqiModules['pay']['path'] . '/include/funpay.php';
include $jieqiModules['pay']['path'] . '/include/funipn.php';
$sign_new = jieqi_pay_makesign($_POST, $jieqiPayset[JIEQI_PAY_TYPE]['paykey']);
if (strtolower($_POST['signature']) == strtolower($sign_new)) {
    $payinfo = array('orderid' => intval($_POST['mhtOrderNo']), 'retserialno' => '', 'retaccount' => '', 'retinfo' => '', 'return' => true);
    if (isset($_POST['nowPayOrderNo'])) {
        $payinfo['retserialno'] = $_POST['nowPayOrderNo'];
    }
    if (isset($_POST['payConsumerId'])) {
        $payinfo['retaccount'] = $_POST['payConsumerId'];
    }
    if (isset($_POST['payConsumerName'])) {
        $payinfo['retinfo'] = $_POST['payConsumerName'];
    }
    if (isset($_POST['payChannelType'])) {
        $payinfo['subtype'] = $_POST['payChannelType'];
    }
    if (isset($_POST['deviceType'])) {
        $payinfo['fromtype'] = $_POST['deviceType'];
    }
    jieqi_pay_return($payinfo);
    echo 'success=Y';
    if ($logflag) {
        $logfile = JIEQI_ROOT_PATH . '/files/pay/log/' . JIEQI_PAY_TYPE . '_notify.txt';
        jieqi_checkdir(dirname($logfile), true);
        $log = "\r\n" . date('Y-m-d H:i:s') . ' success=Y' . "\r\n";
        jieqi_writefile($logfile, $log, 'ab');
    }
} else {
    exit('success=N');
}