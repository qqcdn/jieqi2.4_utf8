<?php

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'paypal');
require_once '../../global.php';
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
if (!jieqi_checklogin(true)) {
    jieqi_printfail($jieqiLang['pay']['need_login']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');
if (empty($jieqiPayset[JIEQI_PAY_TYPE]['payid'])) {
    jieqi_printfail($jieqiLang['pay']['paypal_not_open']);
}
include $jieqiModules['pay']['path'] . '/include/funpay.php';
$paylog = jieqi_pay_start();
$payvars = array();
$payvars['cmd'] = $jieqiPayset[JIEQI_PAY_TYPE]['cmd'];
$payvars['business'] = $jieqiPayset[JIEQI_PAY_TYPE]['payid'];
$payvars['item_name'] = $jieqiPayset[JIEQI_PAY_TYPE]['item_name'];
$payvars['charset'] = $jieqiPayset[JIEQI_PAY_TYPE]['charset'];
$payvars['currency_code'] = $jieqiPayset[JIEQI_PAY_TYPE]['currency_code'];
$payvars['amount'] = $_REQUEST['amount'];
$payvars['item_number'] = $paylog->getVar('payid');
$payvars['custom'] = $payvars['item_number'];
$payvars['notify_url'] = $jieqiPayset[JIEQI_PAY_TYPE]['paynotify'];
$payvars['rm'] = $jieqiPayset[JIEQI_PAY_TYPE]['rm'];
$payvars['return'] = $jieqiPayset[JIEQI_PAY_TYPE]['payreturn'];
$payvars['cancel_return'] = $jieqiPayset[JIEQI_PAY_TYPE]['cancel_return'];
$payvars['no_shipping'] = $jieqiPayset[JIEQI_PAY_TYPE]['no_shipping'];
$payvars['no_note'] = $jieqiPayset[JIEQI_PAY_TYPE]['no_note'];
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