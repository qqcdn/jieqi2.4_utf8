<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['aid'] = intval($_REQUEST['aid']);
if (empty($_POST['act']) || $_POST['act'] != 'sort') {
    jieqi_printfail(LANG_ERROR_PARAMETER);
} else {
    jieqi_checkpost();
}
jieqi_loadlang('article', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['aid']);
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
    jieqi_printfail($jieqiLang['article']['noper_edit_article']);
}
$chaptercount = $article->getVar('chapters');
if (!isset($_REQUEST['fromid']) || $_REQUEST['fromid'] < 1 || $chaptercount < $_REQUEST['fromid'] || !isset($_REQUEST['toid']) || $_REQUEST['toid'] < 0 || $chaptercount < $_REQUEST['toid']) {
    jieqi_printfail($jieqiLang['article']['chapter_sort_errorpar']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if ($_REQUEST['fromid'] == $_REQUEST['toid'] || $_REQUEST['fromid'] == $_REQUEST['toid'] + 1) {
    jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . $_REQUEST['aid'], LANG_DO_SUCCESS, $jieqiLang['article']['chapter_sort_success']);
} else {
    include_once $jieqiModules['article']['path'] . '/class/chapter.php';
    $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
    $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['aid']));
    $criteria->add(new Criteria('chapterorder', $_REQUEST['fromid']));
    $chapter_handler->queryObjects($criteria);
    $chapter = $chapter_handler->getObject();
    unset($criteria);
    if ($chapter) {
        $cid = $chapter->getVar('chapterid');
        if ($_REQUEST['fromid'] < $_REQUEST['toid']) {
            $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['aid'], '='));
            $criteria->add(new Criteria('chapterorder', $_REQUEST['fromid'], '>'));
            $criteria->add(new Criteria('chapterorder', $_REQUEST['toid'], '<='));
            $chapter_handler->updatefields('chapterorder=chapterorder-1', $criteria);
            unset($criteria);
            $criteria = new CriteriaCompo(new Criteria('chapterid', $cid, '='));
            $chapter_handler->updatefields('chapterorder=' . $_REQUEST['toid'], $criteria);
            unset($criteria);
        } else {
            $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['aid'], '='));
            $criteria->add(new Criteria('chapterorder', $_REQUEST['fromid'], '<'));
            $criteria->add(new Criteria('chapterorder', $_REQUEST['toid'], '>'));
            $chapter_handler->updatefields('chapterorder=chapterorder+1', $criteria);
            unset($criteria);
            $criteria = new CriteriaCompo(new Criteria('chapterid', $cid, '='));
            $chapter_handler->updatefields('chapterorder=' . ($_REQUEST['toid'] + 1), $criteria);
            unset($criteria);
        }
        include_once $jieqiModules['article']['path'] . '/class/package.php';
        $package = new JieqiPackage($_REQUEST['aid']);
        $package->sortChapter($_REQUEST['fromid'], $_REQUEST['toid']);
        include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
        $lastinfo = jieqi_article_searchlast($article, 'all');
        $updateblock = false;
        if ($article->getVar('lastchapterid') != $lastinfo['lastchapterid']) {
            $article->setVar('lastchapterid', $lastinfo['lastchapterid']);
            $article->setVar('lastchapter', $lastinfo['lastchapter']);
            $article->setVar('lastsummary', $lastinfo['lastsummary']);
            $updateblock = true;
        }
        if ($article->getVar('lastvolumeid') != $lastinfo['lastvolumeid']) {
            $article->setVar('lastvolumeid', $lastinfo['lastvolumeid']);
            $article->setVar('lastvolume', $lastinfo['lastvolume']);
            $updateblock = true;
        }
        if ($article->getVar('vipchapterid') != $lastinfo['vipchapterid']) {
            $article->setVar('vipchapterid', $lastinfo['vipchapterid']);
            $article->setVar('vipchapter', $lastinfo['vipchapter']);
            $article->setVar('vipsummary', $lastinfo['vipsummary']);
            $updateblock = true;
        }
        if ($article->getVar('vipvolumeid') != $lastinfo['vipvolumeid']) {
            $article->setVar('vipvolumeid', $lastinfo['vipvolumeid']);
            $article->setVar('vipvolume', $lastinfo['vipvolume']);
            $updateblock = true;
        }
        if ($updateblock) {
            $article_handler->insert($article);
            jieqi_article_updateinfo($article, 'chapteredit');
        }
        jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . $_REQUEST['aid'], LANG_DO_SUCCESS, $jieqiLang['article']['chapter_sort_success']);
    } else {
        jieqi_printfail($jieqiLang['article']['chapter_sort_notexists']);
    }
}