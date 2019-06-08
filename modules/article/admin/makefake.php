<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/makefake.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';