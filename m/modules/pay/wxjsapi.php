<?php

if (isset($_POST['egold']) && !empty($_POST['egold'])) {
    $redirect_url = 'wxjsapi.php?egold=' . urlencode($_POST['egold']);
    if (!empty($_REQUEST['jumpurl'])) {
        $redirect_url .= '&jumpurl=' . urlencode($_REQUEST['jumpurl']);
    }
    header('Location: ' . $redirect_url);
    exit;
} else {
    if (isset($_POST['money']) && !empty($_POST['money'])) {
        $redirect_url = 'wxjsapi.php?money=' . urlencode($_POST['money']);
        if (!empty($_REQUEST['jumpurl'])) {
            $redirect_url .= '&jumpurl=' . urlencode($_REQUEST['jumpurl']);
        }
        header('Location: ' . $redirect_url);
        exit;
    }
}
@define('JIEQI_MODULE_NAME', 'pay');
@define('JIEQI_PAY_TYPE', 'wxjsapi');
require_once '../../global.php';
jieqi_loadlang('pay', 'pay');
if (!jieqi_checklogin(true)) {
    jieqi_printfail($jieqiLang['pay']['need_login']);
}
jieqi_getconfigs('pay', JIEQI_PAY_TYPE, 'jieqiPayset');
$logflag = empty($jieqiPayset[JIEQI_PAY_TYPE]['payislog']) ? 0 : 1;
include_once JIEQI_ROOT_PATH . '/lib/WxPay/WxPay.Api.php';
include_once JIEQI_ROOT_PATH . '/lib/WxPay/WxPay.JsApiPay.php';
include_once $jieqiModules['pay']['path'] . '/wxpaylog.php';
if ($logflag) {
    $logHandler = new CLogFileHandler(JIEQI_ROOT_PATH . '/files/pay/log/' . JIEQI_PAY_TYPE . '_request.txt');
    $log = Log::Init($logHandler, 15);
}
$apiName = 'wxmp';
$tools = new JsApiPay();
if (!empty($_SESSION['jieqiUserApi'][$apiName]['openid'])) {
    $openId = $_SESSION['jieqiUserApi'][$apiName]['openid'];
} else {
    $openId = $tools->GetOpenid();
    if (is_array($tools->data)) {
        $_SESSION['jieqiUserApi'][$apiName] = $tools->data;
    }
    $_SESSION['jieqiUserApi'][$apiName]['openid'] = $openId;
}
include $jieqiModules['pay']['path'] . '/include/funpay.php';
$paylog = jieqi_pay_start();
$out_trade_no = $paylog->getVar('payid');
if (strlen($out_trade_no) < 2) {
    $out_trade_no = '0' . $out_trade_no;
}
$input = new WxPayUnifiedOrder();
$tmpvar = empty($jieqiPayset[JIEQI_PAY_TYPE]['body']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['body'];
$tmpvar = jieqi_charsetconvert($tmpvar, JIEQI_SYSTEM_CHARSET, 'utf-8');
$input->SetBody($tmpvar);
$input->SetAttach('');
$input->SetOut_trade_no($out_trade_no);
$input->SetTotal_fee($_REQUEST['money']);
$input->SetTime_start(date('YmdHis'));
$input->SetTime_expire(date('YmdHis', time() + 600));
$input->SetGoods_tag('');
if (!isset($jieqiPayset[JIEQI_PAY_TYPE]['paynotify'])) {
    $jieqiPayset[JIEQI_PAY_TYPE]['paynotify'] = $jieqiPayset[JIEQI_PAY_TYPE]['notify_url'];
}
$input->SetNotify_url($jieqiPayset[JIEQI_PAY_TYPE]['paynotify']);
$input->SetTrade_type('JSAPI');
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
if ($order['return_code'] == 'FAIL') {
    $order = jieqi_charsetconvert($order, 'utf-8', JIEQI_SYSTEM_CHARSET);
    jieqi_printfail('tradeno=' . $out_trade_no . ', fee=' . $_REQUEST['money'] . ', return=' . $order['return_msg']);
} else {
    if ($order['result_code'] == 'FAIL') {
        $order = jieqi_charsetconvert($order, 'utf-8', JIEQI_SYSTEM_CHARSET);
        jieqi_printfail('tradeno=' . $out_trade_no . ', fee=' . $_REQUEST['money'] . ', errcode=' . $order['err_code'] . ', errdes=' . $order['err_code_des']);
    } else {
        $jsApiParameters = $tools->GetJsApiParameters($order);
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('buyname', $_SESSION['jieqiUserName']);
        $jieqiTpl->assign('egold', $_REQUEST['egold']);
        $jieqiTpl->assign('money', $_REQUEST['amount']);
        $subject = empty($jieqiPayset[JIEQI_PAY_TYPE]['subject']) ? JIEQI_EGOLD_NAME : $jieqiPayset[JIEQI_PAY_TYPE]['subject'];
        $jieqiTpl->assign('subject', $subject);
        $jieqiTpl->assign('jsApiParameters', $jsApiParameters);
        $jieqiTpl->assign('jumpurl', jieqi_htmlstr($_REQUEST['jumpurl']));
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/wxjsapi.html';
        $jieqiTpl->setCaching(0);
        include_once JIEQI_ROOT_PATH . '/footer.php';
    }
}