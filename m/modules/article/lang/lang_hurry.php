<?php

$jieqiLang['article']['hurry'] = 1;
$jieqiLang['article']['article_not_exists'] = '对不起，该小说不存在！';
$jieqiLang['article']['need_user_login'] = '对不起，请先登陆！';
$jieqiLang['article']['user_not_exists'] = '对不起，您的用户信息不存在！';
$jieqiLang['article']['user_no_emoney'] = '对不起，您没有' . JIEQI_EGOLD_NAME . '，请先充值！<br /><br /><a href="' . $jieqiModules['pay']['url'] . '/buyegold.php">点击这里进行充值</a>';
$jieqiLang['article']['payegold_over_zero'] = '您支付的' . JIEQI_EGOLD_NAME . '必须是大于0的整数！';
$jieqiLang['article']['payegold_over_min'] = '您支付的' . JIEQI_EGOLD_NAME . '至少是 %s 以上的整数！';
$jieqiLang['article']['payegold_over_max'] = '您支付的' . JIEQI_EGOLD_NAME . '不能超过 %s 以上！';
$jieqiLang['article']['payegold_over_emoney'] = '你支付的' . JIEQI_EGOLD_NAME . '不能超过所拥有的！';
$jieqiLang['article']['minwords_over_zero'] = '要求更新字数必须是个大于0的整数！';
$jieqiLang['article']['overtime_over_now'] = '要求更新时间必须大于当前时间！';
$jieqiLang['article']['overtime_over_minhour'] = '要求更新时间至少超过当前时间的 %s 个小时以上！';
$jieqiLang['article']['database_save_error'] = '数据库保存失败，请与管理员联系！';
$jieqiLang['article']['hurry_save_success'] = '提交成功，系统会预先扣除您的' . JIEQI_EGOLD_NAME . '。<br />若作者作者更新没有达到您的要求，则自动退还。<br />感谢您对本书的支持！';
$jieqiLang['article']['hurry_message_title'] = '%s用%s催你更新《%s》';
$jieqiLang['article']['hurry_message_content'] = '催更信息通知：' . "\n" . '催更人：[ID:%s]%s' . "\n" . '催更电子书：[ID:%s]%s' . "\n" . '催更' . JIEQI_EGOLD_NAME . '：%s' . "\n" . '催更开始时间：%s' . "\n" . '催更结束时间：%s' . "\n" . '要求更新字数：%s字以上';
$jieqiLang['article']['user_payout_failure'] = '扣除' . JIEQI_EGOLD_NAME . '失败，可能余额不足或者暂时无法使用本功能！';