<?php

function hmac($key, $data)
{
    if (function_exists('iconv')) {
        $key = iconv('GB2312', 'UTF-8', $key);
        $data = iconv('GB2312', 'UTF-8', $data);
    } else {
        include_once JIEQI_ROOT_PATH . '/include/changecode.php';
        $key = jieqi_gb2utf8($key);
        $data = jieqi_gb2utf8($data);
    }
    $b = 64;
    if ($b < strlen($key)) {
        $key = pack('H*', md5($key));
    }
    $key = str_pad($key, $b, chr(0));
    $ipad = str_pad('', $b, chr(54));
    $opad = str_pad('', $b, chr(92));
    $k_ipad = $key ^ $ipad;
    $k_opad = $key ^ $opad;
    return md5($k_opad . pack('H*', md5($k_ipad . $data)));
}
function getReqHmacString($orderId, $amount, $cur, $productId, $productCat, $productDesc, $sMctProperties, $frpId, $needResponse)
{
    global $nodeAuthorizationURL;
    global $messageType;
    global $addressFlag;
    global $merchantId;
    global $merchantCallbackURL;
    global $keyValue;
    $sbOld = '';
    $sbOld = $sbOld . $messageType;
    $sbOld = $sbOld . $merchantId;
    $sbOld = $sbOld . $orderId;
    $sbOld = $sbOld . $amount;
    $sbOld = $sbOld . $cur;
    $sbOld = $sbOld . $productId;
    $sbOld = $sbOld . $productCat;
    $sbOld = $sbOld . $productDesc;
    $sbOld = $sbOld . $merchantCallbackURL;
    $sbOld = $sbOld . $addressFlag;
    $sbOld = $sbOld . $sMctProperties;
    $sbOld = $sbOld . $frpId;
    $sbOld = $sbOld . $needResponse;
    return hmac($keyValue, $sbOld);
}
function getCallbackHmacString($sCmd, $sErrorCode, $sTrxId, $orderId, $amount, $cur, $productId, $userId, $MP, $bType)
{
    global $keyValue;
    global $merchantId;
    $sbOld = '';
    $sbOld = $sbOld . $merchantId;
    $sbOld = $sbOld . $sCmd;
    $sbOld = $sbOld . $sErrorCode;
    $sbOld = $sbOld . $sTrxId;
    $sbOld = $sbOld . $amount;
    $sbOld = $sbOld . $cur;
    $sbOld = $sbOld . $productId;
    $sbOld = $sbOld . $orderId;
    $sbOld = $sbOld . $userId;
    $sbOld = $sbOld . $MP;
    $sbOld = $sbOld . $bType;
    return hmac($keyValue, $sbOld);
}
function getCallBackValue(&$sCmd, &$sErrorCode, &$sTrxId, &$amount, &$cur, &$productId, &$orderId, &$userId, &$MP, &$bType, &$svrHmac)
{
    $sCmd = $_REQUEST['r0_Cmd'];
    $sErrorCode = $_REQUEST['r1_Code'];
    $sTrxId = $_REQUEST['r2_TrxId'];
    $amount = $_REQUEST['r3_Amt'];
    $cur = $_REQUEST['r4_Cur'];
    $productId = $_REQUEST['r5_Pid'];
    $orderId = $_REQUEST['r6_Order'];
    $userId = $_REQUEST['r7_Uid'];
    $MP = $_REQUEST['r8_MP'];
    $bType = $_REQUEST['r9_BType'];
    $svrHmac = $_REQUEST['hmac'];
    return NULL;
}
function CheckHmac($sCmd, $sErrorCode, $sTrxId, $orderId, $amount, $cur, $productId, $userId, $MP, $bType, $svrHmac)
{
    if ($svrHmac == getcallbackhmacstring($sCmd, $sErrorCode, $sTrxId, $orderId, $amount, $cur, $productId, $userId, $MP, $bType)) {
        return true;
    } else {
        return false;
    }
}
echo ' ';