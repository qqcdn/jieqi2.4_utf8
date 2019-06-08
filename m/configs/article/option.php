<?php
/**
 * 数据表里面可选项和值的对应关系
 * multiple 0 单选 1 多选
 * default 默认值
 * items 选项列表
*/
//管理授权
$jieqiOption['article']['authorflag'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '授权给该作者', 0 => '暂时不予授权'));

//授权级别
$jieqiOption['article']['permission'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '独家作品', 0 => '授权作品'));

//首发状态
$jieqiOption['article']['firstflag'] = array('multiple' => 0, 'default' => 1, 'items' => array(1 => '本站首发', 0 => '他站首发'));

//连载状态
$jieqiOption['article']['fullflag'] = array('multiple' => 0, 'default' => 0, 'items' => array(0 => '连载', 1 => '全本'));

//写作进程
$jieqiOption['article']['progress'] = array('multiple' => 0, 'default' => 0, 'items' => array(0 => '新书上传', 1 => '情节展开', 2 => '精彩纷呈', 3 => '接近尾声', 4 => '已经完本'));

//是否VIP
$jieqiOption['article']['isvip'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => 'VIP', 0 => '免费'));

//显示状态
$jieqiOption['article']['display'] = array('multiple' => 0, 'default' => 0, 'items' => array(0 => '显示', 1 => '待审', 2=>'隐藏'));

//是否签约
$jieqiOption['article']['issign'] = array('multiple' => 0, 'default' => 0, 'items' => array(10 => 'VIP签约', 1 => '普通签约', 0 => '未签约'));

//是否买断
$jieqiOption['article']['buyout'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '已买断', 0 => '未买断'));

//是否包月
$jieqiOption['article']['monthly'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '包月', 0 => '非包月'));

//是否打折
$jieqiOption['article']['discount'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '打折', 0 => '普通'));

//是否精品
$jieqiOption['article']['quality'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '精品', 0 => '普通'));

//是否短篇
$jieqiOption['article']['isshort'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '短篇', 0 => '长篇'));

//是否参赛
$jieqiOption['article']['inmatch'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '参赛', 0 => '普通'));

//是否共享
$jieqiOption['article']['isshare'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '共享', 0 => '普通'));

//读者群体（所属频道）
//$jieqiOption['article']['rgroup'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '男生', 2 => '女生'));

//是否已出版
$jieqiOption['article']['ispub'] = array('multiple' => 0, 'default' => 0, 'items' => array(1 => '已出版', 0 => '未出版'));

?>