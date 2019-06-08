<?php
//微信登录接口参数设置
//未申请微信登录接口账号的，请到 http://open.weixin.qq.com/ 提交申请

$apiOrder = 2; //接口序号，请勿修改
$apiName = 'weixin'; //接口名，请勿修改
$apiTitle = '微信'; //接口标题，请勿修改

$apiConfigs[$apiName] = array(); //初始化参数数组，请勿修改

$apiConfigs[$apiName]['appid'] = '000000';  //应用ID，根据实际申请的值修改

$apiConfigs[$apiName]['appkey'] = '000000';  //接口密钥，根据实际申请的值修改

$apiConfigs[$apiName]['callback'] = JIEQI_LOCAL_URL.'/api/'.$apiName.'/loginback.php';  //登录后返回的本站地址，请勿修改

$apiConfigs[$apiName]['scope'] = 'snsapi_login,snsapi_base,snsapi_userinfo'; //允许授权哪些api接口，用英文逗号分隔

$apiConfigs[$apiName]['binduser'] = 0; //是否需要绑定本站会员 0-不绑定，自动注册本站会员；1-提示直接访问本站，或者绑定本站现有会员或者注册一个新会员用于绑定

$apiConfigs[$apiName]['useunionid'] = 1; //是否启用unionid，0-不用 1-使用；如果开发者有在多个公众号，或在公众号、移动应用之间统一用户帐号的需求，需要前往微信开放平台（open.weixin.qq.com）绑定公众号后，才可利用UnionID机制来满足上述需求。
