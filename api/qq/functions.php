<?php
/**
 * 本接口通用函数
 *
 * 本接口通用函数
 *
 * 调用模板：
 *
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: functions.php 344 2009-06-23 03:06:07Z juny $
 */

//获取接口方默认的用户名（昵称），每个接口必须定义
function jieqi_api_userinfo($key = '')
{
    $uinfo = OpenSDK_Tencent_SNS2::call('user/get_user_info', array());
    $uinfo = jieqi_api_charsetconvert($uinfo);
    /*
     * 返回数据
ret	返回码
msg	如果ret<0，会有相应的错误信息提示，返回数据全部用UTF-8编码。
nickname	用户在QQ空间的昵称。
figureurl	大小为30×30像素的QQ空间头像URL。
figureurl_1	大小为50×50像素的QQ空间头像URL。
figureurl_2	大小为100×100像素的QQ空间头像URL。
figureurl_qq_1	大小为40×40像素的QQ头像URL。
figureurl_qq_2	大小为100×100像素的QQ头像URL。需要注意，不是所有的用户都拥有QQ的100x100的头像，但40x40像素则是一定会有。
gender	性别。 如果获取不到则默认返回"男"
is_yellow_vip	标识用户是否为黄钻用户（0：不是；1：是）。
vip	标识用户是否为黄钻用户（0：不是；1：是）
yellow_vip_level	黄钻等级
level	黄钻等级
is_yellow_year_vip	标识是否为年费黄钻用户（0：不是； 1：是）
     */
    $ret = array();
    $ret['uname'] = $uinfo['nickname'];
    $ret['sex'] = ($uinfo['gender'] == '男') ? 1 : (($uinfo['gender'] == '女') ? 2 : 0);
	$ret['url_avatar'] = !empty($uinfo['figureurl_qq_2']) ? $uinfo['figureurl_qq_2'] : (!empty($uinfo['figureurl_qq_1']) ? $uinfo['figureurl_qq_1'] : (!empty($uinfo['figureurl_2']) ? $uinfo['figureurl_2'] : $uinfo['figureurl_1']));
    $ret['uname'] = jieqi_api_unamefilter($ret['uname']);
    if (strlen($key) == 0) return $ret;
    else return $ret[$key];
}

