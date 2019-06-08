<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
if (empty($_POST['act']) || $_POST['act'] != 'repack') {
    jieqi_printfail(LANG_ERROR_PARAMETER);
} else {
    jieqi_checkpost();
}
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$canedit = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($article->getVar('authorid') == $tmpvar || $article->getVar('posterid') == $tmpvar || $article->getVar('agentid') == $tmpvar)) {
        $canedit = true;
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['article']['noper_manage_article']);
}
@set_time_limit(3600);
@session_write_close();
if (!is_array($_REQUEST['packflag']) || count($_REQUEST['packflag']) < 1) {
    jieqi_printfail($jieqiLang['article']['need_repack_option']);
} else {
    jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
    $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
    $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
    include_once $jieqiModules['article']['path'] . '/include/repack.php';
    $ptypes = array();
    foreach ($_REQUEST['packflag'] as $v) {
        $ptypes[$v] = 1;
    }
    echo str_repeat(' ', 4096);
    echo $jieqiLang['article']['wait_to_execute'];
    ob_flush();
    flush();
    $ret = article_repack($_REQUEST['id'], $ptypes);
    jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . $_REQUEST['id'], LANG_DO_SUCCESS, $jieqiLang['article']['article_repack_success']);
}