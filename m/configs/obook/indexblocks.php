<?php 
//左边区块
$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'用户登录', 'module'=>'system', 'filename'=>'block_login', 'classname'=>'BlockSystemLogin', 'side'=>0, 'title'=>'用户登录', 'vars'=>'', 'template'=>'', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>0);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'分类阅读', 'module'=>'obook', 'filename'=>'block_sort', 'classname'=>'BlockObookSort', 'side'=>0, 'title'=>'分类阅读', 'vars'=>'', 'template'=>'', 'contenttype'=>0, 'custom'=>0, 'publish'=>3, 'hasvars'=>0);

//中间区块
$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'电子书推荐', 'module'=>'obook', 'filename'=>'block_obookcommend', 'classname'=>'BlockObookObookcommend', 'side'=>5, 'title'=>'电子书推荐', 'vars'=>'1|2|3|4|5|6|7|8', 'template'=>'block_obookcommend.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>2);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'最近更新', 'module'=>'obook', 'filename'=>'block_obooklist', 'classname'=>'BlockObookObooklist', 'side'=>5, 'title'=>'最近更新', 'vars'=>'lastupdate,15,0,0,0,0', 'template'=>'block_obooklist.html', 'contenttype'=>1, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

//右边区块
$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'电子书搜索', 'module'=>'obook', 'filename'=>'block_search', 'classname'=>'BlockObookSearch', 'side'=>1, 'title'=>'电子书搜索', 'vars'=>'', 'template'=>'', 'contenttype'=>0, 'custom'=>0, 'publish'=>3, 'hasvars'=>0);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'最新上架', 'module'=>'obook', 'filename'=>'block_search', 'classname'=>'BlockObookSearch', 'side'=>1, 'title'=>'最新上架', 'vars'=>'postdate,15,0,0,0,0', 'template'=>'block_toplist.html', 'contenttype'=>0, 'custom'=>0, 'publish'=>3, 'hasvars'=>0);

?>