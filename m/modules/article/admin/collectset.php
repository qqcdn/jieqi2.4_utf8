<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['adminconfig'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'collectsite');
$updateconfig = false;
if (isset($_POST['act']) && $_POST['act'] == 'del' && !empty($_REQUEST['config']) && preg_match('/^\\w+$/', $_REQUEST['config'])) {
    jieqi_checkpost();
    foreach ($jieqiCollectsite as $k => $v) {
        if ($v['config'] == $_REQUEST['config']) {
            unset($jieqiCollectsite[$k]);
            $updateconfig = true;
            break;
        }
    }
    jieqi_jumppage($jieqiModules['article']['url'] . '/admin/collectset.php', '', '', true);
}
include_once JIEQI_ROOT_PATH . '/admin/header.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$jieqiTpl->assign_by_ref('siterows', $jieqiCollectsite);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/collectset.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';
if ($updateconfig) {
    jieqi_setconfigs('collectsite', 'jieqiCollectsite', $jieqiCollectsite, JIEQI_MODULE_NAME);
    if (file_exists(JIEQI_ROOT_PATH . '/configs/article/site_' . $_REQUEST['config'] . '.php')) {
        jieqi_delfile(JIEQI_ROOT_PATH . '/configs/article/site_' . $_REQUEST['config'] . '.php');
    }
}