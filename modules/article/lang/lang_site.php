<?php
/**
 * 语言包-内容提供商相关
 *
 * 语言包-内容提供商相关
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_site.php 228 2008-11-27 06:44:31Z juny $
 */

$jieqiLang['article']['site']=1; //表示本语言包已经包含

$jieqiLang['article']['site_no_siteid']='对不起，参数错误，您尚未选择需要采集的网站！';
$jieqiLang['article']['site_start_notice']='即将采集网站“%s”的小说，可能会需要比较长时间，请耐心等待！<br /><br />默认将打开新窗口运行，程序执行结束前请勿关闭，也不要同时打开多个窗口执行采集，否则可能造成数据错乱。<br /><br /><a href="%s" target="_blank">&gt;&gt;点击这里开始采集</a><br /><br />';
$jieqiLang['article']['site_articlelist_start']='开始采集小说更新列表！';
$jieqiLang['article']['site_log_notexists']='对不起，采集日志不存在！';
$jieqiLang['article']['site_cfile_notexists']='对不起，采集缓存文件不存在！';
$jieqiLang['article']['site_cfile_formaterror']='对不起，采集缓存文件格式错误！';
$jieqiLang['article']['site_onearticle_success']='恭喜您，指定小说采集完成!<br /><a href="%s" target="_blank">点击查看小说信息</a>';
$jieqiLang['article']['site_allarticle_success']='恭喜您，全部小说采集完成，本次共采集小说 %s 本!<script type="text/javascript"> setTimeout("if(navigator.userAgent.indexOf(\'Firefox\')==-1){window.opener=null;window.open(\'\',\'_self\');window.close();}else{var opened=window.open(\'about:blank\',\'_self\');opened.close();}", 10000); </script>';
$jieqiLang['article']['site_article_noupdate']='检查完成，没有需要更新的小说！<script type="text/javascript"> setTimeout("if(navigator.userAgent.indexOf(\'Firefox\')==-1){window.opener=null;window.open(\'\',\'_self\');window.close();}else{var opened=window.open(\'about:blank\',\'_self\');opened.close();}", 10000); </script>';
$jieqiLang['article']['site_cachefile_openfailed']='打开缓存文件 %s 失败，请检查改文件及目录是否可写！';
$jieqiLang['article']['site_article_updatenum']='共有 %s 本小说需要采集，稍后将逐本开始采集。';
$jieqiLang['article']['site_next_html']='<html><head><title>小说采集</title><meta http-equiv="Content-Type" content="text/html; charset=%s"></head><body><br /><br />&nbsp;&nbsp;开始采集小说，本次预计采集 %s 本，即将采集第 %s 本。请耐心等待......<br /><br /><a href="%s">如浏览器确实不支持转换，点击这里采集下一本。</a><script type="text/javascript">document.location="%s";</script></body></html>';
$jieqiLang['article']['site_maybe_doing']='前一次采集可能正在进行中，请不要同时执行多个采集程序。<br /><br />如果您确认上次采集已经结束，请等待3分钟后再继续尝试采集！<script type="text/javascript"> setTimeout("if(navigator.userAgent.indexOf(\'Firefox\')==-1){window.opener=null;window.open(\'\',\'_self\');window.close();}else{var opened=window.open(\'about:blank\',\'_self\');opened.close();}", 10000); </script>';
$jieqiLang['article']['site_allarticle_finish'] = '恭喜您，全部小说采集完成，系统将跳转到下一步指定网址。';

$jieqiLang['article']['site_article_notneed']='《%s》的本站内容和对方站相同，不需要采集。';
$jieqiLang['article']['site_article_begin']='《%s》开始采集...';
$jieqiLang['article']['site_return_formaterror']='返回内容格式错误！';
$jieqiLang['article']['site_savearticle_failure']='创建或更新小说信息失败！';
$jieqiLang['article']['site_chaptercontent_nourl']='解析章节内容网址失败！';
$jieqiLang['article']['site_chaptercontent_failure']='获取章节内容失败！<br />章节名称：%s';
$jieqiLang['article']['site_chapteradd_failure']='保存章节信息到数据库失败！';

?>