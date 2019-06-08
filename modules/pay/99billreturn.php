<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', '99bill');
require_once '../../global.php';
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
$logflag = empty($jieqiPayset[JIEQI_PAY_TYPE]['payislog']) ? 0 : 1;
if ($logflag) {
    $logfile = JIEQI_ROOT_PATH . '/files/pay/log/' . JIEQI_PAY_TYPE . '_return.txt';
    jieqi_checkdir(dirname($logfile), true);
    $log = print_r($_GET, true) . '' . "\r\n" . '' . "\r\n" . '';
    jieqi_writefile($logfile, $log, 'ab');
}
include $jieqiModules['pay']['path'] . '/include/funpay.php';
include $jieqiModules['pay']['path'] . '/include/fun99bill.php';
if (isset($_GET['pay_ret']) && isset($_GET['pay_id'])) {
    $_GET['pay_id'] = intval($_GET['pay_id']);
    if ($_GET['pay_id'] <= 0) {
        jieqi_pay_failure(array('message' => LANG_ERROR_PARAMETER));
    }
    $orderinfo = array('orderid' => $_GET['pay_id']);
    $paylog = jieqi_pay_getpaylog($orderinfo['orderid']);
    if (is_object($paylog)) {
        $orderinfo = array('orderid' => $_GET['pay_id'], 'buyid' => $paylog->getVar('buyid'), 'buyname' => $paylog->getVar('buyname'), 'egold' => $paylog->getVar('egold'), 'money' => $paylog->getVar('money'), 'payflag' => $paylog->getVar('payflag'));
        switch ($_GET['pay_ret']) {
            case 1:
                $orderinfo['message'] = sprintf($jieqiLang['pay']['buy_egold_success'], $orderinfo['egold'] . JIEQI_EGOLD_NAME);
                jieqi_pay_success($orderinfo);
                break;
            case 2:
                $orderinfo['message'] = sprintf($jieqiLang['pay']['buy_already_success'], $orderinfo['egold'] . JIEQI_EGOLD_NAME);
                jieqi_pay_success($orderinfo);
                break;
            case -1:
                $orderinfo['message'] = $jieqiLang['pay']['no_buy_record'];
                jieqi_pay_failure($orderinfo);
                break;
            case -2:
                $orderinfo['message'] = $jieqiLang['pay']['save_paylog_failure'];
                jieqi_pay_failure($orderinfo);
                break;
            case -11:
                $orderinfo['message'] = LANG_ERROR_PARAMETER;
                jieqi_pay_failure($orderinfo);
                break;
            case -12:
                $orderinfo['message'] = $jieqiLang['pay']['return_checkcode_error'];
                jieqi_pay_failure($orderinfo);
                break;
            case -13:
                $orderinfo['message'] = $jieqiLang['pay']['pay_return_error'];
                jieqi_pay_failure($orderinfo);
                break;
            default:
                jieqi_printfail(LANG_ERROR_PARAMETER);
                break;
        }
    } else {
        $orderinfo['message'] = $jieqiLang['pay']['no_buy_record'];
        jieqi_pay_failure($orderinfo);
    }
} else {
    if (!empty($_GET['merchantAcctId']) && $_GET['merchantAcctId'] == $jieqiPayset[JIEQI_PAY_TYPE]['payid'] && !empty($_GET['orderId']) && !empty($_GET['payResult']) && !empty($_GET['signMsg'])) {
        if (isset($_GET['orderId'])) {
            $_GET['orderId'] = intval($_GET['orderId']);
        }
        $checksign = jieqi_pay_checksign($_GET, $jieqiPayset[JIEQI_PAY_TYPE]['publickey']);
        if ($checksign != 1) {
            jieqi_pay_failure(array('message' => $jieqiLang['pay']['return_checkcode_error']));
        }
        if ($_GET['payResult'] != '10') {
            jieqi_pay_failure(array('message' => $jieqiLang['pay']['pay_return_error']));
        }
        $payinfo = array('orderid' => intval($_GET['orderId']), 'retserialno' => $_GET['dealId'], 'retaccount' => $_GET['bankId'], 'subtype' => $_GET['payType'], 'retinfo' => $_GET['bankDealId'], 'return' => false);
        jieqi_pay_return($payinfo);
    } else {
        jieqi_pay_failure(array('message' => LANG_ERROR_PARAMETER));
    }
}