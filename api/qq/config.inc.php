<?php
//QQ登录接口参数设置
//未申请QQ登录接口账号的，请到 https://connect.qq.com/ 提交申请

$apiOrder = 1; //接口序号，请勿修改
$apiName = 'qq'; //接口名，请勿修改
$apiTitle = 'QQ'; //接口标题，请勿修改

$apiConfigs[$apiName] = array(); //初始化参数数组，请勿修改

$apiConfigs[$apiName]['appid'] = '000000';  //应用ID，根据实际申请的值修改

$apiConfigs[$apiName]['appkey'] = '000000';  //接口密钥，根据实际申请的值修改

$apiConfigs[$apiName]['callback'] = JIEQI_LOCAL_URL.'/api/'.$apiName.'/loginback.php';  //登录后返回的本站地址，请勿修改

$apiConfigs[$apiName]['ismobile'] = 0; //是否显示手机站样式 0-pc站 1-wml手机站 2-xhtml手机站

$apiConfigs[$apiName]['scope'] = 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo'; //允许授权哪些api接口，用英文逗号分隔

$apiConfigs[$apiName]['binduser'] = 0; //是否需要绑定本站会员 0-不绑定，自动注册本站会员；1-提示直接访问本站，或者绑定本站现有会员或者注册一个新会员用于绑定

