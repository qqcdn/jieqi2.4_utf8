<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'tenpay');
require_once '../../global.php';
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
if (!jieqi_checklogin(true)) {
    jieqi_printfail($jieqiLang['pay']['need_login']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
include $jieqiModules['pay']['path'] . '/include/funpay.php';
$paylog = jieqi_pay_start();
$payvars = array();
$payvars['partner'] = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
$payvars['out_trade_no'] = $paylog->getVar('payid');
$payvars['total_fee'] = $_REQUEST['money'];
$payvars['return_url'] = $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'];
$payvars['notify_url'] = $jieqiPayset[JIEQI_PAY_TYPE]['paynotify'];
$payvars['body'] = empty($jieqiPayset[JIEQI_PAY_TYPE]['body']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['body'];
$payvars['bank_type'] = $jieqiPayset[JIEQI_PAY_TYPE]['bank_type'];
$payvars['spbill_create_ip'] = jieqi_userip();
$payvars['fee_type'] = $jieqiPayset[JIEQI_PAY_TYPE]['fee_type'];
$payvars['sign_type'] = $jieqiPayset[JIEQI_PAY_TYPE]['sign_type'];
$payvars['service_version'] = $jieqiPayset[JIEQI_PAY_TYPE]['service_version'];
$payvars['input_charset'] = $jieqiPayset[JIEQI_PAY_TYPE]['input_charset'];
$payvars['sign_key_index'] = $jieqiPayset[JIEQI_PAY_TYPE]['sign_key_index'];
$payvars['attach'] = $jieqiPayset[JIEQI_PAY_TYPE]['attach'];
$payvars['product_fee'] = '';
$payvars['transport_fee'] = '0';
$payvars['time_start'] = date('YmdHis');
$payvars['time_expire'] = '';
$payvars['buyer_id'] = '';
$payvars['goods_tag'] = '';
$payvars['sign'] = jieqi_pay_getsign($payvars, array('key' => $jieqiPayset[JIEQI_PAY_TYPE]['paykey'], 'kname' => 'key', 'urlencode' => false, 'case' => 'upper'));
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