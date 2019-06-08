<?php

define('JIEQI_MODULE_NAME', 'system');
define('JIEQI_NEED_SESSION', 1);
require_once '../../global.php';
include_once JIEQI_ROOT_PATH . '/lib/OpenSDK/Baidu/Open.php';
include_once './config.inc.php';
include_once './functions.php';
include_once '../include/lang_userapi.php';
include_once '../include/funuserapi.php';
if (empty($_SESSION['jieqiUserApi'][$apiName]['openid'])) {
    jieqi_printfail(sprintf($jieqiLang['system']['api_bind_need_login'], $apiTitle));
}
OpenSDK_Baidu_Open::init($apiConfigs[$apiName]['appid'], $apiConfigs[$apiName]['appkey']);
$jieqiUsers = jieqi_api_bindcheck();
if (is_object($jieqiUsers)) {
    jieqi_api_bindlogin($jieqiUsers);
    exit;
}
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
if (!isset($_REQUEST['act']) && isset($_REQUEST['action'])) {
    $_REQUEST['act'] = $_REQUEST['action'];
}
if (!isset($_REQUEST['act'])) {
    $_REQUEST['act'] = 'show';
}
switch ($_REQUEST['act']) {
    case 'bindnew':
        jieqi_loadlang('users', JIEQI_MODULE_NAME);
        if (defined('JIEQI_USER_INTERFACE') && preg_match('/^\\w+$/is', JIEQI_USER_INTERFACE)) {
            include_once JIEQI_ROOT_PATH . '/include/funuser_' . JIEQI_USER_INTERFACE . '.php';
        } else {
            include_once JIEQI_ROOT_PATH . '/include/funuser.php';
        }
        include_once JIEQI_ROOT_PATH . '/include/userlocal.php';
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $params =& $_REQUEST;
        if (empty($params['uip']) || !is_numeric(str_replace('.', '', $params['uip']))) {
            $params['uip'] = jieqi_userip();
        }
        $params['username'] = trim($params['username']);
        $fromstr = $params['username'];
        $strlen = strlen($fromstr);
        $tmpstr = '';
        for ($i = 0; $i < $strlen; $i++) {
            if (128 < ord($fromstr[$i])) {
                $tmpstr .= $fromstr[$i] . $fromstr[$i + 1];
                $i++;
            } else {
                $tmpstr .= strtolower($fromstr[$i]);
            }
        }
        $params['username'] = $tmpstr;
        $params['email'] = trim($params['email']);
        $params['password'] = trim($params['password']);
        $params['repassword'] = trim($params['repassword']);
        $params['mobile'] = isset($params['mobile']) ? trim($params['mobile']) : '';
        if (empty($params['checkcode'])) {
            $params['checkcode'] = '';
        } else {
            $params['checkcode'] = trim($params['checkcode']);
        }
        $params['error'] = '';
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        define('JIEQI_NO_CHECKCODE', 1);
        jieqi_uregister_lprepare($params);
        jieqi_uregister_iprepare($params);
        jieqi_uregister_lprocess($params);
        jieqi_api_binduser(array('isregister' => 1, 'userid' => $_SESSION['jieqiUserId']));
        jieqi_uregister_iprocess($params);
        break;
    case 'bindold':
        jieqi_loadlang('users', JIEQI_MODULE_NAME);
        include_once JIEQI_ROOT_PATH . '/include/useraction.php';
        $params =& $_REQUEST;
        define('JIEQI_NO_CHECKCODE', 1);
        jieqi_ulogin_lprepare($params);
        jieqi_ulogin_iprepare($params);
        jieqi_ulogin_lprocess($params);
        jieqi_api_binduser(array('isregister' => 0, 'userid' => $_SESSION['jieqiUserId']));
        jieqi_ulogin_iprocess($params);
        break;
    case 'bindauto':
        $jieqiUsers = jieqi_api_bindauto();
        jieqi_api_bindlogin($jieqiUsers);
        break;
    default:
        jieqi_api_bindshow();
        break;
}