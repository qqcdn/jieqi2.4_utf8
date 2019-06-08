<?php

function jieqi_pay_querysign($params, $type = 'request', $ue = false)
{
    if ($type == 'response') {
        $fields = array('merchantAcctId', 'version', 'language', 'signType', 'payType', 'bankId', 'orderId', 'orderTime', 'orderAmount', 'bindCard', 'bindMobile', 'dealId', 'bankDealId', 'dealTime', 'payAmount', 'fee', 'ext1', 'ext2', 'payResult', 'errCode');
    } else {
        $fields = array('inputCharset', 'pageUrl', 'bgUrl', 'version', 'language', 'signType', 'merchantAcctId', 'payerName', 'payerContactType', 'payerContact', 'payerIdType', 'payerId', 'payerIP', 'orderId', 'orderAmount', 'orderTime', 'orderTimestamp', 'productName', 'productNum', 'productId', 'productDesc', 'ext1', 'ext2', 'payType', 'bankId', 'cardIssuer', 'cardNum', 'remitType', 'remitCode', 'redoFlag', 'pid', 'submitType', 'orderTimeOut', 'mobileGateway', 'extDataType', 'extDataContent');
    }
    $sign_string = '';
    foreach ($fields as $k) {
        if (isset($params[$k]) && 0 < strlen($params[$k])) {
            if (0 < strlen($sign_string)) {
                $sign_string .= '&';
            }
            $sign_string .= $ue == true ? urlencode($k) . '=' . urlencode($params[$k]) : $k . '=' . $params[$k];
        }
    }
    return $sign_string;
}
function jieqi_pay_makesign($params, $key)
{
    $sign_string = jieqi_pay_querysign($params, 'request');
    if (is_file($key)) {
        $key = jieqi_readfile($key);
    }
    $pkeyid = openssl_get_privatekey($key);
    openssl_sign($sign_string, $signMsg, $pkeyid, OPENSSL_ALGO_SHA1);
    openssl_free_key($pkeyid);
    $signMsg = base64_encode($signMsg);
    return $signMsg;
}
function jieqi_pay_checksign($params, $key, $type = 'private')
{
    $sign_string = jieqi_pay_querysign($params, 'response');
    $mac = base64_decode($params['signMsg']);
    if (is_file($key)) {
        $key = jieqi_readfile($key);
    }
    $pubkeyid = openssl_get_publickey($key);
    $ok = openssl_verify($sign_string, $mac, $pubkeyid);
    return $ok;
}
function jieqi_pay_notifyout($ret, $url)
{
    exit('<result>' . $ret . '</result><redirecturl>' . $url . '</redirecturl>');
}