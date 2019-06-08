<?php 
/**
 * 后台充值管理导航配置
 *
 * 后台充值管理导航配置
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    pay
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

$jieqiAdminmenu['pay'][] = array('layer' => 0, 'caption' => '全部充值记录', 'command'=>$GLOBALS['jieqiModules']['pay']['url'].'/admin/paylog.php', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['pay'][] = array('layer' => 0, 'caption' => '已成功充值', 'command'=>$GLOBALS['jieqiModules']['pay']['url'].'/admin/paylog.php?payflag=success', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['pay'][] = array('layer' => 0, 'caption' => '未成功充值', 'command'=>$GLOBALS['jieqiModules']['pay']['url'].'/admin/paylog.php?payflag=failure', 'target' => 0, 'publish' => 1);

$jieqiAdminmenu['pay'][] = array('layer' => 0, 'caption' => '充值统计', 'command'=>$GLOBALS['jieqiModules']['pay']['url'].'/admin/paystat.php', 'target' => 0, 'publish' => 1);

?>