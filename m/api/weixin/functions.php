<?php

function jieqi_api_userinfo($key = '')
{
    $uinfo = OpenSDK_Tencent_Weixin::call('sns/userinfo', array(), 'GET');
    $uinfo = jieqi_api_charsetconvert($uinfo);
    $ret = array();
    $ret['uname'] = $uinfo['nickname'];
    $ret['sex'] = $uinfo['sex'];
    $ret['url_avatar'] = $uinfo['headimgurl'];
    $ret['uname'] = jieqi_api_unamefilter($ret['uname']);
    if (!empty($uinfo['unionid'])) {
        $ret['unionid'] = $uinfo['unionid'];
    }
    if (strlen($key) == 0) {
        return $ret;
    } else {
        return $ret[$key];
    }
}