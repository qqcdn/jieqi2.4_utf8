<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_getconfigs('article', 'power');
$ret = jieqi_checkpower($jieqiPower['article']['authorpanel'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$ret) {
    jieqi_jumppage($jieqiModules['article']['url'] . '/applywriter.php', '', '', true);
}
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTpl->assign('authorarea', 1);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/myarticle.html';
include_once JIEQI_ROOT_PATH . '/footer.php';