<?php
//会员各种动作相关参数设置
//acttitle-动作名称 minscore-多少积分以上才能执行本动作  islog-是否记录日志  isvip-是否VIP动作
//ispay-是否有消费  paytitle-消费名称 paybase-消费基数值 paymin-最小消费值 paymax-最大消费值
// earnscore-获得多少个人积分 earncredit-获得多少贡献值

$jieqiAction['system']['register'] = array('acttitle'=>'会员注册', 'minscore'=>0, 'islog'=>1, 'isvip'=>0, 'ispay'=>0, 'paytitle'=>'', 'paybase'=>1, 'paymin'=>0, 'paymax'=>0, 'earnscore'=>10, 'earncredit'=>0, 'lenmin'=>3, 'lenmax'=>30, 'passmin'=>6, 'needemail'=>1);

$jieqiAction['system']['login'] = array('acttitle'=>'会员登录', 'minscore'=>0, 'islog'=>1, 'isvip'=>0, 'ispay'=>0, 'paytitle'=>'', 'paybase'=>1, 'paymin'=>0, 'paymax'=>0, 'earnscore'=>2, 'earncredit'=>0, 'descscore'=>'一天内只有第一次登陆获取积分');

$jieqiAction['system']['adclick'] = array('acttitle'=>'点击广告', 'minscore'=>0, 'islog'=>0, 'isvip'=>0, 'ispay'=>0, 'paytitle'=>'', 'paybase'=>1, 'paymin'=>0, 'paymax'=>5, 'earnscore'=>0, 'earncredit'=>0, 'descscore'=>'一天内最多计分 5 次');

$jieqiAction['system']['newmessage'] = array('acttitle'=>'发站内短信', 'minscore'=>0, 'islog'=>0, 'isvip'=>0, 'ispay'=>0, 'paytitle'=>'', 'paybase'=>1, 'paymin'=>0, 'paymax'=>0, 'earnscore'=>0, 'earncredit'=>0);

$jieqiAction['system']['ptopic'] = array('acttitle'=>'会客室发帖', 'minscore'=>0, 'islog'=>0, 'isvip'=>0, 'ispay'=>0, 'paytitle'=>'', 'paybase'=>1, 'paymin'=>0, 'paymax'=>0, 'earnscore'=>0, 'earncredit'=>0);

//$jieqiAction['system']['tip'] = array('acttitle'=>'打赏会员', 'minscore'=>0, 'islog'=>1, 'isvip'=>1, 'ispay'=>1, 'paytitle'=>JIEQI_EGOLD_NAME, 'paybase'=>1, 'paymin'=>20, 'paymax'=>0, 'earnscore'=>0, 'earncredit'=>0, 'ismessage'=>1);

?>