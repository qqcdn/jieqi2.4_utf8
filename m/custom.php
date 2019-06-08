<?php
/**
 *  自定义页面示例
 */

//定义本页面所属区块，不用修改
define('JIEQI_MODULE_NAME', 'system');
require_once('global.php');

//包含区块配置文件(/configs/customblocks.php)，需要手工创建的文件，各种不同区块的配置写法可以参考后台区块管理
//这里的文件名 customblocks，可以换成自己需要的名字
jieqi_getconfigs(JIEQI_MODULE_NAME, 'customblocks', 'jieqiBlocks');
//不自动显示区块，而是模板里面指定位置调用
if(is_array($jieqiBlocks)) foreach($jieqiBlocks as $k => $v) $jieqiBlocks[$k]['side'] = -1;

//包含页头处理，不用修改
include_once(JIEQI_ROOT_PATH.'/header.php'); 

//设置自定义变量，比如以下设置可以在模板中用 {?$jieqi_domain?} 调用显示
//$jieqiTpl->assign('jieqi_domain', 'jieqi.com'); 

//设置该页面的模板文件，表示在 /templates/custom.html，这个模板调用默认theme.html，所以里面的html代码只要中间内容部分
//如果一下模板是一个完整的包含页头页尾的html，则不套用theme.html，下面的 'jieqi_contents_template' 改成 'jieqi_page_template'
$jieqiTset['jieqi_contents_template']=JIEQI_ROOT_PATH.'/templates/custom.html';

//不使用页面缓存，不用修改
$jieqiTpl->setCaching(0);
//包含页尾处理，不用修改
include_once(JIEQI_ROOT_PATH.'/footer.php');  
?>