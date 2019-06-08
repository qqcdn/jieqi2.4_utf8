<?php 
/**
 * 后台论坛管理导航配置
 *
 * 后台论坛管理导航配置
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    forum
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

$jieqiAdminmenu['forum'][] = array('layer' => 0, 'caption' => '参数设置', 'command'=>JIEQI_URL.'/admin/configs.php?mod=forum', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['forum'][] = array('layer' => 0, 'caption' => '权限管理', 'command'=>JIEQI_URL.'/admin/power.php?mod=forum', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['forum'][] = array('layer' => 0, 'caption' => '论坛管理', 'command'=>$GLOBALS['jieqiModules']['forum']['url'].'/admin/forumlist.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['forum'][] = array('layer' => 0, 'caption' => '论坛主题', 'command'=>$GLOBALS['jieqiModules']['forum']['url'].'/admin/topiclist.php?display=0', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['forum'][] = array('layer' => 0, 'caption' => '论坛帖子', 'command'=>$GLOBALS['jieqiModules']['forum']['url'].'/admin/postlist.php?display=0', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['forum'][] = array('layer' => 0, 'caption' => '待审帖子', 'command'=>$GLOBALS['jieqiModules']['forum']['url'].'/admin/postlist.php?display=1', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['forum'][] = array('layer' => 0, 'caption' => '批量删帖', 'command'=>$GLOBALS['jieqiModules']['forum']['url'].'/admin/forumdels.php', 'target' => 0, 'publish' => 1);
?>