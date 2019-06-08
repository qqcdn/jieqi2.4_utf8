<?php
//充值记录导出字段参数 caption=标题， width=列宽度， display=1 显示，0 不显示
$jieqiExport['pay']['payid'] = array('caption'=>'序号', 'width'=>10, 'display'=>1);
$jieqiExport['pay']['buytime'] = array('caption'=>'交易时间', 'width'=>20, 'display'=>1);
$jieqiExport['pay']['buyname'] = array('caption'=>'用户名', 'width'=>20, 'display'=>0);
if(!empty($GLOBALS['jieqiChannels'])) $jieqiExport['pay']['channel'] = array('caption'=>'用户渠道', 'width'=>20, 'display'=>0);
$jieqiExport['pay']['egold'] = array('caption'=>'购买点数', 'width'=>10, 'display'=>1);
$jieqiExport['pay']['money'] = array('caption'=>'支付金额', 'width'=>10, 'display'=>1);
$jieqiExport['pay']['paytype'] = array('caption'=>'支付方式', 'width'=>10, 'display'=>1);
$jieqiExport['pay']['payflag'] = array('caption'=>'交易状态', 'width'=>10, 'display'=>1);

?>