<?php
/**
 * 语言包-静态首页生成
 *
 * 语言包-静态首页生成
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_index.php 344 2009-06-23 03:06:07Z juny $
 */

$jieqiLang['system']['index']=1; //表示本语言包已经包含

$jieqiLang['system']['make_sindex_success']='恭喜您，静态首页已经重新生成！';
$jieqiLang['system']['make_sindex_failure']='对不起，静态首页生成失败，请检查 %s 是否有可写的权限！';
$jieqiLang['system']['sindex_need_cache']='系统未启用缓存，不支持生成静态首页！';
$jieqiLang['system']['sindex_need_charset']='当前字符集为 %s ,不支持生成静态首页！';
$jieqiLang['system']['sindex_confirm_notice']='生成静态首页(index.html)可以减少系统负载，但是不会自动更新，建议在系统负载较高时才使用本功能。 <br /><br /><a href="%s">点击这里开始生成静态首页</a>';
$jieqiLang['system']['make_static_success']='恭喜您，静态页面已经重新生成！<br /><br /><a href="%s">点击这里查看</a>';
$jieqiLang['system']['make_static_failure']='对不起，静态页面生成失败，请检查 %s 是否有可写的权限！';
$jieqiLang['system']['get_content_failure']='对不起，获取首页内容失败，可能服务器禁止PHP获取URL内容！';
?>