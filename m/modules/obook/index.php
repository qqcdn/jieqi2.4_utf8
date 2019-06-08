<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'indexblocks', 'jieqiBlocks');
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTset['jieqi_contents_template'] = '';
include_once JIEQI_ROOT_PATH . '/footer.php';