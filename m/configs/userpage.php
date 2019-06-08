<?php 

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'会员信息', 'module'=>'system', 'filename'=>'block_uinfo', 'classname'=>'BlockSystemUinfo', 'side'=>0, 'title'=>'会员信息', 'vars'=>'$uid', 'template'=>'', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'会员好友', 'module'=>'system', 'filename'=>'block_ufriends', 'classname'=>'BlockSystemUfriends', 'side'=>0, 'title'=>'会员好友', 'vars'=>'friendsid,10,0,$uid', 'template'=>'', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'相关作品', 'module'=>'article', 'filename'=>'block_uarticles', 'classname'=>'BlockArticleUarticles', 'side'=>4, 'title'=>'相关作品', 'vars'=>'lastupdate,10,0,$uid', 'template'=>'', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

$jieqiBlocks[]=array('bid'=>0, 'blockname'=>'会员书架', 'module'=>'article', 'filename'=>'block_ubookcase', 'classname'=>'BlockArticleUbookcase', 'side'=>4, 'title'=>'会员书架', 'vars'=>'lastupdate,10,0,$uid', 'template'=>'', 'contenttype'=>4, 'custom'=>0, 'publish'=>3, 'hasvars'=>1);

?>