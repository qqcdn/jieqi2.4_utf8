<?php
//排行榜类型配置
$jieqiTop['article']['allvisit'] = array('caption'=>'总点击榜', 'title'=>'总点击', 'sort'=>'allvisit', 'where'=>'', 'order'=>'allvisit DESC', 'default'=>1, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['monthvisit'] = array('caption'=>'月点击榜', 'title'=>'月点击', 'sort'=>'monthvisit', 'where'=>'lastvisit >= <{$monthstart}>', 'order'=>'monthvisit DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['weekvisit'] = array('caption'=>'周点击榜', 'title'=>'周点击', 'sort'=>'weekvisit', 'where'=>'lastvisit >= <{$weekstart}>', 'order'=>'weekvisit DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['dayvisit'] = array('caption'=>'日点击榜', 'title'=>'日点击', 'sort'=>'dayvisit', 'where'=>'lastvisit >= <{$daystart}>', 'order'=>'dayvisit DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);

$jieqiTop['article']['allvote'] = array('caption'=>'总推荐榜', 'title'=>'总推荐', 'sort'=>'allvote', 'where'=>'', 'order'=>'allvote DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['monthvote'] = array('caption'=>'月推荐榜', 'title'=>'月推荐', 'sort'=>'monthvote', 'where'=>'lastvote >= <{$monthstart}>', 'order'=>'monthvote DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['weekvote'] = array('caption'=>'周推荐榜', 'title'=>'周推荐', 'sort'=>'weekvote', 'where'=>'lastvote >= <{$weekstart}>', 'order'=>'weekvote DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['dayvote'] = array('caption'=>'日推荐榜', 'title'=>'日推荐', 'sort'=>'dayvote', 'where'=>'lastvote >= <{$daystart}>', 'order'=>'dayvote DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);

$jieqiTop['article']['allvipvote'] = array('caption'=>'总月票榜', 'title'=>'总月票', 'sort'=>'allvipvote', 'where'=>'', 'order'=>'allvipvote DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['monthvipvote'] = array('caption'=>'本月票榜', 'title'=>'本月票', 'sort'=>'monthvipvote', 'where'=>'lastvipvote >= <{$monthstart}>', 'order'=>'monthvipvote DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['previpvote'] = array('caption'=>'前月票榜', 'title'=>'前月票', 'sort'=>'previpvote', 'where'=>'lastvipvote >= <{$monthstart}>', 'order'=>'previpvote DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['weekvipvote'] = array('caption'=>'周月票榜', 'title'=>'周月票', 'sort'=>'weekvipvote', 'where'=>'lastvipvote >= <{$weekstart}>', 'order'=>'weekvipvote DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['dayvipvote'] = array('caption'=>'日月票榜', 'title'=>'日月票', 'sort'=>'dayvipvote', 'where'=>'lastvipvote >= <{$daystart}>', 'order'=>'dayvipvote DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);

$jieqiTop['article']['allflower'] = array('caption'=>'总鲜花榜', 'title'=>'总鲜花', 'sort'=>'allflower', 'where'=>'', 'order'=>'allflower DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['monthflower'] = array('caption'=>'月鲜花榜', 'title'=>'月鲜花', 'sort'=>'monthflower', 'where'=>'lastflower >= <{$monthstart}>', 'order'=>'monthflower DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['weekflower'] = array('caption'=>'周鲜花榜', 'title'=>'周鲜花', 'sort'=>'weekflower', 'where'=>'lastflower >= <{$weekstart}>', 'order'=>'weekflower DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['dayflower'] = array('caption'=>'日鲜花榜', 'title'=>'日鲜花', 'sort'=>'dayflower', 'where'=>'lastflower >= <{$daystart}>', 'order'=>'dayflower DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);

$jieqiTop['article']['allegg'] = array('caption'=>'总鸡蛋榜', 'title'=>'总鸡蛋', 'sort'=>'allegg', 'where'=>'', 'order'=>'allegg DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['monthegg'] = array('caption'=>'月鸡蛋榜', 'title'=>'月鸡蛋', 'sort'=>'monthegg', 'where'=>'lastegg >= <{$monthstart}>', 'order'=>'monthegg DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['weekegg'] = array('caption'=>'周鸡蛋榜', 'title'=>'周鸡蛋', 'sort'=>'weekegg', 'where'=>'lastegg >= <{$weekstart}>', 'order'=>'weekegg DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['dayegg'] = array('caption'=>'日鸡蛋榜', 'title'=>'日鸡蛋', 'sort'=>'dayegg', 'where'=>'lastegg >= <{$daystart}>', 'order'=>'dayegg DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);

$jieqiTop['article']['allsale'] = array('caption'=>'总销售榜', 'title'=>'总销售', 'sort'=>'allsale', 'where'=>'', 'order'=>'allsale DESC', 'default'=>0, 'publish' => 0, 'isvip' => 1);
$jieqiTop['article']['monthsale'] = array('caption'=>'月销售榜', 'title'=>'月销售', 'sort'=>'monthsale', 'where'=>'lastsale >= <{$monthstart}>', 'order'=>'monthsale DESC', 'default'=>0, 'publish' => 0, 'isvip' => 1);
$jieqiTop['article']['weeksale'] = array('caption'=>'周销售榜', 'title'=>'周销售', 'sort'=>'weeksale', 'where'=>'lastsale >= <{$weekstart}>', 'order'=>'weeksale DESC', 'default'=>0, 'publish' => 0, 'isvip' => 1);
$jieqiTop['article']['daysale'] = array('caption'=>'日销售榜', 'title'=>'日销售', 'sort'=>'daysale', 'where'=>'lastsale >= <{$daystart}>', 'order'=>'daysale DESC', 'default'=>0, 'publish' => 0, 'isvip' => 1);

$jieqiTop['article']['monthwords'] = array('caption'=>'月勤更榜', 'title'=>'月更字数', 'sort'=>'monthwords', 'where'=>'lastupdate >= <{$monthstart}>', 'order'=>'monthwords DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['weekwords'] = array('caption'=>'周勤更榜', 'title'=>'周更字数', 'sort'=>'weekwords', 'where'=>'lastupdate >= <{$weekstart}>', 'order'=>'weekwords DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);
$jieqiTop['article']['daywords'] = array('caption'=>'日勤更榜', 'title'=>'日更字数', 'sort'=>'daywords', 'where'=>'lastupdate >= <{$daystart}>', 'order'=>'daywords DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);

$jieqiTop['article']['lastupdate'] = array('caption'=>'最近更新', 'title'=>'更新时间', 'sort'=>'lastupdate', 'where'=>'', 'order'=>'lastupdate DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['postdate'] = array('caption'=>'最新入库', 'title'=>'入库时间', 'sort'=>'postdate', 'where'=>'', 'order'=>'postdate DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['signtime'] = array('caption'=>'最新上架', 'title'=>'签约时间', 'sort'=>'signtime', 'where'=>'vipid > 0', 'order'=>'signtime DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['goodnum'] = array('caption'=>'收藏榜', 'title'=>'收藏数', 'sort'=>'goodnum', 'where'=>'', 'order'=>'goodnum DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['words'] = array('caption'=>'字数榜', 'title'=>'总字数', 'sort'=>'words', 'where'=>'', 'order'=>'words DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);
$jieqiTop['article']['toptime'] = array('caption'=>'编辑推荐', 'title'=>'推荐时间', 'sort'=>'toptime', 'where'=>'', 'order'=>'toptime DESC', 'default'=>0, 'publish' => 0, 'isvip' => 0);

$jieqiTop['article']['newhot'] = array('caption'=>'新书榜', 'title'=>'点击数', 'sort'=>'allvisit', 'where'=>'postdate >= '.(time() - 2592000), 'order'=>'allvisit DESC', 'default'=>0, 'publish' => 1, 'isvip' => 0);

?>