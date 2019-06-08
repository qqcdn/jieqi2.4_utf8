<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
if (empty($_POST['act']) || $_POST['act'] != 'clean') {
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
    jieqi_printfail($jieqiLang['article']['noper_clean_article']);
}
jieqi_getconfigs('article', 'rule');
$actrule = true;
if (function_exists('jieqi_rule_article_articledelete')) {
    $actrule = jieqi_rule_article_articledelete($article);
    if ($actrule === false) {
        jieqi_printfail($jieqiLang['article']['noper_clean_article']);
    }
}
include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
jieqi_article_clean($_REQUEST['id']);
if (!empty($_REQUEST['collecturl'])) {
    jieqi_jumppage($_REQUEST['collecturl'], LANG_DO_SUCCESS, $jieqiLang['article']['article_clean_collect']);
} else {
    jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . $_REQUEST['id'], LANG_DO_SUCCESS, $jieqiLang['article']['article_clean_success']);
}