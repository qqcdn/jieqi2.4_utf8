<?php
$jieqiLang['system']['tip']=1; //表示本语言包已经包含

$jieqiLang['system']['users_not_exists']='对不起，该用户不存在！';
$jieqiLang['system']['cant_tip_myself']='对不起，您不能打赏给自己！';
$jieqiLang['system']['need_user_login']='对不起，请先登陆！';
$jieqiLang['system']['user_not_exists']='对不清，您的用户信息不存在！';
$jieqiLang['system']['user_no_emoney']='对不起，您没有'.JIEQI_EGOLD_NAME.'，请先充值！<br /><br /><a href="'.$jieqiModules['pay']['url'].'/buyegold.php">点击这里进行充值</a>';
$jieqiLang['system']['payegold_over_zero']='您打赏的'.JIEQI_EGOLD_NAME.'必须是大于0的整数！';
$jieqiLang['system']['payegold_over_min']='您打赏的'.JIEQI_EGOLD_NAME.'至少是 %s 以上的整数！';
$jieqiLang['system']['payegold_over_emoney']='你打赏的'.JIEQI_EGOLD_NAME.'不能超过所拥有的！';
$jieqiLang['system']['database_save_error']='数据库保存失败，请与管理员联系！';
$jieqiLang['system']['tip_save_success']='打赏成功，感谢您对该用户的支持！';
$jieqiLang['system']['tip_message_title']='%s给您打赏了%s'.JIEQI_EGOLD_NAME.'';
$jieqiLang['system']['tip_message_content']='打赏信息通知：
打赏人：[ID:%s]%s
打赏作品：[ID:%s]%s
打赏'.JIEQI_EGOLD_NAME.'：%s';
$jieqiLang['system']['user_payout_failure']='扣除'.JIEQI_EGOLD_NAME.'失败，可能余额不足或者暂时无法使用本功能！';
?>