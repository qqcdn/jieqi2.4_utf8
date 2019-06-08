<?php 
/**
 * 后台在线电子书导航配置
 *
 * 后台在线电子书导航配置
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    obook
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

$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '参数设置', 'command'=>JIEQI_URL.'/admin/configs.php?mod=obook', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '权限设置', 'command'=>JIEQI_URL.'/admin/power.php?mod=obook', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '电子书管理', 'command'=>$GLOBALS['jieqiModules']['obook']['url'].'/admin/obooklist.php', 'target' => 0, 'publish' => 1);

//$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '采集CP作品', 'command'=>$GLOBALS['jieqiModules']['article']['url'].'/admin/syncsite.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '销售统计', 'command'=>$GLOBALS['jieqiModules']['obook']['url'].'/admin/salestat.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '销售月报', 'command'=>$GLOBALS['jieqiModules']['obook']['url'].'/admin/mreport.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '订阅记录', 'command'=>$GLOBALS['jieqiModules']['obook']['url'].'/admin/buylog.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '打赏记录', 'command'=>$GLOBALS['jieqiModules']['article']['url'].'/admin/actlog.php?act=tip', 'target' => 0, 'publish' => 1);
/*
$jieqiAdminmenu['obook'][] = array('layer' => 0, 'caption' => '结算记录', 'command'=>$GLOBALS['jieqiModules']['obook']['url'].'/admin/paidlog.php', 'target' => 0, 'publish' => 1);
*/
?>