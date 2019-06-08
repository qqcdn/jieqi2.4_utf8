<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
if (empty($_POST['act']) || $_POST['act'] != 'delete') {
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
$canedit = jieqi_checkpower($jieqiPower['article']['delallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($article->getVar('authorid') == $tmpvar || $article->getVar('posterid') == $tmpvar || $article->getVar('agentid') == $tmpvar)) {
        $canedit = jieqi_checkpower($jieqiPower['article']['delmyarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['article']['noper_delete_article']);
}
jieqi_getconfigs('article', 'rule');
$actrule = true;
if (function_exists('jieqi_rule_article_articledelete')) {
    $actrule = jieqi_rule_article_articledelete($article);
    if ($actrule === false) {
        jieqi_printfail($jieqiLang['article']['deny_delete_article']);
    }
}
include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
jieqi_article_delete($_REQUEST['id']);
if (0 < $jieqiConfigs['article']['fakestatic']) {
    jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
    include_once $jieqiModules['article']['path'] . '/include/funstatic.php';
    article_update_static('articledel', $_REQUEST['id'], $article->getVar('sortid', 'n'));
}
jieqi_jumppage($article_static_url . '/masterpage.php', LANG_DO_SUCCESS, $jieqiLang['article']['article_delete_success']);