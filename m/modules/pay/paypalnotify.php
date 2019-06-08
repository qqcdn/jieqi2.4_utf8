<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'paypal');
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
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
    $req .= '&' . urlencode($key) . '=' . urlencode($value);
}
if (function_exists('curl_init')) {
    if ($logflag) {
        $log = 'use curl' . "\r\n" . '';
        jieqi_writefile($logfile, $log, 'ab');
    }
    $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
    $ch = curl_init($paypal_url);
    if ($ch == false) {
        exit('curl init failure');
    }
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    $res = curl_exec($ch);
    if (curl_errno($ch) != 0) {
        echo 'Can\'t connect to PayPal to validate IPN message: ' . curl_error($ch);
        if ($logflag) {
            $log = 'Can\'t connect to PayPal to validate IPN message: ' . curl_error($ch) . "\r\n";
            jieqi_writefile($logfile, $log, 'ab');
        }
        curl_close($ch);
        exit;
    } else {
        curl_close($ch);
    }
} else {
    if ($logflag) {
        $log = 'use fsocketopen ' . "\r\n" . '';
        jieqi_writefile($logfile, $log, 'ab');
    }
    $header = 'POST /cgi-bin/webscr HTTP/1.1' . "\r\n" . '';
    $header .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n" . '';
    $header .= 'Content-Length: ' . strlen($req) . '' . "\r\n" . '' . "\r\n" . '';
    $fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);
    if (!$fp) {
        echo 'open ssl failure!';
        if ($logflag) {
            $log = 'open ssl failure! ' . "\r\n" . '';
            jieqi_writefile($logfile, $log, 'ab');
        }
        exit;
    } else {
        fputs($fp, $header . $req);
        $res = '';
        while (!feof($fp)) {
            $res .= fgets($fp, 1024);
        }
        fclose($fp);
    }
}
if ($logflag) {
    $log = $res . "\r\n";
    jieqi_writefile($logfile, $log, 'ab');
}
$tokens = explode('' . "\r\n" . '' . "\r\n" . '', trim($res));
$res = trim(end($tokens));
include $jieqiModules['pay']['path'] . '/include/funpay.php';
if (strcmp($res, 'VERIFIED') == 0) {
    if ($logflag) {
        $log = 'VERIFIED' . "\r\n" . '';
        jieqi_writefile($logfile, $log, 'ab');
    }
    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $mc_gross = $_POST['mc_gross'];
    $mc_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
    $custom = $_POST['custom'];
    if (strcmp($payment_status, 'Completed') == 0) {
        $payinfo = array('orderid' => 0, 'retserialno' => '', 'retaccount' => $txn_id, 'retinfo' => '', 'return' => true);
        $payinfo['orderid'] = !empty($item_number) ? intval($item_number) : intval($custom);
        $payret = jieqi_pay_return($payinfo);
        if ($payret < 0) {
            echo 'error no: ' . $payret;
        } else {
            echo '200 OK';
        }
    } else {
        echo 'payment_status = ' . $payment_status;
    }
} else {
    if (strcmp($res, 'INVALID') == 0) {
        echo 'The response from IPN was: <b>' . $res . '</b>';
    }
}