<?php 
/**
 * 后台系统管理导航配置
 *
 * 后台系统管理导航配置
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: adminmenu.php 187 2008-11-24 09:30:03Z juny $
 */

/**
'layer'     - 菜单深度，默认 0
'caption'   - 菜单标题
'command'   - 链接的网址
'target'    - 点击链接是否打开新窗口(0-不新开；1-新开)
'publish'   - 是否显示（0-不显示；1-显示）
*/

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '系统定义', 'command'=>JIEQI_URL.'/admin/configs.php?mod=system&define=1', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '参数设置', 'command'=>JIEQI_URL.'/admin/configs.php?mod=system', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '权限设置', 'command'=>JIEQI_URL.'/admin/power.php?mod=system', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '权利设置', 'command'=>JIEQI_URL.'/admin/right.php?mod=system', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '用户组管理', 'command'=>JIEQI_URL.'/admin/groups.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '头衔管理', 'command'=>JIEQI_URL.'/admin/honors.php', 'target' => 0, 'publish' => 1);

if(defined('JIEQI_PC_LOCATION') && defined('JIEQI_MOBILE_LOCATION') && strlen(JIEQI_PC_LOCATION) > 0 && strlen(JIEQI_MOBILE_LOCATION) > 0){
	$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '电脑版区块配置', 'command'=>JIEQI_PC_LOCATION.'/admin/blockfiles.php', 'target' => 0, 'publish' => 1);
	$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '手机版区块配置', 'command'=>JIEQI_MOBILE_LOCATION.'/admin/blockfiles.php', 'target' => 0, 'publish' => 1);
}else{
	$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '区块配置', 'command'=>JIEQI_URL.'/admin/blockfiles.php', 'target' => 0, 'publish' => 1);
}

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '区块列表', 'command'=>JIEQI_URL.'/admin/blocks.php?modname=1', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '模块配置', 'command'=>JIEQI_URL.'/admin/managemodules.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '系统日志', 'command'=>JIEQI_URL.'/admin/logs.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '系统收件箱', 'command'=>JIEQI_URL.'/admin/message.php?box=inbox', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '系统发件箱', 'command'=>JIEQI_URL.'/admin/message.php?box=outbox', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '发送消息', 'command'=>JIEQI_URL.'/admin/newmessage.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['system'][] = array('layer' => 0, 'caption' => '全部站内消息', 'command'=>JIEQI_URL.'/admin/messagelist.php', 'target' => 0, 'publish' => 1);

//==========================================================
//用户管理导航
//==========================================================
$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '用户管理', 'command'=>JIEQI_URL.'/admin/users.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '联系人管理', 'command'=>JIEQI_URL.'/admin/personlist.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '用户增长统计', 'command'=>JIEQI_URL.'/admin/usergrowth.php', 'target' => 0, 'publish' => 1);

//$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '用户报告', 'command'=>JIEQI_URL.'/admin/reportlist.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '用户管理日志', 'command'=>JIEQI_URL.'/admin/userlog.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '用户分成记录', 'command'=>JIEQI_URL.'/admin/earnlog.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '用户提现记录', 'command'=>JIEQI_URL.'/admin/settlelog.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '会客室主题', 'command'=>JIEQI_URL.'/admin/ptopiclist.php?display=0', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '会客室帖子', 'command'=>JIEQI_URL.'/admin/ppostlist.php?display=0', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '会客室待审', 'command'=>JIEQI_URL.'/admin/ppostlist.php?display=1', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '在线用户', 'command'=>JIEQI_URL.'/admin/online.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '充值记录', 'command'=>$GLOBALS['jieqiModules']['pay']['url'].'/admin/paylog.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['users'][] = array('layer' => 0, 'caption' => '充值统计', 'command'=>$GLOBALS['jieqiModules']['pay']['url'].'/admin/paystat.php', 'target' => 0, 'publish' => 1);

//==========================================================
//数据库维护导航
//==========================================================

$jieqiAdminmenu['database'][] = array('layer' => 0, 'caption' => '数据库备份', 'command'=>JIEQI_URL.'/admin/dbmanage.php?option=export', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['database'][] = array('layer' => 0, 'caption' => '数据库恢复', 'command'=>JIEQI_URL.'/admin/dbmanage.php?option=import', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['database'][] = array('layer' => 0, 'caption' => '数据库优化', 'command'=>JIEQI_URL.'/admin/dboptimize.php?option=optimize', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['database'][] = array('layer' => 0, 'caption' => '数据库修复', 'command'=>JIEQI_URL.'/admin/dboptimize.php?option=repair', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['database'][] = array('layer' => 0, 'caption' => '数据库升级', 'command'=>JIEQI_URL.'/admin/dbquery.php', 'target' => 0, 'publish' => 1);

//==========================================================
//系统工具导航
//==========================================================

//$jieqiAdminmenu['tools'][] = array('layer' => 0, 'caption' => '刷新静态首页', 'command'=>JIEQI_URL.'/indexs.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['tools'][] = array('layer' => 0, 'caption' => '清理区块缓存', 'command'=>JIEQI_URL.'/admin/cleancache.php?target=blockcache', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['tools'][] = array('layer' => 0, 'caption' => '清理网页缓存', 'command'=>JIEQI_URL.'/admin/cleancache.php?target=cache', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['tools'][] = array('layer' => 0, 'caption' => '清理程序编译缓存', 'command'=>JIEQI_URL.'/admin/cleancache.php?target=compiled', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['tools'][] = array('layer' => 0, 'caption' => '系统信息及优化建议', 'command'=>JIEQI_URL.'/admin/sysinfo.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['tools'][] = array('layer' => 0, 'caption' => '系统收件箱', 'command'=>JIEQI_URL.'/admin/message.php?box=inbox', 'target' => 0, 'publish' => 1);

?>