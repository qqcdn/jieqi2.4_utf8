<?php
//淘宝登录接口参数设置
//未申请淘宝登录接口账号的，请到 http://open.taobao.com/ 提交申请

$apiOrder = 5; //接口序号，请勿修改
$apiName = 'taobao'; //接口名，请勿修改
$apiTitle = '淘宝'; //接口标题，请勿修改

$apiConfigs[$apiName] = array(); //初始化参数数组，请勿修改

$apiConfigs[$apiName]['appid'] = '000000';  //应用ID，根据实际申请的值修改

$apiConfigs[$apiName]['appkey'] = '000000';  //接口密钥，根据实际申请的值修改

$apiConfigs[$apiName]['callback'] = JIEQI_LOCAL_URL.'/api/'.$apiName.'/loginback.php';  //登录后返回的本站地址，请勿修改

$apiConfigs[$apiName]['view'] = 'web';
//web 对应PC端（淘宝logo）浏览器页面样式(默认)
//tmall 对应天猫的浏览器页面样式
//wap 对应无线端的浏览器页面样式

$apiConfigs[$apiName]['scope'] = ''; //允许授权哪些api接口，用英文逗号分隔

$apiConfigs[$apiName]['binduser'] = 0; //是否需要绑定本站会员 0-不绑定，自动注册本站会员；1-提示直接访问本站，或者绑定本站现有会员或者注册一个新会员用于绑定
