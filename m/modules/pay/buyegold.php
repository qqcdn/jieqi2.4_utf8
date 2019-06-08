<?php

define('JIEQI_MODULE_NAME', 'pay');
require_once '../../global.php';
jieqi_checklogin();
jieqi_loadlang('pay', 'pay');
if (!empty($_REQUEST['t']) && preg_match('/^\\w+$/', $_REQUEST['t']) && strlen($_REQUEST['t']) < 64) {
    $paytpl = $_REQUEST['t'];
} else {
    $paytpl = 'buyegold';
    $_REQUEST['t'] = 'buyegold';
}
$tmpfile = $jieqiModules['pay']['path'] . '/templates/' . $paytpl . '.html';
if (is_file($tmpfile)) {
    $jieqiTset['jieqi_contents_template'] = $tmpfile;
} else {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && is_file($jieqiModules['pay']['path'] . '/templates/wxjsapipay.html')) {
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/wxjsapipay.html';
        $_REQUEST['t'] = 'wxjsapipay';
        $paytpl = 'wxjsapipay';
    } else {
        if (defined('JIEQI_MOBILE_LOCATION') && 7 < strlen(JIEQI_MOBILE_LOCATION) && substr(JIEQI_LOCAL_URL, 0, strlen(JIEQI_MOBILE_LOCATION)) == JIEQI_MOBILE_LOCATION && is_file($jieqiModules['pay']['path'] . '/templates/aliwappay.html')) {
            $jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/aliwappay.html';
            $_REQUEST['t'] = 'aliwappay';
            $paytpl = 'aliwappay';
        } else {
            if (is_file($jieqiModules['pay']['path'] . '/templates/alipaypay.html')) {
                $jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/alipaypay.html';
                $_REQUEST['t'] = 'alipaypay';
                $paytpl = 'alipaypay';
            } else {
                $jieqiTset['jieqi_contents_template'] = '';
            }
        }
    }
}
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTpl->setCaching(0);
$jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
if (!empty($_REQUEST['p']) && preg_match('/^\\w+$/', $_REQUEST['p']) && strlen($_REQUEST['p']) < 64) {
    $paytype = $_REQUEST['p'];
} else {
    if (!empty($jieqiTset['jieqi_pay_paytype']) && preg_match('/^\\w+$/', $jieqiTset['jieqi_pay_paytype']) && strlen($jieqiTset['jieqi_pay_paytype']) < 64) {
        $paytype = $jieqiTset['jieqi_pay_paytype'];
    } else {
        if (substr($paytpl, -3) == 'pay') {
            $paytype = substr($paytpl, 0, -3);
        } else {
            $paytype = '';
        }
    }
}
@define('JIEQI_PAY_TYPE', $paytype);
if (!empty($paytype)) {
    jieqi_getconfigs('pay', $paytype, 'jieqiPayset');
    $jieqiTpl->assign('paytype', jieqi_htmlstr($paytype));
    $paytype_name = isset($jieqiPayset[$paytype]['paytype']) ? $jieqiPayset[$paytype]['paytype'] : '';
    $jieqiTpl->assign('paytype_name', jieqi_htmlstr($paytype_name));
    $subtype_name = isset($_REQUEST['subtype']) && isset($jieqiPayset[$paytype]['subtype'][$_REQUEST['subtype']]) ? $jieqiPayset[$paytype]['subtype'][$_REQUEST['subtype']] : (isset($jieqiLang['pay']['pay_subtype_title']) ? $jieqiLang['pay']['pay_subtype_title'] : '');
    $jieqiTpl->assign('subtype_name', jieqi_htmlstr($subtype_name));
    $fromtype_name = isset($_REQUEST['fromtype']) && isset($jieqiPayset[$paytype]['fromtype'][$_REQUEST['fromtype']]) ? $jieqiPayset[$paytype]['fromtype'][$_REQUEST['fromtype']] : '';
    $jieqiTpl->assign('fromtype_name', jieqi_htmlstr($fromtype_name));
}
if (!empty($_REQUEST['a']) && preg_match('/^\\w+$/', $_REQUEST['a']) && strlen($_REQUEST['a']) < 64) {
    $payaction = $_REQUEST['a'];
} else {
    $payaction = 'payaction';
}
if (!empty($payaction)) {
    jieqi_getconfigs('pay', $payaction, 'jieqiPayaction');
}
if (!empty($jieqiPayset[$paytype])) {
    if (empty($jieqiPayset[$paytype]['paydefault']) && is_array($jieqiPayset[$paytype]['paylimit'])) {
        reset($jieqiPayset[$paytype]['paylimit']);
        $jieqiPayset[$paytype]['paydefault'] = key($jieqiPayset[$paytype]['paylimit']);
    }
    $jieqiTpl->assign('paytype', $paytype);
    $jieqiTpl->assign('payset', jieqi_funtoarray('jieqi_htmlstr', $jieqiPayset[$paytype]));
} else {
    $jieqiTpl->assign('paytype', '');
    $jieqiTpl->assign('payset', '');
}
if (!empty($jieqiPayAction)) {
    foreach ($jieqiPayAction as $k => $v) {
        if (!empty($v['expiretime']) && strtotime($v['expiretime']) < JIEQI_NOW_TIME) {
            $jieqiPayAction[$k]['isexpire'] = 1;
        } else {
            $jieqiPayAction[$k]['isexpire'] = 0;
        }
        if (empty($v['expiretime'])) {
            $v['expiretime'] = '';
        }
    }
    $jieqiTpl->assign('payaction', jieqi_funtoarray('jieqi_htmlstr', $jieqiPayAction));
} else {
    $jieqiTpl->assign('payaction', array());
}
if (!empty($_REQUEST['jumpurl']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_REQUEST['jumpurl'])) {
    $jumpurl = $_REQUEST['jumpurl'];
} else {
    if (!empty($_SERVER['HTTP_REFERER']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_SERVER['HTTP_REFERER']) && !preg_match('/(\\/pay\\/\\w+\\.php)/i', $_SERVER['HTTP_REFERER'])) {
        $jumpurl = $_SERVER['HTTP_REFERER'];
    } else {
        $jumpurl = JIEQI_USER_URL . '/userdetail.php';
    }
}
$jieqiTpl->assign('jumpurl', jieqi_htmlstr($jumpurl));
$jieqiTpl->assign('jumpurl_n', $jumpurl);
include_once JIEQI_ROOT_PATH . '/footer.php';