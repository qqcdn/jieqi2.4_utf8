<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'ipn');
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
if (empty($_GET) || !isset($_GET['funcode']) || !isset($_GET['appId']) || !isset($_GET['mhtOrderNo']) || !isset($_GET['tradeStatus']) || !isset($_GET['signature']) || $_GET['funcode'] != 'N002' || $_GET['tradeStatus'] != 'A001' || $_GET['appId'] != $jieqiPayset[JIEQI_PAY_TYPE]['payid']) {
    jieqi_printfail($jieqiLang['pay']['pay_return_error']);
}
include $jieqiModules['pay']['path'] . '/include/funpay.php';
include $jieqiModules['pay']['path'] . '/include/funipn.php';
$sign_new = jieqi_pay_makesign($_GET, $jieqiPayset[JIEQI_PAY_TYPE]['paykey']);
if (strtolower($_GET['signature']) == strtolower($sign_new)) {
    jieqi_pay_submitted();
} else {
    jieqi_printfail($jieqiLang['pay']['return_checkcode_error']);
}