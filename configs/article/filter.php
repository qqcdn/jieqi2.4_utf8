<?php
//过滤条件设置

//排序方式
$jieqiFilter['article']['order'] = array(
	'weekvisit' => array('caption'=>'周点击', 'sort'=>'weekvisit', 'order'=>'weekvisit DESC', 'limit'=>'lastvisit >= <{$weekstart}>', 'isvip' => 0),
	'monthvisit' => array('caption'=>'月点击', 'sort'=>'monthvisit', 'order'=>'monthvisit DESC', 'limit'=>'lastvisit >= <{$monthstart}>', 'isvip' => 0),
	'allvisit' => array('caption'=>'总点击', 'sort'=>'allvisit', 'order'=>'allvisit DESC', 'isvip' => 0),
	'weekvote' => array('caption'=>'周推荐', 'sort'=>'weekvote', 'order'=>'weekvote DESC', 'limit'=>'lastvote >= <{$weekstart}>', 'isvip' => 0),
	'monthvote' => array('caption'=>'月推荐', 'sort'=>'monthvote', 'order'=>'monthvote DESC', 'limit'=>'lastvote >= <{$monthstart}>', 'isvip' => 0),
	'allvote' => array('caption'=>'总推荐', 'sort'=>'allvote', 'order'=>'allvote DESC', 'isvip' => 0),
	'weekflower' => array('caption'=>'周鲜花', 'sort'=>'weekflower', 'order'=>'weekflower DESC', 'limit'=>'lastflower >= <{$weekstart}>', 'isvip' => 0),
	'monthflower' => array('caption'=>'月鲜花', 'sort'=>'monthflower', 'order'=>'monthflower DESC', 'limit'=>'lastflower >= <{$monthstart}>', 'isvip' => 0),
	'allflower' => array('caption'=>'总鲜花', 'sort'=>'allflower', 'order'=>'allflower DESC', 'isvip' => 0),
	//'weekvipvote' => array('caption'=>'周月票', 'sort'=>'weekvipvote', 'order'=>'weekvipvote DESC', 'limit'=>'lastvipvote >= <{$weekstart}>', 'isvip' => 0),
	//'monthvipvote' => array('caption'=>'本月票', 'sort'=>'monthvipvote', 'order'=>'monthvipvote DESC', 'limit'=>'lastvipvote >= <{$monthstart}>', 'isvip' => 0),
	'allvipvote' => array('caption'=>'总月票', 'sort'=>'allvipvote', 'order'=>'allvipvote DESC', 'isvip' => 0),
	//'weeksale' => array('caption'=>'周销售', 'sort'=>'weeksale', 'order'=>'weeksale DESC', 'limit'=>'lastsale >= <{$weekstart}>', 'isvip' => 1),
	//'monthsale' => array('caption'=>'月销售', 'sort'=>'monthsale', 'order'=>'monthsale DESC', 'limit'=>'lastsale >= <{$monthstart}>', 'isvip' => 1),
	//'allsale' => array('caption'=>'总销售', 'sort'=>'allsale', 'order'=>'allsale DESC', 'isvip' => 1),

	//'newhot' => array('caption'=>'新书榜', 'sort'=>'allvisit', 'order'=>'allvisit DESC', 'limit'=>'postdate >= '.(time() - 2592000), 'isvip' => 0),
	'words' => array('caption'=>'字数', 'sort'=>'words', 'order'=>'words DESC', 'isvip' => 0),
	'goodnum' => array('caption'=>'收藏数', 'sort'=>'goodnum', 'order'=>'goodnum DESC', 'isvip' => 0),
	'lastupdate' => array('caption'=>'更新时间', 'sort'=>'lastupdate', 'order'=>'lastupdate DESC', 'isvip' => 0),
	'postdate' => array('caption'=>'入库时间', 'sort'=>'postdate', 'order'=>'postdate DESC', 'isvip' => 0),
	'toptime' => array('caption'=>'编辑推荐', 'sort'=>'toptime', 'order'=>'toptime DESC', 'isvip' => 0)
);

//字数限制(注意：words 在数据库是字节数，是实际字数的2倍)
$jieqiFilter['article']['words'] = array(
	1 => array('caption'=>'30万以下', 'limit'=>'words < 300000'),
	2 => array('caption'=>'30-50万', 'limit'=>'words >= 300000 AND words < 500000'),
	3 => array('caption'=>'50-100万', 'limit'=>'words >= 500000 AND words < 1000000'),
	4 => array('caption'=>'100-200万', 'limit'=>'words >= 1000000 AND words < 2000000'),
	5 => array('caption'=>'200万以上', 'limit'=>'words >= 2000000')
);

//更新时间
$jieqiFilter['article']['update'] = array(
	1 => array('caption'=>'三日内', 'days'=>3),
	2 => array('caption'=>'七日内', 'days'=>7),
	3 => array('caption'=>'半月内', 'days'=>15),
	4 => array('caption'=>'一月内', 'days'=>30)
);

//所属频道
/*
$jieqiFilter['article']['rgroup'] = array(
	1 => array('caption'=>'男生', 'limit'=>'rgroup = 1'),
	2 => array('caption'=>'女生', 'limit'=>'rgroup = 2')
);
*/

//是否原创
$jieqiFilter['article']['original'] = array(
	1 => array('caption'=>'原创', 'limit'=>'authorid > 0'),
	2 => array('caption'=>'转载', 'limit'=>'authorid = 0'),
);

//写作进度
$jieqiFilter['article']['isfull'] = array(
	1 => array('caption'=>'新书上传', 'limit'=>'progress = 0'),
	2 => array('caption'=>'情节展开', 'limit'=>'progress = 1'),
	3 => array('caption'=>'精彩纷呈', 'limit'=>'progress = 2'),
	4 => array('caption'=>'接近尾声', 'limit'=>'progress = 3'),
	5 => array('caption'=>'已经完本', 'limit'=>'progress = 4')
);

//VIP选项
$jieqiFilter['article']['isvip'] = array(
	2 => array('caption'=>'免费作品', 'limit'=>'isvip = 0'),
	1 => array('caption'=>'VIP作品', 'limit'=>'isvip > 0'),
	3 => array('caption'=>'签约作品', 'limit'=>'issign > 0'),
	//4 => array('caption'=>'包月作品', 'limit'=>'isvip > 0 AND monthly > 0'),
	//5 => array('caption'=>'限时免费', 'limit'=>'isvip > 0 AND freeend >= ' . JIEQI_NOW_TIME . ' AND freestart <= ' . JIEQI_NOW_TIME),
);

?>