<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
if (!isset($_POST['act'])) {
    $_POST['act'] = 'getpass';
}
jieqi_loadlang('users', JIEQI_MODULE_NAME);
switch ($_POST['act']) {
    case 'sendpass':
        $_POST['uname'] = trim($_POST['uname']);
        $_POST['email'] = trim($_POST['email']);
        if ($_POST['uname'] == '' || $_POST['email'] == '') {
            jieqi_printfail($jieqiLang['system']['need_user_email']);
        }
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        $user = $users_handler->getByname($_POST['uname']);
        if (is_object($user)) {
            if ($user->getVar('email', 'n') == $_POST['email']) {
                jieqi_getconfigs('system', 'configs');
                include_once JIEQI_ROOT_PATH . '/lib/mail/mail.php';
                $to = $_POST['email'];
                $url_setpass = JIEQI_USER_URL . '/setpass.php?id=' . $user->getVar('uid') . '&checkcode=' . md5($user->getVar('pass'));
                $title = strpos($jieqiLang['system']['reset_password'], '%s') ? sprintf($jieqiLang['system']['reset_password'], JIEQI_SITE_NAME) : $jieqiLang['system']['reset_password'] . '-' . JIEQI_SITE_NAME;
                $htmlformat = false;
                $c_template = JIEQI_ROOT_PATH . '/templates/emailpass.html';
                if (is_file($c_template)) {
                    include_once JIEQI_ROOT_PATH . '/header.php';
                    $jieqiTpl->assign('uname', jieqi_htmlstr($_POST['uname']));
                    $jieqiTpl->assign('email', jieqi_htmlstr($_POST['email']));
                    $jieqiTpl->assign('title', jieqi_htmlstr($title));
                    $jieqiTpl->assign('url_setpass', $url_setpass);
                    $jieqiTpl->setCaching(0);
                    $content = $jieqiTpl->fetch($c_template);
                    $htmlformat = true;
                } else {
                    $content = strpos($jieqiLang['system']['get_password_link'], '%s') ? sprintf($jieqiLang['system']['get_password_link'], $url_setpass) : $jieqiLang['system']['get_password_link'] . $url_setpass;
                }
                $params = array();
                if (isset($jieqiConfigs['system']['mailtype'])) {
                    $params['mailtype'] = $jieqiConfigs['system']['mailtype'];
                }
                if (isset($jieqiConfigs['system']['maildelimiter'])) {
                    $params['maildelimiter'] = $jieqiConfigs['system']['maildelimiter'];
                }
                if (isset($jieqiConfigs['system']['mailfrom'])) {
                    $params['mailfrom'] = $jieqiConfigs['system']['mailfrom'];
                }
                if (isset($jieqiConfigs['system']['mailserver'])) {
                    $params['mailserver'] = $jieqiConfigs['system']['mailserver'];
                }
                if (isset($jieqiConfigs['system']['mailport'])) {
                    $params['mailport'] = $jieqiConfigs['system']['mailport'];
                }
                if (isset($jieqiConfigs['system']['mailauth'])) {
                    $params['mailauth'] = $jieqiConfigs['system']['mailauth'];
                }
                if (isset($jieqiConfigs['system']['mailuser'])) {
                    $params['mailuser'] = $jieqiConfigs['system']['mailuser'];
                }
                if (isset($jieqiConfigs['system']['mailpassword'])) {
                    $params['mailpassword'] = $jieqiConfigs['system']['mailpassword'];
                }
                $params['contenttype'] = $htmlformat == true ? 'text/html' : 'text/plain';
                $jieqimail = new JieqiMail($to, $title, $content, $params);
                $jieqimail->sendmail();
                if ($jieqimail->isError(JIEQI_ERROR_RETURN)) {
                    jieqi_printfail(sprintf($jieqiLang['system']['email_send_failure'], implode('<br />', $jieqimail->getErrors(JIEQI_ERROR_RETURN))));
                } else {
                    jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['send_password_success']);
                }
            } else {
                jieqi_printfail($jieqiLang['system']['email_not_users']);
            }
        } else {
            jieqi_printfail(LANG_NO_USER);
        }
        break;
    case 'getpass':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('url_getpass', JIEQI_USER_URL . '/getpass.php?do=submit');
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/getpass.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}