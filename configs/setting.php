<?php
//杰奇服务相关参数设置
$jieqiSetting = array();
$jieqiSetting['siteid'] = '0'; //本站在杰奇的ID
$jieqiSetting['siteip'] = '127.0.0.1'; //本站服务器IP
$jieqiSetting['getkey'] = '000000'; //本站和杰奇服务的通讯密钥
//杰奇的小说分类名称和本站的分类ID对应关系（default 表示对应不上时候的默认分类）
$jieqiSetting['articlesort'] = array('玄幻魔法'=>1, '武侠修真'=>2, '都市言情'=>3, '历史军事'=>4, '穿越架空'=>5, '游戏竞技'=>6, '科幻灵异'=>7, '同人动漫'=>8, '社会文学'=>9, '综合其他'=>10, 'default'=>10) ;

//以下参数请使用默认值，不需修改
$jieqiSetting['apiserver'] = 'http://book.jieqi.com';  //杰奇API服务器网址
$jieqiSetting['payserver'] = 'http://book.jieqi.com/modules/pay';  //杰奇充值服务器网址
?>