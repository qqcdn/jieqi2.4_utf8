<?php
/**
 * 语言包-区块管理
 *
 * 语言包-区块管理
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_blocks.php 344 2009-06-23 03:06:07Z juny $
 */

$jieqiLang['system']['blocks']=1; //表示本语言包已经包含
//admin/blockedit.php blocks
$jieqiLang['system']['block_not_exists']='对不起，该区块不存在!';
$jieqiLang['system']['blockconfig_is_exists']='对不起，该区块配置文件已经存在!';
$jieqiLang['system']['blockconfig_add_failure']='对不起，添加区块配置文件时出错!';
$jieqiLang['system']['blockconfig_add_success']='恭喜您，已经成功添加一个区块配置文件!';
$jieqiLang['system']['add_custom_block']='增加自定义区块';
$jieqiLang['system']['edit_custom_block']='编辑自定义区块';
$jieqiLang['system']['edit_system_block']='编辑系统区块';
$jieqiLang['system']['note_block_weight']='序号大小表示排列顺序';
$jieqiLang['system']['block_show_no']='不显示';
$jieqiLang['system']['block_show_logout']='仅登录前显示';
$jieqiLang['system']['block_show_login']='仅登录后显示';
$jieqiLang['system']['block_show_both']='都显示';
$jieqiLang['system']['block_template_file']='模板文件名称';
$jieqiLang['system']['block_save_type']='保存类型';
$jieqiLang['system']['block_save_self']='保存为当前区块';
$jieqiLang['system']['block_save_another']='作为一个新区块保存';
$jieqiLang['system']['add_block']='增加区块';
$jieqiLang['system']['save_block']='保存区块';
$jieqiLang['system']['need_block_name']='区块名不能为空！';
$jieqiLang['system']['need_block_modname']='模块名不能为空！';
$jieqiLang['system']['need_block_content']='区块内容不能为空！';
$jieqiLang['system']['block_add_failure']='增加区块失败，请与管理员联系！';
$jieqiLang['system']['block_edit_success']='恭喜您，区块 %s 已经更新完成！';
$jieqiLang['system']['block_edit_failure']='更新区块失败，请与管理员联系！';
$jieqiLang['system']['block_delete_cofirm']='确实要删除该区块么？';
$jieqiLang['system']['block_action_edit']='编辑';
$jieqiLang['system']['block_action_delete']='删除';
$jieqiLang['system']['block_action_refresh']='刷新';
$jieqiLang['system']['block_less_one']='对不起，本类型区块至少保留一个！';
$jieqiLang['system']['block_empty_blockfile']='对不起，区块配置文件不存在或者内容为空！';
$jieqiLang['system']['block_modname_error']='模块名参数错误';
$jieqiLang['system']['block_template_errorformat']='模板文件名称错误，请不要指定路径，直接写文件名，如：template.html';

//manageblocks.php
$jieqiLang['system']['block_delete_success']='该区块已经成功删除,正返回模块列表页面';
$jieqiLang['system']['block_update_success']='该区块配置文件已经成功更新,正返回模块列表页面';
$jieqiLang['system']['block_add_success']='该区块已经成功添加,正返回模块列表页面';
$jieqiLang['system']['block_newconfig_success']='该模块配置文件新建成功,正返回模块列表页面';
$jieqiLang['system']['block_newconfig_failure']='该模块配置文件新建失败,请检查权限设置!';
$jieqiLang['system']['block_config_notexists']='对不起，该区块配置文件记录不存在!';
$jieqiLang['system']['block_onloading']='正在载入...';
$jieqiLang['system']['block_key_lowzero']='区块序号不能小于零！';
$jieqiLang['system']['block_key_notrepeat']='区块序号值 %s 重复了！';


$jieqiLang['system']['block_type0']='文本';
$jieqiLang['system']['block_type1']='html';
$jieqiLang['system']['block_type2']='js文件';
$jieqiLang['system']['block_type3']='html和script混合';
$jieqiLang['system']['block_type4']='php代码';

//table field
$jieqiLang['system']['table_blocks_blockname']='区块名称';
$jieqiLang['system']['table_blocks_modname']='模块名称';
$jieqiLang['system']['table_blocks_filename']='区块文件';
$jieqiLang['system']['table_blocks_side']='显示位置';
$jieqiLang['system']['table_blocks_weight']='排列序号';
$jieqiLang['system']['table_blocks_publish']='是否显示';
$jieqiLang['system']['table_blocks_title']='区块标题';
$jieqiLang['system']['table_blocks_contenttype']='内容类型';
$jieqiLang['system']['table_blocks_content']='区块内容';
$jieqiLang['system']['table_blocks_description']='区块描述';
$jieqiLang['system']['table_blocks_blockvars']='区块参数';
?>