<?php
//1.人工创建的区块配置文件，每个区块配置的写法可以参考后台区块管理里面的默认写法，再根据实际需要修改参数。
//2.不自动显示区块，而是模板指定调用时候，建议所有区块的 side 参数值设置成 -1
//3.$jieqiBlocks[ 和 ]之间的数字可以自己定义，不同区块别重复。比如$jieqiBlocks[12]，模板里面调用这个区块时候，区块标题用： {?$jieqi_pageblocks['12']['title']?} ，区块内容用  {?$jieqi_pageblocks['12']['content']?} 

$jieqiBlocks[1]=array('bid'=>0, 'blockname'=>'用户登录', 'module'=>'system', 'filename'=>'block_login', 'classname'=>'BlockSystemLogin', 'side'=>-1, 'title'=>'用户登录', 'vars'=>'', 'template'=>'', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>0);

$jieqiBlocks[2]=array('bid'=>1, 'blockname'=>'本站公告', 'module'=>'system', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>-1, 'title'=>'本站公告', 'vars'=>'', 'template'=>'', 'contenttype'=>1, 'custom'=>1, 'publish'=>3, 'hasvars'=>0);

$jieqiBlocks[3]=array('bid'=>0, 'blockname'=>'小说搜索', 'module'=>'article', 'filename'=>'block_search', 'classname'=>'BlockArticleSearch', 'side'=>-1, 'title'=>'小说搜索', 'vars'=>'', 'template'=>'', 'contenttype'=>0, 'custom'=>0, 'publish'=>3, 'hasvars'=>0);

$jieqiBlocks[4]=array('bid'=>0, 'blockname'=>'点击排行', 'module'=>'article', 'filename'=>'block_articlelist', 'classname'=>'BlockArticleArticlelist', 'side'=>0, 'title'=>'点击排行', 'vars'=>'allvisit,10,0,0,0,0', 'template'=>'block_articlelist.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[5]=array('bid'=>0, 'blockname'=>'最近更新', 'module'=>'article', 'filename'=>'block_articlelist', 'classname'=>'BlockArticleArticlelist', 'side'=>5, 'title'=>'最近更新', 'vars'=>'lastupdate,10,0,0,0,0', 'template'=>'block_lastupdate.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[6]=array('bid'=>0, 'blockname'=>'推荐排行', 'module'=>'article', 'filename'=>'block_articlelist', 'classname'=>'BlockArticleArticlelist', 'side'=>0, 'title'=>'推荐排行', 'vars'=>'allvote,10,0,0,0,0', 'template'=>'block_articlelist.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

?>