<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
include_once JIEQI_ROOT_PATH . '/header.php';
jieqi_getconfigs('article', 'sort');
$jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/sortselect.html';
include_once JIEQI_ROOT_PATH . '/footer.php';