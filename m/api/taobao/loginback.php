<?php

define('JIEQI_MODULE_NAME', 'system');
define('JIEQI_NEED_SESSION', 1);
require_once '../../global.php';
include_once JIEQI_ROOT_PATH . '/lib/OpenSDK/Taobao/Taobao2.php';
include_once './config.inc.php';
include_once './functions.php';
include_once '../include/lang_userapi.php';
include_once '../include/funuserapi.php';
OpenSDK_Taobao_Oauth2::init($apiConfigs[$apiName]['appid'], $apiConfigs[$apiName]['appkey']);
if (empty($_SESSION['jieqiUserApi'][$apiName]['openid'])) {
    if (isset($_GET['code']) && $_SESSION['jieqiUserApi'][$apiName]['state'] == $_GET['state']) {
        $ret = OpenSDK_Taobao_Oauth2::getAccessToken('code', array('code' => $_GET['code'], 'redirect_uri' => $apiConfigs[$apiName]['callback']));
        if (isset($ret['error'])) {
            jieqi_printfail(sprintf($jieqiLang['system']['api_access_token_failure'], $ret['error'], $ret['error_description']));
        }
    } else {
        jieqi_printfail($jieqiLang['system']['api_error_callback_params']);
    }
    $_SESSION['jieqiUserApi'][$apiName]['expire_time'] = JIEQI_NOW_TIME + $_SESSION['jieqiUserApi'][$apiName]['expire_in'];
}
if (empty($_SESSION['jieqiUserApi'][$apiName]['openid'])) {
    jieqi_printfail($jieqiLang['system']['api_error_callback_getvar']);
}
$jieqiUsers = jieqi_api_bindcheck();
if (!is_object($jieqiUsers)) {
    if (!empty($_SESSION['jieqiUserId'])) {
        jieqi_api_binduser(array('isregister' => 0, 'userid' => $_SESSION['jieqiUserId']));
        jieqi_api_loginjump($_SESSION['jieqiUserName']);
        exit;
    } else {
        if (!empty($apiConfigs[$apiName]['binduser'])) {
            jieqi_api_bindshow();
            exit;
        } else {
            $jieqiUsers = jieqi_api_bindauto();
        }
    }
}
jieqi_api_bindlogin($jieqiUsers);