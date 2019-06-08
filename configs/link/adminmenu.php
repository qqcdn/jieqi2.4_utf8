<?php
/**
 * 后台友情链接导航配置
 *
 * 后台友情链接导航配置
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    link
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

$jieqiAdminmenu['link'][] = array('layer' => 0, 'caption' => '友情链接管理', 'command'=>$GLOBALS['jieqiModules']['link']['url'].'/admin/link.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['link'][] = array('layer' => 0, 'caption' => '添加链接', 'command'=>$GLOBALS['jieqiModules']['link']['url'].'/admin/addlink.php', 'target' => 0, 'publish' => 1);


?>