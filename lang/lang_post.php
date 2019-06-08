<?php
/**
 * 语言包-用户提交变量检查提示
 *
 * 语言包-用户提交变量检查提示
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_post.php 344 2009-06-23 03:06:07Z juny $
 */

$jieqiLang['system']['post']=1; //表示本语言包已经包含

$jieqiLang['system']['post_need_title']='标题不能为空！';
$jieqiLang['system']['post_need_content']='内容不能为空！';
$jieqiLang['system']['post_deny_times']='对不起，以下时间段禁止发帖！<br />%s';
$jieqiLang['system']['post_time_limit']='对不起，两次发表的间隔时间不得少于 %s 秒';
$jieqiLang['system']['post_words_deny']='对不起，您发表的内容含有禁用的单词：<br />%s';
$jieqiLang['system']['post_words_water']='对不起，您发表的内容被怀疑为灌水。如有误判，尚请谅解！';
$jieqiLang['system']['post_min_content']='您发表的内容不能少于 %s 字节！';
$jieqiLang['system']['post_max_content']='您发表的内容不能多于 %s 字节！';
$jieqiLang['system']['post_min_experience']='对不起，经验值不足 %s 的会员禁止发帖！';
$jieqiLang['system']['post_email_verify']='对不起，个人资料中的email未通过验证的会员禁止发帖！';

$jieqiLang['system']['post_uptype_error']='%s不是允许上传的文件类型！';
$jieqiLang['system']['post_uptype_safe']='为了安全起见，系统默认禁止上传 *.%s 文件！';
$jieqiLang['system']['post_upsize_over']='%s文件大小超出系统限制的%dK！';

$jieqiLang['system']['post_topic_locked']='对不起，该主题已锁定！';

$jieqiLang['system']['post_checkcode_label']='验证码';
$jieqiLang['system']['post_checkcode_code']='<img id="p_imgccode" src="" style="cursor:pointer;vertical-align:middle;margin-left:3px;display:none;" onclick="this.src=\''.JIEQI_URL.'/checkcode.php?rand=\'+Math.random();" title="点击刷新验证码">';
$jieqiLang['system']['post_checkcode_error']='对不起，验证码错误，请返回重新输入';
$jieqiLang['system']['post_checkcode_ctitle']='点击显示验证码';
?>