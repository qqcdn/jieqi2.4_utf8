<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/recentread.html';
include_once JIEQI_ROOT_PATH . '/footer.php';