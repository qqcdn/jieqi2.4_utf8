<?php
/**
 * 语言包-通用充值
 *
 * 语言包-通用充值
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    pay
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_pay.php 343 2009-06-23 03:04:19Z juny $
 */

$jieqiLang['pay']['pay']=1; //表示本语言包已经包含

$jieqiLang['pay']['need_login']='对不起，必须注册成为本站会员并登陆才能使用本功能！<br /><br /><a class="btnlink b_note" href="'.JIEQI_URL.'/login.php">用户登录</a> &nbsp;&nbsp; <a class="btnlink b_note" href="/register.php">注册新用户</a>';
$jieqiLang['pay']['buy_type_error']='对不起，您选择购买的类型不存在！';
$jieqiLang['pay']['money_over_zero']='对不起，充值金额必须大于零！';
$jieqiLang['pay']['money_over_min']='对不起，充值金额必须大于%s元！';
$jieqiLang['pay']['add_paylog_error']='数据库处理失败，请与管理员联系！';
$jieqiLang['pay']['need_buy_type']='对不起，请先选择您要购买的金额！';
$jieqiLang['pay']['no_buy_record']='对不起，无此交易记录！';
$jieqiLang['pay']['save_paylog_failure']='充值成功，保存交易记录失败！<br /><br />请检查您的帐号，如有问题请与管理员联系。';
$jieqiLang['pay']['return_checkcode_error']='对不起，信息校验错误，请与管理员联系！';
$jieqiLang['pay']['buy_egold_success']='交易成功，您选择购买<b>%s</b>已经入帐，感谢您对我们的支持！<br /><br /><a class="btnlink b_note" href="'.JIEQI_URL.'/userdetail.php">查看我的帐户</a>';
$jieqiLang['pay']['buy_already_success']='交易成功，您选择购买<b>%s</b>已经入帐，感谢您对我们的支持！<br /><br /><a class="btnlink b_note" href="/userdetail.php">查看我的帐户</a>';
$jieqiLang['pay']['buy_return_success']='交易成功，实际入账可能稍有延迟，感谢您对我们的支持！<br /><br /><a class="btnlink b_note" href="'.JIEQI_URL.'/userdetail.php">查看我的帐户</a>';
$jieqiLang['pay']['buy_return_jumpurl']='交易成功，实际入账可能稍有延迟，感谢您对我们的支持！<br /><br /><a class="btnlink b_note mb" href="%s">返回继续阅读</a><br /><a class="btnlink b_note" href="'.JIEQI_URL.'/userdetail.php">查看我的帐户</a>';
$jieqiLang['pay']['already_add_egold']='恭喜您，本次交易已经完成充值,请检查您的帐户余额！';
$jieqiLang['pay']['add_egold_success']='%s 购买 %s 成功';
$jieqiLang['pay']['add_egold_failure']='%s 购买 %s 失败';
$jieqiLang['pay']['state_unconfirm']='未确认';
$jieqiLang['pay']['state_paysuccess']='支付成功';
$jieqiLang['pay']['state_handconfirm']='手工确认';
$jieqiLang['pay']['state_unknow']='未知状态';
$jieqiLang['pay']['paytype_unknow']='未知方式';
$jieqiLang['pay']['hand_confirm_confirm']='确实要手工确认该订单么？';
$jieqiLang['pay']['hand_confirm']='手工处理';
$jieqiLang['pay']['delete_pay_confirm']='确实要删除么';
$jieqiLang['pay']['delete_pay']='删除';
$jieqiLang['pay']['customer_id_error']='对不起，商户编号对应不上，请与管理员联系！';
$jieqiLang['pay']['pay_return_error']='对不起，交易返回失败，可能余额不足或转帐过程出错！';
$jieqiLang['pay']['card_foreign']='外币卡';
$jieqiLang['pay']['card_local']='人民币卡';
$jieqiLang['pay']['pay_failure_message']='对不起，交易失败！<br>%s';
$jieqiLang['pay']['need_pay_type']='请选择支付类型';
$jieqiLang['pay']['need_card_nopwd']='请输入卡号和密码！';
$jieqiLang['pay']['paylog_clean_success']='恭喜您，过期的未成功充值记录已经清理完成！';
$jieqiLang['pay']['pay_request_error']='对不起交易请求失败，可能是提交的参数错误或者服务器没有及时响应！';
$jieqiLang['pay']['pay_subtype_title']='在线';

$jieqiLang['pay']['payaction_type_error']='对不起，您选择的购买的类型不存在！';
$jieqiLang['pay']['payaction_deny_group']='对不起，您所在的用户组不需要或者不允许购买本项目！';
$jieqiLang['pay']['payaction_expire_time']='对不起，您选择购买的项目截止日期为%s，目前已经下线！';
?>