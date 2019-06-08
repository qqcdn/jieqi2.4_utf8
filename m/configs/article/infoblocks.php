<?php 
$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'关于作者', 'module'=>'system', 'filename'=>'block_uinfo', 'classname'=>'BlockSystemUinfo', 'side'=>1, 'title'=>'关于作者', 'vars'=>'$authorid', 'template'=>'block_uinfo.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'作者作品', 'module'=>'article', 'filename'=>'block_uarticles', 'classname'=>'BlockArticleUarticles', 'side'=>1, 'title'=>'作者作品', 'vars'=>'lastupdate,5,0,$authorid', 'template'=>'block_aarticles.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'粉丝排行榜', 'module'=>'article', 'filename'=>'block_credit', 'classname'=>'BlockArticleCredit', 'side'=>1, 'title'=>'粉丝排行榜', 'vars'=>'point,10,0,id', 'template'=>'block_credit.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'我的粉丝值', 'module'=>'article', 'filename'=>'block_mycredit', 'classname'=>'BlockArticleMycredit', 'side'=>1, 'title'=>'我的粉丝值', 'vars'=>'id', 'template'=>'block_mycredit.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'最新打赏', 'module'=>'article', 'filename'=>'block_actlog', 'classname'=>'BlockArticleActlog', 'side'=>1, 'title'=>'最新打赏', 'vars'=>'actlogid,10,0,id,tip', 'template'=>'block_actlog.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'同类推荐', 'module'=>'article', 'filename'=>'block_articlelist', 'classname'=>'BlockArticleArticlelist', 'side'=>1, 'title'=>'同类推荐', 'vars'=>'allvote,10,$sortid,0,0,0', 'template'=>'block_articlelist.html', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

?>