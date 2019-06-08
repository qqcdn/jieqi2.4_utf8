<?php

function jieqi_uregister_lprepare(&$params)
{
    global $jieqiConfigs;
    global $jieqiLang;
    global $query;
    global $users_handler;
    global $jieqiDeny;
    global $jieqiAction;
    if (!isset($jieqiAction['system'])) {
        jieqi_getconfigs('system', 'action', 'jieqiAction');
    }
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    if (empty($params['uip']) || !is_numeric(str_replace('.', '', $params['uip']))) {
        $params['uip'] = jieqi_userip();
    }
    $jieqiConfigs['system']['regtimelimit'] = intval($jieqiConfigs['system']['regtimelimit']);
    if (0 < $jieqiConfigs['system']['regtimelimit']) {
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_registerip') . ' WHERE ip=\'' . jieqi_dbslashes($params['uip']) . '\' AND regtime>' . (JIEQI_NOW_TIME - $jieqiConfigs['system']['regtimelimit'] * 3600) . ' LIMIT 0,1';
        $res = $query->execute($sql);
        if ($query->getRow()) {
            $params['error'] = sprintf($jieqiLang['system']['user_register_timelimit'], $jieqiConfigs['system']['regtimelimit']);
            if ($params['return']) {
                return false;
            } else {
                jieqi_printfail($params['error']);
            }
        }
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
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $errors = $users_handler->validField('username', $params['username']);
    if (!empty($errors)) {
        $params['error'] .= implode('<br />', $errors) . '<br />';
    }
    if (isset($params['nickname'])) {
        $errors = $users_handler->validField('nickname', $params['nickname']);
        if (!empty($errors)) {
            $params['error'] .= implode('<br />', $errors) . '<br />';
        }
    } else {
        $params['nickname'] = $params['username'];
    }
    $errors = $users_handler->validField('email', $params['email']);
    if (!empty($errors)) {
        $params['error'] .= implode('<br />', $errors) . '<br />';
    }
    $errors = $users_handler->validField('mobile', $params['mobile']);
    if (!empty($errors)) {
        $params['error'] .= implode('<br />', $errors) . '<br />';
    }
    $errors = $users_handler->validField('password', array('password' => $params['password'], 'repassword' => $params['repassword']));
    if (!empty($errors)) {
        $params['error'] .= implode('<br />', $errors) . '<br />';
    }
    $params['verify'] = 0;
    if (!defined('JIEQI_NO_CHECKCODE') && defined('JIEQI_REGISTER_CHECKCODE') && 0 < JIEQI_REGISTER_CHECKCODE) {
        if (0 < (JIEQI_REGISTER_CHECKCODE & 1)) {
            if (empty($params['checkcode']) || empty($_SESSION['jieqiCheckCode']) || strtolower($params['checkcode']) != strtolower($_SESSION['jieqiCheckCode'])) {
                $params['error'] .= $jieqiLang['system']['error_checkcode'] . '<br />';
            }
        }
        if (0 < (JIEQI_REGISTER_CHECKCODE & 2)) {
            if (empty($params['emailrand']) || empty($_SESSION['jieqiRandCode']['emailcode']) || empty($_SESSION['jieqiRandCode']['emailtime']) || strtolower($params['emailrand']) != strtolower($_SESSION['jieqiRandCode']['emailcode']) || 300 < JIEQI_NOW_TIME - $_SESSION['jieqiRandCode']['emailtime']) {
                $params['error'] .= $jieqiLang['system']['error_emailrand'] . '<br />';
            } else {
                $params['verify'] = $params['verify'] | 1;
            }
        }
        if (0 < (JIEQI_REGISTER_CHECKCODE & 4)) {
            if (empty($params['mobilerand']) || empty($_SESSION['jieqiRandCode']['mobilecode']) || empty($_SESSION['jieqiRandCode']['mobiletime']) || strtolower($params['mobilerand']) != strtolower($_SESSION['jieqiRandCode']['mobilecode']) || 300 < JIEQI_NOW_TIME - $_SESSION['jieqiRandCode']['mobiletime']) {
                $params['error'] .= $jieqiLang['system']['error_mobilerand'] . '<br />';
            } else {
                $params['verify'] = $params['verify'] | 2;
            }
        }
    }
    if (!empty($params['error'])) {
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    } else {
        return true;
    }
}
function jieqi_uregister_lprocess(&$params)
{
    global $jieqiConfigs;
    global $jieqiLang;
    global $query;
    global $users_handler;
    global $jieqiAction;
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $newUser = $users_handler->usersAdd($params);
    if (!$newUser) {
        $params['uid'] = 0;
        $params['error'] = $jieqiLang['system']['register_failure'];
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    } else {
        $params['uid'] = $newUser->getVar('uid', 'n');
        include_once JIEQI_ROOT_PATH . '/include/checklogin.php';
        jieqi_loginprocess($newUser);
        if (!defined('JIEQI_NO_CHECKCODE') && defined('JIEQI_REGISTER_CHECKCODE') && 0 < JIEQI_REGISTER_CHECKCODE) {
            if (0 < (JIEQI_REGISTER_CHECKCODE & 1)) {
                if (isset($_SESSION['jieqiCheckCode'])) {
                    unset($_SESSION['jieqiCheckCode']);
                }
            }
            if (0 < (JIEQI_REGISTER_CHECKCODE & 2)) {
                if (isset($_SESSION['jieqiRandCode']['emailcode'])) {
                    unset($_SESSION['jieqiRandCode']['emailcode']);
                }
                if (isset($_SESSION['jieqiRandCode']['emailtime'])) {
                    unset($_SESSION['jieqiRandCode']['emailtime']);
                }
            }
            if (0 < (JIEQI_REGISTER_CHECKCODE & 4)) {
                if (isset($_SESSION['jieqiRandCode']['mobilecode'])) {
                    unset($_SESSION['jieqiRandCode']['mobilecode']);
                }
                if (isset($_SESSION['jieqiRandCode']['mobiletime'])) {
                    unset($_SESSION['jieqiRandCode']['mobiletime']);
                }
            }
        }
        if (!empty($jieqiConfigs['system']['registeremail']) && 0 < strlen($newUser->getVar('email', 'n'))) {
            include_once JIEQI_ROOT_PATH . '/lib/mail/mail.php';
            $to = $newUser->getVar('email', 'n');
            $url_emailverify = JIEQI_USER_URL . '/emailverify.php?id=' . $newUser->getVar('uid', 'n') . '&checkcode=' . md5($newUser->getVar('email', 'n') . $newUser->getVar('uid', 'n') . $newUser->getVar('regdate', 'n') . $newUser->getVar('salt', 'n'));
            $title = sprintf($jieqiLang['system']['register_email_title'], JIEQI_SITE_NAME);
            $htmlformat = false;
            $c_template = JIEQI_ROOT_PATH . '/templates/emailregister.html';
            if (is_file($c_template)) {
                include_once JIEQI_ROOT_PATH . '/header.php';
                $jieqiTpl->assign('uid', $newUser->getVar('uid'));
                $jieqiTpl->assign('email', $newUser->getVar('email'));
                $jieqiTpl->assign('uname', $newUser->getVar('uname'));
                $jieqiTpl->assign('name', $newUser->getVar('name'));
                $jieqiTpl->assign('title', jieqi_htmlstr($title));
                $jieqiTpl->assign('url_emailverify', $url_emailverify);
                $jieqiTpl->setCaching(0);
                $content = $jieqiTpl->fetch($c_template);
                $htmlformat = true;
            } else {
                $content = sprintf($jieqiLang['system']['register_email_content'], $params['username'], JIEQI_SITE_NAME, JIEQI_LOCAL_URL, $url_emailverify);
            }
            $mailvars = array();
            if (isset($jieqiConfigs['system']['mailtype'])) {
                $mailvars['mailtype'] = $jieqiConfigs['system']['mailtype'];
            }
            if (isset($jieqiConfigs['system']['maildelimiter'])) {
                $mailvars['maildelimiter'] = $jieqiConfigs['system']['maildelimiter'];
            }
            if (isset($jieqiConfigs['system']['mailfrom'])) {
                $mailvars['mailfrom'] = $jieqiConfigs['system']['mailfrom'];
            }
            if (isset($jieqiConfigs['system']['mailserver'])) {
                $mailvars['mailserver'] = $jieqiConfigs['system']['mailserver'];
            }
            if (isset($jieqiConfigs['system']['mailport'])) {
                $mailvars['mailport'] = $jieqiConfigs['system']['mailport'];
            }
            if (isset($jieqiConfigs['system']['mailauth'])) {
                $mailvars['mailauth'] = $jieqiConfigs['system']['mailauth'];
            }
            if (isset($jieqiConfigs['system']['mailuser'])) {
                $mailvars['mailuser'] = $jieqiConfigs['system']['mailuser'];
            }
            if (isset($jieqiConfigs['system']['mailpassword'])) {
                $mailvars['mailpassword'] = $jieqiConfigs['system']['mailpassword'];
            }
            $mailvars['contenttype'] = $htmlformat == true ? 'text/html' : 'text/plain';
            $jieqimail = new JieqiMail($to, $title, $content, $mailvars);
            $jieqimail->sendmail();
            if (!$jieqimail->isError(JIEQI_ERROR_RETURN)) {
                $jieqiLang['system']['register_success'] = $jieqiLang['system']['register_success_email'];
            }
        }
    }
    if (empty($params['jumpurl']) || !preg_match('/^(\\/\\w+|https?:\\/\\/)/i', $_REQUEST['jumpurl'])) {
        $params['jumpurl'] = JIEQI_URL . '/';
    }
    return true;
}
function jieqi_ulogin_lprepare(&$params)
{
    $params['username'] = trim($params['username']);
    return true;
}
function jieqi_ulogin_lprocess(&$params)
{
    global $jieqiLang;
    global $query;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    include_once JIEQI_ROOT_PATH . '/include/checklogin.php';
    if (isset($params['usecookie']) && is_numeric($params['usecookie'])) {
        $params['usecookie'] = intval($params['usecookie']);
    } else {
        $params['usecookie'] = 0;
    }
    if (empty($params['checkcode'])) {
        $params['checkcode'] = '';
    }
    if (!isset($params['uidtype'])) {
        $params['uidtype'] = 0;
    }
    $islogin = jieqi_logincheck($params['username'], $params['password'], $params['checkcode'], $params['usecookie'], 0, intval($params['uidtype']));
    if ($islogin == 0) {
        if (defined('JIEQI_ADMIN_LOGIN')) {
            global $jieqiPower;
            if (!isset($jieqiPower['system'])) {
                jieqi_getconfigs('system', 'power');
            }
            $jieqiUsersGroup = $_SESSION['jieqiUserGroup'];
            switch ($_SESSION['jieqiUserGroup']) {
                case JIEQI_GROUP_GUEST:
                    $jieqiUsersStatus = JIEQI_GROUP_GUEST;
                    break;
                case JIEQI_GROUP_ADMIN:
                    $jieqiUsersStatus = JIEQI_GROUP_ADMIN;
                    break;
                default:
                    $jieqiUsersStatus = JIEQI_GROUP_USER;
                    break;
            }
            if (jieqi_checkpower($jieqiPower['system']['adminpanel'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
                $_SESSION['jieqiAdminLogin'] = 1;
                $jieqi_online_info = empty($_COOKIE['jieqiOnlineInfo']) ? array() : jieqi_strtosary($_COOKIE['jieqiOnlineInfo']);
                $jieqi_online_info['jieqiAdminLogin'] = 1;
                @setcookie('jieqiOnlineInfo', jieqi_sarytostr($jieqi_online_info), 0, '/', JIEQI_COOKIE_DOMAIN, 0);
                include_once JIEQI_ROOT_PATH . '/class/logs.php';
                $logs_handler = JieqiLogsHandler::getInstance('JieqiLogsHandler');
                $logdata = array('logtype' => 1);
                $logs_handler->addlog($logdata);
            }
        }
        if (defined('JIEQI_USER_USERAPI') && 0 < JIEQI_USER_USERAPI && 0 < $_SESSION['jieqiUserId']) {
            if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
                jieqi_includedb();
                $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            }
            $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE uid = ' . intval($_SESSION['jieqiUserId']) . ' LIMIT 0,1';
            $query->execute($sql);
            $row = $query->getRow();
            if (is_array($row) && !empty($row['uid'])) {
                $tmpvar = jieqi_unserialize($row['apidata']);
                if (is_array($tmpvar) && 0 < count($tmpvar)) {
                    foreach ($tmpvar as $k => $v) {
                        if (!isset($_SESSION['jieqiUserApi'][$k])) {
                            $_SESSION[$k] = $v;
                        }
                    }
                }
            }
        }
        if (!defined('JIEQI_NO_CHECKCODE') && defined('JIEQI_LOGIN_CHECKCODE') && 0 < JIEQI_LOGIN_CHECKCODE && $params['checkcode'] !== false) {
            if (isset($_SESSION['jieqiCheckCode'])) {
                unset($_SESSION['jieqiCheckCode']);
            }
        }
        if (empty($params['jumpurl'])) {
            if (!empty($params['jumpreferer']) && !empty($_SERVER['HTTP_REFERER']) && basename($_SERVER['HTTP_REFERER']) != 'login.php') {
                $params['jumpurl'] = $_SERVER['HTTP_REFERER'];
            } else {
                $params['jumpurl'] = JIEQI_URL . '/';
            }
        }
    } else {
        switch ($islogin) {
            case -1:
                $params['error'] = $jieqiLang['system']['need_username'];
                break;
            case -2:
                $params['error'] = $jieqiLang['system']['need_password'];
                break;
            case -3:
                $params['error'] = $jieqiLang['system']['need_userpass'];
                break;
            case -4:
                $params['error'] = $jieqiLang['system']['no_this_user'];
                break;
            case -5:
                $params['error'] = $jieqiLang['system']['error_password'];
                break;
            case -6:
                $params['error'] = $jieqiLang['system']['error_userpass'];
                break;
            case -7:
                $params['error'] = $jieqiLang['system']['error_checkcode'];
                break;
            case -8:
                $params['error'] = $jieqiLang['system']['other_has_login'];
                break;
            case -9:
                $params['error'] = $jieqiLang['system']['user_has_denied'];
                break;
            default:
                $params['error'] = $jieqiLang['system']['login_failure'];
                break;
        }
        $params['errorno'] = $islogin;
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    }
    return true;
}
function jieqi_ulogout_lprepare(&$params)
{
    $params['uid'] = isset($_SESSION['jieqiUserId']) ? intval($_SESSION['jieqiUserId']) : 0;
    return true;
}
function jieqi_ulogout_lprocess(&$params)
{
    include_once JIEQI_ROOT_PATH . '/class/online.php';
    $online_handler = JieqiOnlineHandler::getInstance('JieqiOnlineHandler');
    $criteria = new CriteriaCompo(new Criteria('sid', session_id()));
    $criteria->add(new Criteria('uid', $params['uid']), 'OR');
    $online_handler->delete($criteria);
    header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
    if (!empty($_COOKIE['jieqiUserInfo'])) {
        setcookie('jieqiUserInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
    }
    if (!empty($_COOKIE['jieqiOnlineInfo'])) {
        setcookie('jieqiOnlineInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
    }
    if (!empty($_COOKIE[session_name()])) {
        setcookie(session_name(), '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
    }
    @session_unset();
    @session_destroy();
    @session_write_close();
    @session_regenerate_id(true);
    return true;
}
function jieqi_udelete_lprepare(&$params)
{
    return true;
}
function jieqi_udelete_lprocess(&$params)
{
    global $users_handler;
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $user = $users_handler->get($params['uid']);
    if (!is_object($user)) {
        $params['error'] = LANG_NO_USER;
        $params['username'] = '';
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    } else {
        $params['username'] = $user->getVar('uname', 'n');
    }
    if (!$users_handler->delete($params['uid'])) {
        $params['error'] = $jieqiLang['system']['delete_user_failure'];
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    } else {
        $sql = 'DELETE FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE uid = ' . intval($params['uid']);
        $users_handler->execute($sql);
        if (!empty($GLOBALS['jieqiModules']['article']['publish'])) {
            $sql = 'DELETE FROM ' . jieqi_dbprefix('article_credit') . ' WHERE uid = ' . intval($params['uid']);
            $users_handler->execute($sql);
        }
        if (JIEQI_NOW_TIME - intval($user->getVar('lastlogin', 'n')) < 2592000) {
            $userset = jieqi_unserialize($user->getVar('setting', 'n'));
            if (!empty($userset['loginsid'])) {
                $mysid = session_id();
                @session_id(jieqi_headstr($userset['loginsid']));
                @session_destroy();
                @session_id($mysid);
            }
        }
        include_once JIEQI_ROOT_PATH . '/class/userlog.php';
        $userlog_handler = JieqiUserlogHandler::getInstance('JieqiUserlogHandler');
        $newlog = $userlog_handler->create();
        $newlog->setVar('siteid', JIEQI_SITE_ID);
        $newlog->setVar('logtime', JIEQI_NOW_TIME);
        $newlog->setVar('fromid', $_SESSION['jieqiUserId']);
        $newlog->setVar('fromname', $_SESSION['jieqiUserName']);
        $newlog->setVar('toid', $user->getVar('uid', 'n'));
        $newlog->setVar('toname', $user->getVar('uname', 'n'));
        $newlog->setVar('reason', $params['reason']);
        $newlog->setVar('chginfo', $jieqiLang['system']['delete_user']);
        $newlog->setVar('chglog', '');
        $newlog->setVar('isdel', '1');
        $newlog->setVar('userlog', serialize($user->getVars()));
        $userlog_handler->insert($newlog);
        return true;
    }
}
function jieqi_uedit_lprepare(&$params)
{
    global $users_handler;
    global $jieqiPower;
    global $jieqiUsersStatus;
    global $jieqiUsersGroup;
    global $jieqiLang;
    global $jieqiConfigs;
    global $jieqiDeny;
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $user = $users_handler->get($params['uid']);
    if (!is_object($user)) {
        $params['error'] = LANG_NO_USER;
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    } else {
        $params['username'] = $user->getVar('uname', 'n');
    }
    $tmpstr = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);
    if (empty($_SESSION['jieqiAdminLogin']) || strstr($tmpstr, 'useredit.php')) {
        $params['adminlevel'] = 0;
    } else {
        if (!isset($jieqiPower['system'])) {
            jieqi_getconfigs('system', 'power');
        }
        if (jieqi_checkpower($jieqiPower['system']['deluser'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) {
            $params['adminlevel'] = 5;
        } else {
            if (jieqi_checkpower($jieqiPower['system']['adminvip'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) {
                $params['adminlevel'] = 4;
            } else {
                if (jieqi_checkpower($jieqiPower['system']['changegroup'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) {
                    $params['adminlevel'] = 3;
                } else {
                    if (jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) {
                        $params['adminlevel'] = 2;
                    } else {
                        $params['adminlevel'] = 0;
                    }
                }
            }
        }
    }
    if ($params['adminlevel'] == 0) {
        if ($params['uid'] == $_SESSION['jieqiUserId']) {
            $params['adminlevel'] = 1;
        } else {
            if (!empty($params['oldpass']) && ($user->getVar('pass', 'n') == $params['oldpass'] || $user->getVar('pass', 'n') == $users_handler->encryptPass($params['oldpass'], $user->getVar('salt', 'n')))) {
                $params['adminlevel'] = 1;
            }
        }
    }
    if ($params['adminlevel'] == 0) {
        $params['error'] = LANG_NO_PERMISSION;
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    }
    $params['error'] = '';
    if ($params['adminlevel'] == 1) {
        if (isset($params['email'])) {
            $params['email'] = trim($params['email']);
            if (0 < strlen($params['email'])) {
                if (!preg_match('/^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+([\\.][a-z0-9-]+)+$/i', $params['email'])) {
                    $params['error'] .= $jieqiLang['system']['error_email_format'] . '<br />';
                }
                if ($params['email'] != $user->getVar('email', 'n')) {
                    if (0 < $users_handler->getCount(new Criteria('email', $params['email'], '='))) {
                        $params['error'] .= $jieqiLang['system']['email_has_registered'] . '<br />';
                    }
                }
            }
        }
        if (isset($params['mobile'])) {
            $params['mobile'] = trim($params['mobile']);
            if (!preg_match('/^(1[358][0-9]{9})$/', $params['mobile'])) {
                $params['error'] .= $jieqiLang['system']['error_mobile_format'] . '<br />';
            }
        }
        $params['changenick'] = false;
        if (isset($params['nickname']) && $user->getVar('name', 'n') != $params['nickname']) {
            if ($params['nickname'] != '' && $params['nickname'] != $user->getVar('uname', 'n')) {
                jieqi_getconfigs('system', 'deny', 'jieqiDeny');
                if (!empty($jieqiDeny['users'])) {
                    include_once JIEQI_ROOT_PATH . '/include/checker.php';
                    $checker = new JieqiChecker();
                }
                if ($users_handler->getByname($params['nickname'], 3) != false) {
                    $params['error'] .= $jieqiLang['system']['user_name_exists'] . '<br />';
                } else {
                    if (!empty($jieqiDeny['users'])) {
                        $matchwords = $checker->deny_words($params['nickname'], $jieqiDeny['users'], true, true);
                        if (is_array($matchwords)) {
                            $params['error'] .= sprintf($jieqiLang['system']['nickname_deny'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords))) . '<br />';
                        }
                    }
                }
            }
            $params['changenick'] = true;
        }
        if (!empty($params['newpass'])) {
            $params['oldpass'] = isset($params['oldpass']) ? trim($params['oldpass']) : '';
            $params['newpass'] = trim($params['newpass']);
            $params['repass'] = trim($params['repass']);
            if ($params['newpass'] != $params['repass']) {
                $params['error'] .= $jieqiLang['system']['password_not_equal'] . '<br />';
            } else {
                if (strlen($params['newpass']) == 0) {
                    $params['error'] .= $jieqiLang['system']['need_pass_repass'] . '<br />';
                } else {
                    if (0 < strlen($user->getVar('pass', 'n')) && $user->getVar('pass', 'n') != $params['oldpass'] && $user->getVar('pass', 'n') != $users_handler->encryptPass($params['oldpass'], $user->getVar('salt', 'n'))) {
                        $params['error'] .= $jieqiLang['system']['error_old_pass'] . '<br />';
                    }
                }
            }
        }
    }
    if (!empty($params['error'])) {
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    } else {
        return true;
    }
}
function jieqi_uedit_lprocess(&$params)
{
    global $users_handler;
    global $jieqiLang;
    global $jieqiConfigs;
    global $jieqiHonors;
    global $jieqiGroups;
    global $jieqiUsersStatus;
    global $jieqiUsersGroup;
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $user = $users_handler->get($params['uid']);
    if (!is_object($user)) {
        $params['error'] = LANG_NO_USER;
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    }
    $relogin = false;
    $chglog = array();
    $chginfo = '';
    $user->unsetNew();
    if (0 < $params['adminlevel']) {
        if (0 < strlen($params['newpass'])) {
            $user->setVar('pass', $users_handler->encryptPass($params['newpass'], $user->getVar('salt', 'n')));
        }
    }
    if ($params['adminlevel'] == 1) {
        if (isset($params['nickname']) && 0 < strlen($params['nickname'])) {
            $user->setVar('name', $params['nickname']);
        } else {
            $user->setVar('name', $user->getVar('uname', 'n'));
        }
        $user->setVar('sex', intval($params['sex']));
        if (isset($params['email']) && $params['email'] != $user->getVar('email', 'n')) {
            $user->setVar('email', $params['email']);
            $user->setVar('verify', $user->upUserset('verify', 'email', 0));
        }
        if (isset($params['mobile']) && $params['mobile'] != $user->getVar('mobile', 'n')) {
            $user->setVar('mobile', $params['mobile']);
            $user->setVar('verify', $user->upUserset('verify', 'mobile', 0));
        }
        if (isset($params['workid'])) {
            $user->setVar('workid', intval($params['workid']));
        }
        if (isset($params['url'])) {
            $user->setVar('url', $params['url']);
        }
        if (isset($params['qq'])) {
            $user->setVar('qq', $params['qq']);
        }
        if (isset($params['weixin'])) {
            $user->setVar('weixin', $params['weixin']);
        }
        if (isset($params['weibo'])) {
            $user->setVar('weibo', $params['weibo']);
        }
        if ($params['showset_email'] != 1) {
            $params['showset_email'] = 0;
        }
        $user->setVar('showset', $user->upUserset('showset', 'email', $params['showset_email']));
        if ($params['acceptset_email'] != 1) {
            $params['acceptset_email'] = 0;
        }
        $user->setVar('acceptset', $user->upUserset('acceptset', 'email', $params['acceptset_email']));
        if (isset($params['workid']) && intval($user->getVar('workid', 'n')) != intval($params['workid'])) {
            $user->setVar('workid', $params['workid']);
            $params['changework'] = true;
        } else {
            $params['changework'] = false;
        }
        $user->setVar('sign', $params['sign']);
        $user->setVar('intro', $params['intro']);
        if (!$users_handler->insert($user)) {
            $params['error'] = empty($params['lang_failure']) ? $jieqiLang['system']['user_edit_failure'] : $params['lang_failure'];
            if ($params['return']) {
                return false;
            } else {
                jieqi_printfail($params['error']);
            }
        } else {
            if ($params['changework'] && $_SESSION['jieqiUserId'] == $user->getVar('uid')) {
                jieqi_getconfigs('system', 'honors');
                $honorid = jieqi_gethonorid($user->getVar('score'), $jieqiHonors);
                $_SESSION['jieqiUserHonor'] = $jieqiHonors[$honorid]['name'][intval($user->getVar('workid', 'n'))];
            }
            if ($params['changenick'] && $_SESSION['jieqiUserId'] == $user->getVar('uid')) {
                $_SESSION['jieqiUserName'] = 0 < strlen($user->getVar('name', 'n')) ? $user->getVar('name', 'n') : $user->getVar('uname', 'n');
            }
            $user->saveToSession();
            return true;
        }
    } else {
        if (1 < $params['adminlevel']) {
            if (2 <= $params['adminlevel']) {
                if (0 < strlen($params['pass'])) {
                    $user->setVar('pass', $users_handler->encryptPass($params['pass'], $user->getVar('salt', 'n')));
                    $chginfo .= $jieqiLang['system']['userlog_change_password'];
                    $relogin = true;
                }
                if (is_numeric($params['experience']) && $params['experience'] != $user->getVar('experience')) {
                    $chglog['experience']['from'] = $user->getVar('experience');
                    $chglog['experience']['to'] = $params['experience'];
                    $user->setVar('experience', $params['experience']);
                    if ($chglog['experience']['to'] < $chglog['experience']['from']) {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_less_experience'], $chglog['experience']['from'] - $chglog['experience']['to']);
                    } else {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_add_experience'], $chglog['experience']['to'] - $chglog['experience']['from']);
                    }
                }
                if (is_numeric($params['score']) && $params['score'] != $user->getVar('score')) {
                    $chglog['score']['from'] = $user->getVar('score');
                    $chglog['score']['to'] = $params['score'];
                    $user->setVar('score', $params['score']);
                    if ($chglog['score']['to'] < $chglog['score']['from']) {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_less_score'], $chglog['score']['from'] - $chglog['score']['to']);
                    } else {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_add_score'], $chglog['score']['to'] - $chglog['score']['from']);
                    }
                }
            }
            if (3 <= $params['adminlevel']) {
                if (is_numeric($params['groupid']) && $params['groupid'] != $user->getVar('groupid')) {
                    if ($params['groupid'] == JIEQI_GROUP_ADMIN && $jieqiUsersGroup != JIEQI_GROUP_ADMIN) {
                        $params['error'] = $jieqiLang['system']['cant_set_admin'];
                        if ($params['return']) {
                            return false;
                        } else {
                            jieqi_printfail($params['error']);
                        }
                    }
                    $chglog['groupid']['from'] = $user->getVar('groupid');
                    $chglog['groupid']['to'] = $params['groupid'];
                    $user->setVar('groupid', $params['groupid']);
                    $chginfo .= sprintf($jieqiLang['system']['userlog_change_group'], $jieqiGroups[$chglog['groupid']['from']], $jieqiGroups[$chglog['groupid']['to']]);
                    $relogin = true;
                }
            }
            if (4 <= $params['adminlevel']) {
                if (is_numeric($params['isvip']) && $params['isvip'] != $user->getVar('isvip')) {
                    $tmpstr = $user->getViptype();
                    $chglog['isvip']['from'] = $user->getVar('isvip');
                    $chglog['isvip']['to'] = $params['groupid'];
                    $user->setVar('isvip', $params['isvip']);
                    $chginfo .= sprintf($jieqiLang['system']['userlog_change_vip'], $tmpstr, $user->getViptype());
                }
                if (isset($params['overtime'])) {
                    if (!is_numeric($params['overtime']) && 0 < strlen($params['overtime'])) {
                        $params['overtime'] = strtotime($params['overtime']);
                    } else {
                        $params['overtime'] = 0;
                    }
                    if ($params['overtime'] != $user->getVar('overtime')) {
                        $old_overtime = $user->getVar('overtime');
                        if (empty($old_overtime)) {
                            $old_overtime = $jieqiLang['system']['no_overtime_title'];
                        } else {
                            $old_overtime = date('Y-m-d H:i:s', $old_overtime);
                        }
                        $user->setVar('overtime', $params['overtime']);
                        $chginfo .= sprintf($jieqiLang['system']['userlog_change_overtime'], $old_overtime, date('Y-m-d H:i:s', $params['overtime']));
                    }
                }
                $setting = jieqi_unserialize($user->getVar('setting', 'n'));
                if (!is_array($setting)) {
                    $setting = array();
                }
                $setupdate = false;
                if (is_numeric($params['vipvote']) && $params['vipvote'] != intval($setting['gift']['vipvote'])) {
                    $chglog['vipvote']['from'] = intval($setting['gift']['vipvote']);
                    $chglog['vipvote']['to'] = $params['vipvote'];
                    $setting['gift']['vipvote'] = $params['vipvote'];
                    if ($chglog['vipvote']['to'] < $chglog['vipvote']['from']) {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_less_vipvote'], $chglog['vipvote']['from'] - $chglog['vipvote']['to']);
                    } else {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_add_vipvote'], $chglog['vipvote']['to'] - $chglog['vipvote']['from']);
                    }
                    $setupdate = true;
                    if (intval(date('Ym', intval($user->getVar('lastlogin', 'n')))) < intval(date('Ym', JIEQI_NOW_TIME))) {
                        $user->setVar('lastlogin', JIEQI_NOW_TIME);
                    }
                }
                if (is_numeric($params['flower']) && $params['flower'] != intval($setting['gift']['flower'])) {
                    $chglog['flower']['from'] = intval($setting['gift']['flower']);
                    $chglog['flower']['to'] = $params['flower'];
                    $setting['gift']['flower'] = $params['flower'];
                    if ($chglog['flower']['to'] < $chglog['flower']['from']) {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_less_flower'], $chglog['flower']['from'] - $chglog['flower']['to']);
                    } else {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_add_flower'], $chglog['flower']['to'] - $chglog['flower']['from']);
                    }
                    $setupdate = true;
                }
                if (is_numeric($params['egg']) && $params['egg'] != intval($setting['gift']['egg'])) {
                    $chglog['egg']['from'] = intval($setting['gift']['egg']);
                    $chglog['egg']['to'] = $params['egg'];
                    $setting['gift']['egg'] = $params['egg'];
                    if ($chglog['egg']['to'] < $chglog['egg']['from']) {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_less_egg'], $chglog['egg']['from'] - $chglog['egg']['to']);
                    } else {
                        $chginfo .= sprintf($jieqiLang['system']['userlog_add_egg'], $chglog['egg']['to'] - $chglog['egg']['from']);
                    }
                    $setupdate = true;
                }
                if ($setupdate) {
                    $user->setVar('setting', serialize($setting));
                }
            }
            if (!$users_handler->insert($user)) {
                $params['error'] = $jieqiLang['system']['change_user_failure'];
                if ($params['return']) {
                    return false;
                } else {
                    jieqi_printfail($params['error']);
                }
            } else {
                if ($relogin == true && JIEQI_NOW_TIME - intval($user->getVar('lastlogin', 'n')) < 2592000) {
                    $userset = jieqi_unserialize($user->getVar('setting', 'n'));
                    if (!empty($userset['loginsid'])) {
                        $mysid = session_id();
                        @session_id(jieqi_headstr($userset['loginsid']));
                        @session_destroy();
                        @session_id($mysid);
                    }
                }
                include_once JIEQI_ROOT_PATH . '/class/userlog.php';
                $userlog_handler = JieqiUserlogHandler::getInstance('JieqiUserlogHandler');
                $newlog = $userlog_handler->create();
                $newlog->setVar('siteid', JIEQI_SITE_ID);
                $newlog->setVar('logtime', JIEQI_NOW_TIME);
                $newlog->setVar('fromid', $_SESSION['jieqiUserId']);
                $newlog->setVar('fromname', $_SESSION['jieqiUserName']);
                $newlog->setVar('toid', $user->getVar('uid', 'n'));
                $newlog->setVar('toname', $user->getVar('uname', 'n'));
                $newlog->setVar('reason', $params['reason']);
                $newlog->setVar('chginfo', $chginfo);
                $newlog->setVar('chglog', serialize($chglog));
                $newlog->setVar('isdel', '0');
                $newlog->setVar('userlog', '');
                $userlog_handler->insert($newlog);
                return true;
            }
        }
    }
    return true;
}