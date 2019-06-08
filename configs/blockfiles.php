<?php
//区块配置文件的列表
//module-所属模块； filename-文件名，不带后缀； caption-配置文件名称； description-配置文件描述
$jieqiBlockfiles = array();

$jieqiBlockfiles[] = array('module'=>'system', 'filename'=>'indexblocks', 'caption'=>'网站首页', 'description'=>'');
$jieqiBlockfiles[] = array('module'=>'system', 'filename'=>'userblocks', 'caption'=>'用户面板', 'description'=>'');
$jieqiBlockfiles[] = array('module'=>'article', 'filename'=>'authorblocks', 'caption'=>'作家面板', 'description'=>'');
$jieqiBlockfiles[] = array('module'=>'article', 'filename'=>'sortblocks', 'caption'=>'小说分类列表', 'description'=>'');
$jieqiBlockfiles[] = array('module'=>'article', 'filename'=>'topblocks', 'caption'=>'小说排行列表', 'description'=>'');
?>