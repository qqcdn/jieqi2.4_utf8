<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', '99bill');
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
include $jieqiModules['pay']['path'] . '/include/funpay.php';
include $jieqiModules['pay']['path'] . '/include/fun99bill.php';
if (isset($_REQUEST['orderId'])) {
    $_REQUEST['orderId'] = intval($_REQUEST['orderId']);
}
if (empty($_REQUEST['merchantAcctId']) || $_REQUEST['merchantAcctId'] != $jieqiPayset[JIEQI_PAY_TYPE]['payid'] || empty($_REQUEST['orderId']) || empty($_REQUEST['payResult']) || empty($_REQUEST['signMsg'])) {
    jieqi_pay_notifyout(0, $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] . '?pay_ret=-11&pay_id=0');
}
$checksign = jieqi_pay_checksign($_REQUEST, $jieqiPayset[JIEQI_PAY_TYPE]['publickey']);
if ($checksign != 1) {
    jieqi_pay_notifyout(0, $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] . '?pay_ret=-12&pay_id=' . $_REQUEST['orderId']);
}
if ($_REQUEST['payResult'] != '10') {
    jieqi_pay_notifyout(0, $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] . '?pay_ret=-13&pay_id=' . $_REQUEST['orderId']);
}
$payinfo = array('orderid' => intval($_REQUEST['orderId']), 'retserialno' => $_REQUEST['dealId'], 'retaccount' => $_REQUEST['bankId'], 'subtype' => $_REQUEST['payType'], 'retinfo' => $_REQUEST['bankDealId'], 'return' => true);
$payret = jieqi_pay_return($payinfo);
jieqi_pay_notifyout(1, $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'] . '?pay_ret=' . $payret . '&pay_id=' . $_REQUEST['orderId']);