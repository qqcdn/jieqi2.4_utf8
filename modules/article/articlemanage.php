<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$ismanager = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$canedit = $ismanager;
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($article->getVar('authorid') == $tmpvar || $article->getVar('posterid') == $tmpvar || $article->getVar('agentid') == $tmpvar)) {
        $canedit = true;
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['article']['noper_manage_article']);
}
$candelete = $ismanager;
if (!$candelete && $canedit) {
    $candelete = jieqi_checkpower($jieqiPower['article']['delmyarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once JIEQI_ROOT_PATH . '/header.php';
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$setting = jieqi_unserialize($article->getVar('setting', 'n'));
$url_collect = $article_static_url . '/admin/collect.php?toid=' . $article->getVar('articleid', 'n');
if (is_numeric($setting['fromarticle'])) {
    $url_collect .= '&fromid=' . $setting['fromarticle'];
}
if (is_numeric($setting['fromsite'])) {
    $url_collect .= '&siteid=' . $setting['fromsite'];
}
$jieqiTpl->assign('url_collect', $url_collect);
include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
$articlevals = jieqi_article_vars($article);
$jieqiTpl->assign_by_ref('articlevals', $articlevals);
foreach ($articlevals as $k => $v) {
    $jieqiTpl->assign($k, $articlevals[$k]);
}
if (!is_numeric($jieqiConfigs['article']['articlevote'])) {
    $jieqiConfigs['article']['articlevote'] = 0;
} else {
    $jieqiConfigs['article']['articlevote'] = intval($jieqiConfigs['article']['articlevote']);
}
$jieqiTpl->assign('articlevote', $jieqiConfigs['article']['articlevote']);
include_once $jieqiModules['article']['path'] . '/include/funchapter.php';
include_once $jieqiModules['article']['path'] . '/class/chapter.php';
$chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
$criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['id'], '='));
$criteria->setSort('chapterorder');
$criteria->setOrder('ASC');
$chapter_handler->queryObjects($criteria);
$i = 0;
$chapterrows = array();
$k = 0;
while ($chapter = $chapter_handler->getObject()) {
    $chapterrows[$k] = jieqi_article_chaptervars($chapter);
    $o = $k + 1;
    if ($chapterrows[$k]['chapterorder'] != $o) {
        $chapterrows[$k]['chapterorder'] = $o;
        $chapter->setVar('chapterorder', $o);
        $chapter_handler->insert($chapter);
    }
    $k++;
}
if (intval($article->getVar('chapters', 'n')) != $k) {
    $article->setVar('chapters', $k);
    $article_handler->insert($article);
}
$jieqiTpl->assign_by_ref('chapterrows', $chapterrows);
$jieqiTpl->assign('url_chaptersort', $article_static_url . '/chaptersort.php?do=submit');
$jieqiTpl->assign('url_chaptersdel', $article_static_url . '/chaptersdel.php?do=submit');
$jieqiTpl->assign('url_repack', $article_static_url . '/repack.php?do=submit');
$packflag = array();
if (!empty($jieqiConfigs['article']['makeopf'])) {
    $packflag[] = array('value' => 'makeopf', 'title' => $jieqiLang['article']['repack_opf']);
}
if (!empty($jieqiConfigs['article']['makehtml'])) {
    $packflag[] = array('value' => 'makehtml', 'title' => $jieqiLang['article']['repack_html']);
}
if (!empty($jieqiConfigs['article']['maketxtjs'])) {
    $packflag[] = array('value' => 'maketxtjs', 'title' => $jieqiLang['article']['repack_txtjs']);
}
if (!empty($jieqiConfigs['article']['makezip'])) {
    $packflag[] = array('value' => 'makezip', 'title' => $jieqiLang['article']['repack_zip']);
}
if (!empty($jieqiConfigs['article']['makefull'])) {
    $packflag[] = array('value' => 'makefull', 'title' => $jieqiLang['article']['repack_fullpage']);
}
if (!empty($jieqiConfigs['article']['maketxtfull'])) {
    $packflag[] = array('value' => 'maketxtfull', 'title' => $jieqiLang['article']['repack_txtfullpage']);
}
if (!empty($jieqiConfigs['article']['makeumd'])) {
    $packflag[] = array('value' => 'makeumd', 'title' => $jieqiLang['article']['repack_umdpage']);
}
if (!empty($jieqiConfigs['article']['makejar'])) {
    $packflag[] = array('value' => 'makejar', 'title' => $jieqiLang['article']['repack_jarpage']);
}
$jieqiTpl->assign_by_ref('packflag', $packflag);
$jieqiTpl->assign('authorarea', 1);
$jieqiTpl->assign('ismanager', intval($ismanager));
$jieqiTpl->assign('candelete', intval($candelete));
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/articlemanage.html';
include_once JIEQI_ROOT_PATH . '/footer.php';