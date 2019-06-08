<?php

function jieqi_user_register(&$params)
{
    return jieqi_uregister_lprepare($params) && jieqi_uregister_iprepare($params) && jieqi_uregister_lprocess($params) && jieqi_uregister_iprocess($params);
}
function jieqi_user_login(&$params)
{
    return jieqi_ulogin_lprepare($params) && jieqi_ulogin_iprepare($params) && jieqi_ulogin_lprocess($params) && jieqi_ulogin_iprocess($params);
}
function jieqi_user_logout(&$params)
{
    return jieqi_ulogout_lprepare($params) && jieqi_ulogout_iprepare($params) && jieqi_ulogout_lprocess($params) && jieqi_ulogout_iprocess($params);
}
function jieqi_user_delete(&$params)
{
    return jieqi_udelete_lprepare($params) && jieqi_udelete_iprepare($params) && jieqi_udelete_lprocess($params) && jieqi_udelete_iprocess($params);
}
function jieqi_user_edit(&$params)
{
    return jieqi_uedit_lprepare($params) && jieqi_uedit_iprepare($params) && jieqi_uedit_lprocess($params) && jieqi_uedit_iprocess($params);
}
if (defined('JIEQI_USER_INTERFACE') && preg_match('/^\\w+$/is', JIEQI_USER_INTERFACE)) {
    include_once dirname(__FILE__) . '/funuser_' . JIEQI_USER_INTERFACE . '.php';
} else {
    include_once dirname(__FILE__) . '/funuser.php';
}
include_once dirname(__FILE__) . '/userlocal.php';