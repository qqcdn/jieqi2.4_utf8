<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'yeepay');
require_once '../../global.php';
require_once 'yeepaycommon.php';
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
if (!jieqi_checklogin(true)) {
    jieqi_printfail($jieqiLang['pay']['need_login']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
include $jieqiModules['pay']['path'] . '/include/funpay.php';
$paylog = jieqi_pay_start();
$amount = $_REQUEST['amount'];
$merchantId = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
$orderId = $paylog->getVar('payid');
$cur = $jieqiPayset[JIEQI_PAY_TYPE]['cur'];
$productId = empty($jieqiPayset[JIEQI_PAY_TYPE]['productId']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['productId'];
$productCat = $jieqiPayset[JIEQI_PAY_TYPE]['productCat'];
$productDesc = $jieqiPayset[JIEQI_PAY_TYPE]['productDesc'];
$sMctProperties = $jieqiPayset[JIEQI_PAY_TYPE]['sMctProperties'];
if (empty($_POST['pd_FrpId']) || substr(trim($_POST['pd_FrpId']), -3) != 'B2C') {
    $frpId = empty($jieqiPayset[JIEQI_PAY_TYPE]['frpId']) ? 'ICBC-NET-B2C' : $jieqiPayset[JIEQI_PAY_TYPE]['frpId'];
} else {
    $frpId = trim($_POST['pd_FrpId']);
}
$needResponse = $jieqiPayset[JIEQI_PAY_TYPE]['needResponse'];
$nodeAuthorizationURL = $jieqiPayset[JIEQI_PAY_TYPE]['payurl'];
$merchantCallbackURL = $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'];
$messageType = $jieqiPayset[JIEQI_PAY_TYPE]['messageType'];
$addressFlag = $jieqiPayset[JIEQI_PAY_TYPE]['addressFlag'];
$merchant_url = $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'];
$commodity_info = urlencode(JIEQI_EGOLD_NAME);
$pname = urlencode($_SESSION['jieqiUserName']);
$keyValue = $jieqiPayset[JIEQI_PAY_TYPE]['paykey'];
$mac = getreqhmacstring($orderId, $amount, $cur, $productId, $productCat, $productDesc, $sMctProperties, $frpId, $needResponse);
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTpl->assign('url_pay', $jieqiPayset[JIEQI_PAY_TYPE]['payurl']);
$jieqiTpl->assign('buyname', $_SESSION['jieqiUserName']);
$jieqiTpl->assign('egold', $_REQUEST['egold']);
$jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
$jieqiTpl->assign('money', $_REQUEST['amount']);
$jieqiTpl->assign('merchant_id', $merchantId);
$jieqiTpl->assign('orderid', $orderId);
$jieqiTpl->assign('amount', $amount);
$jieqiTpl->assign('cur', $cur);
$jieqiTpl->assign('merchant_url', $merchant_url);
$jieqiTpl->assign('commodity_info', $commodity_info);
$jieqiTpl->assign('productId', $productId);
$jieqiTpl->assign('productCat', $productCat);
$jieqiTpl->assign('productDesc', $productDesc);
$jieqiTpl->assign('sMctProperties', $sMctProperties);
$jieqiTpl->assign('frpId', $frpId);
$jieqiTpl->assign('needResponse', $needResponse);
$jieqiTpl->assign('nodeAuthorizationURL', $nodeAuthorizationURL);
$jieqiTpl->assign('merchantCallbackURL', $merchantCallbackURL);
$jieqiTpl->assign('messageType', $messageType);
$jieqiTpl->assign('addressFlag', $addressFlag);
$jieqiTpl->assign('mac', $mac);
if (isset($jieqiPayset[JIEQI_PAY_TYPE]['payfrom'])) {
    $jieqiTpl->assign('fromselect', 1);
    $fromrows = array();
    foreach ($jieqiPayset[JIEQI_PAY_TYPE]['payfrom'] as $k => $v) {
        $fromrows[] = array('id' => $k, 'name' => $v);
    }
    $jieqiTpl->assign_by_ref('fromrows', $fromrows);
}
if (is_array($jieqiPayset[JIEQI_PAY_TYPE]['addvars'])) {
    foreach ($jieqiPayset[JIEQI_PAY_TYPE]['addvars'] as $k => $v) {
        $jieqiTpl->assign($k, $v);
    }
}
$jieqiTpl->assign('jumpurl', jieqi_htmlstr($_REQUEST['jumpurl']));
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/yeepay.html';
include_once JIEQI_ROOT_PATH . '/footer.php';