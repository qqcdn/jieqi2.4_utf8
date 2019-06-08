<?php

function jieqi_randcode($len)
{
    $str = '1234567890';
    $result = '';
    $l = strlen($str) - 1;
    srand((double) microtime() * 1000000);
    for ($i = 0; $i < $len; $i++) {
        $num = rand(0, $l);
        $result .= $str[$num];
    }
    return $result;
}
define('JIEQI_MODULE_NAME', 'system');
if (!empty($_REQUEST['sendemail']) && !empty($_REQUEST['type']) && $_REQUEST['type'] == 'randcode') {
    define('JIEQI_NEED_SESSION', 1);
    define('JIEQI_IS_OPEN', 1);
}
require_once 'global.php';
jieqi_loadlang('users', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
if (!empty($_REQUEST['sendemail'])) {
    if ($_REQUEST['type'] == 'randcode') {
        if (empty($_REQUEST['email']) || !preg_match('/^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+([\\.][a-z0-9-]+)+$/i', trim($_REQUEST['email']))) {
            jieqi_printfail($jieqiLang['system']['emailrandcode_email_formaterror']);
        } else {
            $email = trim($_REQUEST['email']);
        }
    } else {
        jieqi_checklogin();
        $user = $users_handler->get($_SESSION['jieqiUserId']);
        if (!is_object($user)) {
            jieqi_printfail(LANG_NO_USER);
        }
        $email = $user->getVar('email', 'n');
        if (empty($email)) {
            jieqi_printfail($jieqiLang['system']['email_not_set']);
        }
        $isverified = $user->getUserset('verify', 'email');
        if (!empty($_REQUEST['cancel'])) {
            if (!$isverified) {
                jieqi_printfail($jieqiLang['system']['emailverify_is_undo']);
            }
        } else {
            if ($isverified) {
                jieqi_printfail($jieqiLang['system']['emailverify_is_finished']);
            }
        }
    }
    jieqi_loadlang('users', 'system');
    jieqi_getconfigs('system', 'configs');
    include_once JIEQI_ROOT_PATH . '/lib/mail/mail.php';
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
    if (empty($_REQUEST['type'])) {
        if (!empty($_REQUEST['cancel'])) {
            $_REQUEST['type'] = 'cancel';
        } else {
            $_REQUEST['type'] = 'verify';
        }
    }
    switch ($_REQUEST['type']) {
        case 'cancel':
            $url_emailverify = JIEQI_USER_URL . '/emailverify.php?id=' . $user->getVar('uid', 'n') . '&checkcode=' . md5($user->getVar('email', 'n') . $user->getVar('uid', 'n') . $user->getVar('regdate', 'n') . $user->getVar('salt', 'n')) . '&cancel=1';
            $title = sprintf($jieqiLang['system']['emailcancel_email_title'], JIEQI_SITE_NAME);
            $htmlformat = false;
            $c_template = JIEQI_ROOT_PATH . '/templates/emailcancel.html';
            if (is_file($c_template)) {
                include_once JIEQI_ROOT_PATH . '/header.php';
                $jieqiTpl->assign('uid', $user->getVar('uid'));
                $jieqiTpl->assign('email', $user->getVar('email'));
                $jieqiTpl->assign('uname', $user->getVar('uname'));
                $jieqiTpl->assign('name', $user->getVar('name'));
                $jieqiTpl->assign('title', jieqi_htmlstr($title));
                $jieqiTpl->assign('url_emailverify', $url_emailverify);
                $jieqiTpl->setCaching(0);
                $content = $jieqiTpl->fetch($c_template);
                $htmlformat = true;
            } else {
                $content = sprintf($jieqiLang['system']['emailverify_email_content'], JIEQI_SITE_NAME, JIEQI_LOCAL_URL, $url_emailverify);
            }
            $params['contenttype'] = $htmlformat == true ? 'text/html' : 'text/plain';
            $jieqimail = new JieqiMail($email, $title, $content, $params);
            $jieqimail->sendmail();
            if ($jieqimail->isError(JIEQI_ERROR_RETURN)) {
                jieqi_printfail(sprintf($jieqiLang['system']['email_send_failure'], implode('<br />', $jieqimail->getErrors(JIEQI_ERROR_RETURN))));
            } else {
                jieqi_jumppage(JIEQI_URL . '/userdetail.php?sendemail=1', $jieqiLang['system']['emailcancel_send_title'], $jieqiLang['system']['emailcancel_send_content']);
            }
            break;
        case 'randcode':
            if (!isset($_SESSION['jieqiRandCode']) || !is_array($_SESSION['jieqiRandCode'])) {
                $_SESSION['jieqiRandCode'] = array();
            }
            if (isset($_SESSION['jieqiRandCode']['emailtime']) && isset($_SESSION['jieqiRandCode']['emailcode']) && JIEQI_NOW_TIME - intval($_SESSION['jieqiRandCode']['emailtime']) < 300) {
                $randcode = $_SESSION['jieqiRandCode']['emailcode'];
            } else {
                $randcode = jieqi_randcode(6);
                $_SESSION['jieqiRandCode']['emailcode'] = $randcode;
                $_SESSION['jieqiRandCode']['emailtime'] = JIEQI_NOW_TIME;
            }
            $title = sprintf($jieqiLang['system']['emailrandcode_email_title'], JIEQI_SITE_NAME);
            $htmlformat = false;
            $c_template = JIEQI_ROOT_PATH . '/templates/emailrandcode.html';
            if (is_file($c_template)) {
                include_once JIEQI_ROOT_PATH . '/header.php';
                $jieqiTpl->assign('email', $email);
                $jieqiTpl->assign('title', jieqi_htmlstr($title));
                $jieqiTpl->assign('randcode', $randcode);
                $jieqiTpl->setCaching(0);
                $content = $jieqiTpl->fetch($c_template);
                $htmlformat = true;
            } else {
                $content = sprintf($jieqiLang['system']['emailrandcode'], JIEQI_SITE_NAME, JIEQI_LOCAL_URL, $randcode);
            }
            $params['contenttype'] = $htmlformat == true ? 'text/html' : 'text/plain';
            $jieqimail = new JieqiMail($email, $title, $content, $params);
            $jieqimail->sendmail();
            if ($jieqimail->isError(JIEQI_ERROR_RETURN)) {
                jieqi_printfail(sprintf($jieqiLang['system']['email_send_failure'], implode('<br />', $jieqimail->getErrors(JIEQI_ERROR_RETURN))));
            } else {
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['emailrandcode_email_success']);
            }
            break;
        case 'verify':
        default:
            $url_emailverify = JIEQI_USER_URL . '/emailverify.php?id=' . $user->getVar('uid', 'n') . '&checkcode=' . md5($user->getVar('email', 'n') . $user->getVar('uid', 'n') . $user->getVar('regdate', 'n') . $user->getVar('salt', 'n'));
            $title = sprintf($jieqiLang['system']['emailverify_email_title'], JIEQI_SITE_NAME);
            $htmlformat = false;
            $c_template = JIEQI_ROOT_PATH . '/templates/emailverify.html';
            if (is_file($c_template)) {
                include_once JIEQI_ROOT_PATH . '/header.php';
                $jieqiTpl->assign('uid', $user->getVar('uid'));
                $jieqiTpl->assign('email', $user->getVar('email'));
                $jieqiTpl->assign('uname', $user->getVar('uname'));
                $jieqiTpl->assign('name', $user->getVar('name'));
                $jieqiTpl->assign('title', jieqi_htmlstr($title));
                $jieqiTpl->assign('url_emailverify', $url_emailverify);
                $jieqiTpl->setCaching(0);
                $content = $jieqiTpl->fetch($c_template);
                $htmlformat = true;
            } else {
                $content = sprintf($jieqiLang['system']['emailverify_email_content'], JIEQI_SITE_NAME, JIEQI_LOCAL_URL, $url_emailverify);
            }
            $params['contenttype'] = $htmlformat == true ? 'text/html' : 'text/plain';
            $jieqimail = new JieqiMail($email, $title, $content, $params);
            $jieqimail->sendmail();
            if ($jieqimail->isError(JIEQI_ERROR_RETURN)) {
                jieqi_printfail(sprintf($jieqiLang['system']['email_send_failure'], implode('<br />', $jieqimail->getErrors(JIEQI_ERROR_RETURN))));
            } else {
                jieqi_jumppage(JIEQI_URL . '/userdetail.php?sendemail=1', $jieqiLang['system']['emailverify_send_title'], $jieqiLang['system']['emailverify_send_content']);
            }
            break;
    }
} else {
    if (!empty($_REQUEST['id']) && !empty($_REQUEST['checkcode'])) {
        $_REQUEST['id'] = intval($_REQUEST['id']);
        $user = $users_handler->get($_REQUEST['id']);
        if (!is_object($user)) {
            jieqi_printfail(LANG_NO_USER);
        }
        if (md5($user->getVar('email', 'n') . $user->getVar('uid', 'n') . $user->getVar('regdate', 'n') . $user->getVar('salt', 'n')) != $_REQUEST['checkcode']) {
            jieqi_printfail($jieqiLang['system']['emailverify_error_checkcode']);
        } else {
            $isverified = $user->getUserset('verify', 'email');
            if (!empty($_REQUEST['cancel'])) {
                if ($isverified) {
                    $user->setVar('verify', $user->upUserset('verify', 'email', 0));
                    $users_handler->insert($user);
                }
                jieqi_jumppage(JIEQI_URL . '/userdetail.php', $jieqiLang['system']['emailcancel_success_title'], $jieqiLang['system']['emailcancel_success_content']);
            } else {
                if (!$isverified) {
                    $user->setVar('verify', $user->upUserset('verify', 'email', 1));
                    $users_handler->insert($user);
                }
                jieqi_jumppage(JIEQI_URL . '/userdetail.php', $jieqiLang['system']['emailverify_success_title'], $jieqiLang['system']['emailverify_success_content']);
            }
        }
    } else {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
}