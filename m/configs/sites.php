<?php
//合作网站设置 
// siteid: CP站ID，默认小于1000的值(数组下标和siteid请使用相同值)
// custom: 0=预置，1=自定义
// vipprice: -1=按照本站规则计算vip价格, 0=按照cp输出的价格计算 
// vipremote: 0=cp的vip内容保存在本地，1=vip内容在远程（打赏、订阅要远程调用）
// typeset: 1=需要重新排版，0-不需要排版
// signmode: 1= $sign=md5($query . $key)， 2= $sign=md5($query . '&key=' . $key), 3= $sign=$key
// interface: 同步接口类型，默认 jieqi,留空表示不同步
$jieqiSites = array();

?>