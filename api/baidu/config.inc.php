<?php
//新浪微博登录接口参数设置

$apiOrder = 3; //接口序号，请勿修改
$apiName = 'baidu'; //接口名，请勿修改
$apiTitle = '百度'; //接口标题，请勿修改

$apiConfigs[$apiName] = array(); //初始化参数数组，请勿修改

$apiConfigs[$apiName]['appid'] = '000000';  //应用ID，根据实际申请的值修改

$apiConfigs[$apiName]['appkey'] = '000000';  //接口密钥，根据实际申请的值修改

$apiConfigs[$apiName]['callback'] = JIEQI_LOCAL_URL.'/api/'.$apiName.'/loginback.php';  //登录后返回的本站地址，请勿修改

$apiConfigs[$apiName]['display'] = 'page';
//page：全屏形式的授权页面(默认)，适用于web应用。
//popup: 弹框形式的授权页面，适用于桌面软件应用和web应用。
//dialog:浮层形式的授权页面，只能用于站内web应用。
//mobile: Iphone/Android等智能移动终端上用的授权页面，适用于Iphone/Android等智能移动终端上的应用。
//tv: 电视等超大显示屏使用的授权页面。
//pad: IPad/Android等智能平板电脑使用的授权页面。

$apiConfigs[$apiName]['scope'] = ''; //允许授权哪些api接口，用英文逗号分隔

$apiConfigs[$apiName]['binduser'] = 0; //是否需要绑定本站会员 0-不绑定，自动注册本站会员；1-提示直接访问本站，或者绑定本站现有会员或者注册一个新会员用于绑定
