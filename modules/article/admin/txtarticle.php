<?php

define('JIEQI_MODULE_NAME', 'article');
define('JIEQI_USE_GZIP', '0');
define('JIEQI_NOCONVERT_CHAR', '1');
@set_time_limit(600);
@ini_set('memory_limit', '128M');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
include_once JIEQI_ROOT_PATH . '/admin/header.php';
include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
$articlevals = jieqi_article_vars($article, true, 'n');
$jieqiTpl->assign_by_ref('articlevals', $articlevals);
foreach ($articlevals as $k => $v) {
    $jieqiTpl->assign($k, $articlevals[$k]);
}
include_once $jieqiModules['article']['path'] . '/class/package.php';
include_once $jieqiModules['article']['path'] . '/include/funchapter.php';
include_once $jieqiModules['article']['path'] . '/class/chapter.php';
$chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
$criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['id'], '='));
if (isset($_REQUEST['isvip'])) {
    if (!empty($_REQUEST['isvip'])) {
        $criteria->add(new Criteria('isvip', intval($_REQUEST['isvip']), '='));
    } else {
        $criteria->add(new Criteria('isvip', 0, '='));
    }
}
$criteria->setSort('chapterorder');
$criteria->setOrder('ASC');
$chapter_handler->queryObjects($criteria);
$chapterrows = array();
$k = 0;
while ($chapter = $chapter_handler->getObject()) {
    $chapterrows[$k] = jieqi_article_chaptervars($chapter, 'n');
    if ($chapter->getVar('chaptertype', 'n') == 0) {
        $chapterrows[$k]['chaptercontent'] = jieqi_get_achapterc(array('articleid' => $article->getVar('articleid', 'n'), 'articlecode' => $article->getVar('articlecode', 'n'), 'chapterid' => $chapter->getVar('chapterid', 'n'), 'isvip' => $chapter->getVar('isvip', 'n'), 'chaptertype' => $chapter->getVar('chaptertype', 'n'), 'display' => 0, 'getformat' => 'txt'));
    } else {
        $chapterrows[$k]['chaptercontent'] = '';
    }
    $k++;
}
$jieqiTpl->assign_by_ref('chapterrows', $chapterrows);
header('Content-type: text/plain');
header('Accept-Ranges: bytes');
if ($_REQUEST['fname'] == 'id') {
    header('Content-Disposition: attachment; filename=' . $_REQUEST['id'] . '.txt');
} else {
    header('Content-Disposition: attachment; filename=' . jieqi_headstr($article->getVar('articlename')) . '.txt');
}
$jieqiTpl->setCaching(0);
$jieqiTpl->display($jieqiModules['article']['path'] . '/templates/admin/txtarticle.html');