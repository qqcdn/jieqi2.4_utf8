<?php
/**
 * 语言包-申请作家
 *
 * 语言包-申请作家
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_applywriter.php 228 2008-11-27 06:44:31Z juny $
 */

$jieqiLang['article']['applywriter']=1; //表示本语言包已经包含
//applywriter.php
$jieqiLang['article']['has_been_writer']='您已经拥有发表小说的权利，系统将自动跳转到作家专区。<br /><br /><a href="/modules/article/myarticle.php">点击这里直接进入作家专区</a><script type="text/javascript">setTimeout(function(){document.location="/modules/article/myarticle.php";}, 2000);</script>';
$jieqiLang['article']['no_writer_group']='对不起，您申请的用户类型不存在！';
$jieqiLang['article']['no_writer_admin']='对不起，不允许升级为管理员！';
$jieqiLang['article']['apply_writer_success']='恭喜您，您已成功申请为%s！';
$jieqiLang['article']['apply_submit_success']='申请提交完成，我们会尽快审核并给予站内短信回复，感谢您的支持！';
$jieqiLang['article']['apply_status_ready']='待审核';
$jieqiLang['article']['apply_status_success']='已审核';
$jieqiLang['article']['apply_status_failure']='未通过';
$jieqiLang['article']['apply_not_exists']='对不起，该申请记录不存在！';
$jieqiLang['article']['apply_no_contact_title']='请先设置联系方式';
$jieqiLang['article']['apply_no_contact']='您尚未设置联系方式，请完成设置后继续申请！';
$jieqiLang['article']['apply_already_post']='对不起，您已经提交过申请，请等待管理员审核！<br />审核结果将通过站内消息通知您，如长时间未收到回复，也可以通过站内消息联系管理员。<br /><a class="hot" href="/newmessage.php?tosys=1">点击这里联系管理员</a>';

$jieqiLang['article']['apply_confirm_title']='恭喜您，申请作者已经通过！';
$jieqiLang['article']['apply_confirm_text']='您的申请作者请求已经通过，重新登录后可以使用本站作者功能，感谢您的支持！';
$jieqiLang['article']['apply_refuse_title']='对不起，申请作者未于通过！';
$jieqiLang['article']['apply_refuse_text']='您提交的申请不符合本站要求，暂时未于通过，如有疑问，请联系本站管理员！';
$jieqiLang['article']['applywriter_not_exists']='对不起，申请记录不存在！';
?>