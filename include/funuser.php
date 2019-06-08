<?php

function jieqi_uregister_iprepare(&$params)
{
    return true;
}
function jieqi_uregister_iprocess(&$params)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    if ($_REQUEST['jumphide']) {
        header('Location: ' . jieqi_headstr($params['jumpurl']));
    } else {
        jieqi_jumppage($params['jumpurl'], $jieqiLang['system']['registered_title'], $jieqiLang['system']['register_success']);
    }
    return true;
}
function jieqi_ulogin_iprepare(&$params)
{
    return true;
}
function jieqi_ulogin_iprocess(&$params)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'system');
    }
    echo sprintf($jieqiLang['system']['login_success'], jieqi_htmlstr($_REQUEST['username']));
    if (!empty($_REQUEST['jumphide'])) {
        header('Location: ' . jieqi_headstr($params['jumpurl']));
    } else {
        jieqi_jumppage($params['jumpurl'], $jieqiLang['system']['login_title'], sprintf($jieqiLang['system']['login_success'], jieqi_htmlstr($_REQUEST['username'])));
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
    if (!empty($_REQUEST['jumphide'])) {
        header('Location: ' . jieqi_headstr($params['jumpurl']));
    } else {
        jieqi_jumppage($params['jumpurl']);
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
    $lang_success = empty($_REQUEST['lang_success']) ? $jieqiLang['system']['change_user_success'] : $_REQUEST['lang_success'];
    if ($_REQUEST['jumphide']) {
        header('Location: ' . jieqi_headstr($params['jumpurl']));
    } else {
        jieqi_jumppage($params['jumpurl'], LANG_DO_SUCCESS, $lang_success);
    }
    return true;
}