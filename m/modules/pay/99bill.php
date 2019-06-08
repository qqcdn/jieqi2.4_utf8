<?php

@define('JIEQI_MODULE_NAME', 'pay');
@define('JIEQI_PAY_TYPE', '99bill');
require_once '../../global.php';
jieqi_loadlang('pay', 'pay');
if (!jieqi_checklogin(true)) {
    jieqi_printfail($jieqiLang['pay']['need_login']);
}
jieqi_getconfigs('pay', JIEQI_PAY_TYPE, 'jieqiPayset');
include $jieqiModules['pay']['path'] . '/include/funpay.php';
$paylog = jieqi_pay_start();
include $jieqiModules['pay']['path'] . '/include/fun99bill.php';
$payvars = array();
$payvars['inputCharset'] = $jieqiPayset[JIEQI_PAY_TYPE]['inputCharset'];
$payvars['pageUrl'] = $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'];
$payvars['bgUrl'] = $jieqiPayset[JIEQI_PAY_TYPE]['paynotify'];
$payvars['version'] = $jieqiPayset[JIEQI_PAY_TYPE]['version'];
if (isset($jieqiPayset[JIEQI_PAY_TYPE]['mobileGateway']) && 0 < strlen($jieqiPayset[JIEQI_PAY_TYPE]['mobileGateway'])) {
    $payvars['mobileGateway'] = $jieqiPayset[JIEQI_PAY_TYPE]['mobileGateway'];
}
$payvars['language'] = $jieqiPayset[JIEQI_PAY_TYPE]['language'];
$payvars['signType'] = $jieqiPayset[JIEQI_PAY_TYPE]['signType'];
$payvars['merchantAcctId'] = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
$payvars['orderId'] = $paylog->getVar('payid');
$payvars['orderAmount'] = $_REQUEST['money'];
$payvars['orderTime'] = date('YmdHis', JIEQI_NOW_TIME);
$payvars['productName'] = empty($jieqiPayset[JIEQI_PAY_TYPE]['productName']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['productName'];
if (0 < strlen($payvars['productDesc'])) {
    $payvars['productDesc'] = sprintf($jieqiPayset[JIEQI_PAY_TYPE]['productDesc'], $_REQUEST['egold'], $_REQUEST['amount']);
}
if (0 < strlen($payvars['ext1'])) {
    $payvars['ext1'] = $jieqiPayset[JIEQI_PAY_TYPE]['ext1'];
}
if (0 < strlen($payvars['ext2'])) {
    $payvars['ext2'] = $jieqiPayset[JIEQI_PAY_TYPE]['ext2'];
}
$payvars['payType'] = $jieqiPayset[JIEQI_PAY_TYPE]['payType'];
$payvars['redoFlag'] = $jieqiPayset[JIEQI_PAY_TYPE]['redoFlag'];
$payvars['signMsg'] = jieqi_pay_makesign($payvars, $jieqiPayset[JIEQI_PAY_TYPE]['privatekey']);
$query = $jieqiPayset[JIEQI_PAY_TYPE]['payurl'] . '?' . jieqi_pay_makequery($payvars, true);
$jieqiTset['jieqi_page_template'] = $jieqiModules['pay']['path'] . '/templates/' . JIEQI_PAY_TYPE . '.html';
if (isset($jieqiPayset[JIEQI_PAY_TYPE]['payrequest']) && strtoupper($jieqiPayset[JIEQI_PAY_TYPE]['payrequest']) == 'POST') {
    include_once JIEQI_ROOT_PATH . '/header.php';
    $jieqiTpl->assign('buyname', $_SESSION['jieqiUserName']);
    $jieqiTpl->assign('egold', $_REQUEST['egold']);
    $jieqiTpl->assign('money', $_REQUEST['amount']);
    $jieqiTpl->assign('url_pay', $jieqiPayset[JIEQI_PAY_TYPE]['payurl']);
    $jieqiTpl->assign('url_query', $query);
    foreach ($payvars as $k => $v) {
        $payvars[$k] = jieqi_htmlchars($v, ENT_QUOTES);
    }
    $jieqiTpl->assign('payvars', $payvars);
    $jieqiTpl->assign('jumpurl', jieqi_htmlstr($_REQUEST['jumpurl']));
    $jieqiTpl->setCaching(0);
    include_once JIEQI_ROOT_PATH . '/footer.php';
} else {
    header('Location: ' . jieqi_headstr($query));
    exit;
}