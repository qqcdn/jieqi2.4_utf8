<?php

function jieqi_api_userinfo($key = '')
{
    $uinfo = OpenSDK_Baidu_Open::call('passport/users/getInfo', array());
    $uinfo = jieqi_api_charsetconvert($uinfo);
    $ret = array();
    $ret['uname'] = $uinfo['username'];
    $ret['sex'] = $uinfo['sex'] == 1 ? 1 : 2;
    $ret['url_avatar'] = 'http://tb.himg.baidu.com/sys/portrait/item/' . $uinfo['portrait'];
    $ret['url_avatars'] = 'http://tb.himg.baidu.com/sys/portraitn/item/' . $uinfo['portrait'];
    $ret['uname'] = jieqi_api_unamefilter($ret['uname']);
    if (strlen($key) == 0) {
        return $ret;
    } else {
        return $ret[$key];
    }
}