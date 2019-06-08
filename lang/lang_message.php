<?php
/**
 * 语言包-站内短消息
 *
 * 语言包-站内短消息
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_message.php 324 2009-01-20 04:47:10Z juny $
 */

$jieqiLang['system']['message']=1; //表示本语言包已经包含
//meessage.php messagedetail.php newmessage.php
$jieqiLang['system']['message_send_box']='发件箱';
$jieqiLang['system']['message_receive_box']='收件箱';
$jieqiLang['system']['message_site_admin']='网站管理员';
$jieqiLang['system']['message_no_exists']='对不起，该消息不存在！';
$jieqiLang['system']['message_need_receiver']='接收人不能为空';
$jieqiLang['system']['message_need_title']='标题不能为空';
$jieqiLang['system']['message_nosend_self']='请不要发送给自己！';
$jieqiLang['system']['message_no_receiver']='对不起，接收人不存在！';
$jieqiLang['system']['message_send_failure']='发送失败，请与管理员联系！';
$jieqiLang['system']['message_send_seccess']='恭喜您，消息已经发送成功！';
$jieqiLang['system']['message_is_full']='信箱已满';
$jieqiLang['system']['message_box_full']='对不起，您的信箱已满！<br /><br />请删除部分过期的消息再使用本功能。';
$jieqiLang['system']['message_write_new']='写新消息';
$jieqiLang['system']['message_appay_writer']='申请成为作家';
$jieqiLang['system']['message_apply_reason']='请在下面输入申请理由：
';
$jieqiLang['system']['message_send_button']='发 送';
$jieqiLang['system']['day_message_limit']='对不起，您已经超出每天最多发送 %s 条消息的限制了！';
$jieqiLang['system']['message_score_label']='提示';
$jieqiLang['system']['message_need_score']='<span class="hot">您已经超出每天发送 %s 条消息的限制，如要继续发送，将消耗您的积分 %s 点</span>';
$jieqiLang['system']['low_sendmsg_score']='对不起，您的积分不足，不允许继续发送消息！';
$jieqiLang['system']['message_send_minscore']='对不起，您的积分必需在 %s 以上才能发送消息！';

//table field
$jieqiLang['system']['table_message_receiver']='收件人';
$jieqiLang['system']['table_message_sender']='发件人';
$jieqiLang['system']['table_message_title']='标题';
$jieqiLang['system']['table_message_content']='内容';
?>