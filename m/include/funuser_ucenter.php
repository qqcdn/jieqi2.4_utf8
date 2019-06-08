<?php

function jieqi_uregister_iprepare(&$params)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    $params['uc_uid'] = uc_user_register($params['username'], $params['password'], $params['email']);
    if (0 < $params['uc_uid']) {
        return true;
    } else {
        switch ($params['uc_uid']) {
            case -1:
                $params['error'] = $jieqiLang['system']['error_user_format'];
                break;
            case -2:
                $params['error'] = $jieqiLang['system']['error_user_format'];
                break;
            case -3:
                $params['error'] = $jieqiLang['system']['user_has_registered'];
                break;
            case -4:
                $params['error'] = $jieqiLang['system']['error_email_format'];
                break;
            case -5:
                $params['error'] = $jieqiLang['system']['email_has_registered'];
                break;
            case -6:
                $params['error'] = $jieqiLang['system']['email_has_registered'];
                break;
            default:
                $params['error'] = $jieqiLang['system']['register_failure'];
                break;
        }
        if ($params['return']) {
            return false;
        } else {
            jieqi_printfail($params['error']);
        }
    }
}
function jieqi_uregister_iprocess(&$params)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    if (0 < $params['uc_uid']) {
        $ucsyncode = uc_user_synlogin($params['uc_uid']);
    } else {
        $ucsyncode = '';
    }
    if ($_REQUEST['jumphide']) {
        jieqi_jumppage($params['jumpurl'], '', $ucsyncode, true);
    } else {
        jieqi_jumppage($params['jumpurl'], $jieqiLang['system']['registered_title'], $jieqiLang['system']['register_success'] . $ucsyncode);
    }
    return true;
}
function jieqi_ulogin_iprepare(&$params)
{
    global $jieqiLang;
    list($uid, $username, $password, $email) = uc_user_login($params['username'], $params['password']);
    $params['uc_uid'] = $uid;
    if (0 < $params['uc_uid']) {
        $params['email'] = $email;
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        $user = $users_handler->getByname($params['username'], 3);
        if ($user == false) {
            $newUser = $users_handler->usersAdd($params);
        } else {
            if (is_object($user)) {
                $upflag = false;
                if ($user->getVar('pass', 'n') != $users_handler->encryptPass($params['password'], $user->getVar('salt', 'n'))) {
                    $user->setVar('pass', $users_handler->encryptPass($params['password'], $user->getVar('salt', 'n')));
                    $upflag = true;
                }
                if ($user->getVar('email', 'n') != $params['email']) {
                    $user->setVar('email', $params['email']);
                    $upflag = true;
                }
                if ($upflag) {
                    $users_handler->insert($user);
                }
            }
        }
    }
    return true;
}
function jieqi_ulogin_iprocess(&$params)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    $ucsyncode = '';
    if (0 < $params['uc_uid']) {
        $ucsyncode = uc_user_synlogin($params['uc_uid']);
    } else {
        if ($params['uc_uid'] == -1) {
            $params['uc_uid'] = uc_user_register($_REQUEST['username'], $_REQUEST['password'], $_SESSION['jieqiUserEmail']);
            if (0 < $params['uc_uid']) {
                $ucsyncode = uc_user_synlogin($params['uc_uid']);
            }
        } else {
            if ($params['uc_uid'] == -2) {
                if ($data = uc_get_user($params['username'])) {
                    $params['uc_uid'] = $data[0];
                    if (0 < $params['uc_uid']) {
                        uc_user_edit($params['username'], '', $params['password'], '', 1);
                        $ucsyncode = uc_user_synlogin($params['uc_uid']);
                    }
                }
            } else {
                if ($params['uc_uid'] == -3) {
                    if ($data = uc_get_user($params['username'])) {
                        $params['uc_uid'] = $data[0];
                        if (0 < $params['uc_uid']) {
                            $ucsyncode = uc_user_synlogin($params['uc_uid']);
                        }
                    }
                }
            }
        }
    }
    if ($_REQUEST['jumphide']) {
        jieqi_jumppage($params['jumpurl'], '', $ucsyncode, true);
    } else {
        jieqi_jumppage($params['jumpurl'], $jieqiLang['system']['login_title'], sprintf($jieqiLang['system']['login_success'], jieqi_htmlstr($params['username'])) . $ucsyncode);
    }
    return true;
}
function jieqi_ulogout_iprepare(&$params)
{
    return true;
}
function jieqi_ulogout_iprocess(&$params)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    $ucsyncode = '';
    $ucsyncode = uc_user_synlogout();
    if ($_REQUEST['jumphide']) {
        jieqi_jumppage($params['jumpurl'], '', $ucsyncode, true);
    } else {
        jieqi_jumppage($params['jumpurl'], $jieqiLang['system']['logout_title'], $jieqiLang['system']['logout_success'] . $ucsyncode);
    }
    return true;
}
function jieqi_udelete_iprepare(&$params)
{
    return true;
}
function jieqi_udelete_iprocess(&$params)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    uc_user_delete($params['username']);
    if ($_REQUEST['jumphide']) {
        header('Location: ' . jieqi_headstr($params['jumpurl']));
    } else {
        jieqi_jumppage($params['jumpurl'], LANG_DO_SUCCESS, $jieqiLang['system']['delete_user_success']);
    }
    return true;
}
function jieqi_uedit_iprepare(&$params)
{
    return true;
}
function jieqi_uedit_iprocess(&$params)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    $ucresult = uc_user_edit($params['username'], strval($params['oldpass']), strval($params['newpass']), strval($params['email']), 1);
    $lang_success = empty($_REQUEST['lang_success']) ? $jieqiLang['system']['change_user_success'] : $_REQUEST['lang_success'];
    if ($_REQUEST['jumphide']) {
        header('Location: ' . jieqi_headstr($params['jumpurl']));
    } else {
        jieqi_jumppage($params['jumpurl'], LANG_DO_SUCCESS, $lang_success);
    }
    return true;
}
include_once JIEQI_ROOT_PATH . '/api/ucenter/config.inc.php';
include_once JIEQI_ROOT_PATH . '/api/ucenter/client.php';