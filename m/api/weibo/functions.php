<?php

function jieqi_api_userinfo($key = '')
{
    global $apiName;
    $uinfo = OpenSDK_Sina_Weibo2::call('users/show', array('uid' => $_SESSION['jieqiUserApi'][$apiName]['openid']));
    $uinfo = jieqi_api_charsetconvert($uinfo);
    $ret = array();
    $ret['uname'] = $uinfo['screen_name'];
    $ret['sex'] = $uinfo['gender'] == 'm' ? 1 : ($uinfo['gender'] == 'f' ? 2 : 0);
    $ret['url_avatar'] = $uinfo['avatar_large'];
    $ret['uname'] = jieqi_api_unamefilter($ret['uname']);
    if (strlen($key) == 0) {
        return $ret;
    } else {
        return $ret[$key];
    }
}