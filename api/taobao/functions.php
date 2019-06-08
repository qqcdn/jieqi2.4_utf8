<?php

function jieqi_api_userinfo($key = '')
{
    global $apiName;
    $ret = array();
    $ret['uname'] = strval($_SESSION['jieqiUserApi'][$apiName]['user_nick']);
    $ret['sex'] = 0;
    $ret['url_avatar'] = '';
    $ret['uname'] = jieqi_api_unamefilter($ret['uname']);
    if (strlen($key) == 0) {
        return $ret;
    } else {
        return $ret[$key];
    }
}