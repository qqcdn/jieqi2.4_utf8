<?php
//新浪微博登录接口参数设置
//未申请微博登录接口账号的，请到 http://open.weibo.com/ 提交申请

$apiOrder = 4; //接口序号，请勿修改
$apiName = 'weibo'; //接口名，请勿修改
$apiTitle = '新浪微博'; //接口标题，请勿修改

$apiConfigs[$apiName] = array(); //初始化参数数组，请勿修改

$apiConfigs[$apiName]['appid'] = '000000';  //应用ID，根据实际申请的值修改

$apiConfigs[$apiName]['appkey'] = '000000';  //接口密钥，根据实际申请的值修改

$apiConfigs[$apiName]['callback'] = JIEQI_LOCAL_URL.'/api/'.$apiName.'/loginback.php';  //登录后返回的本站地址，请勿修改

$apiConfigs[$apiName]['display'] = 'default';
//default	默认的授权页面，适用于web浏览器。
//mobile	移动终端的授权页面，适用于支持html5的手机。注：使用此版授权页请用 https://open.weibo.cn/oauth2/authorize 授权接口
//wap	wap版授权页面，适用于非智能手机。
//client	客户端版本授权页面，适用于PC桌面应用。
//apponweibo	默认的站内应用授权页，授权后不返回access_token，只刷新站内应用父框架。

$apiConfigs[$apiName]['scope'] = ''; //允许授权哪些api接口，用英文逗号分隔

$apiConfigs[$apiName]['binduser'] = 0; //是否需要绑定本站会员 0-不绑定，自动注册本站会员；1-提示直接访问本站，或者绑定本站现有会员或者注册一个新会员用于绑定
