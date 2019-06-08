<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['articleid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('article', JIEQI_MODULE_NAME);
if (empty($_REQUEST['chapterid'])) {
    jieqi_printfail($jieqiLang['article']['noselect_act_chapter']);
}
$_REQUEST['articleid'] = intval($_REQUEST['articleid']);
if (empty($_REQUEST['articleid']) || empty($_POST['act']) || !in_array($_POST['act'], array('delete', 'vip', 'free', 'hide', 'show'))) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
} else {
    jieqi_checkpost();
}
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['articleid']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$ismanager = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$canedit = $ismanager;
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($article->getVar('authorid') == $tmpvar || $article->getVar('agentid') == $tmpvar)) {
        $canedit = true;
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['article']['noper_manage_article']);
}
if ($_POST['act'] == 'vip' && intval($article->getVar('issign', 'n')) < 10) {
    jieqi_printfail($jieqiLang['article']['set_vipchapter_nosign']);
}
if (!$ismanager && in_array($_POST['act'], array('show', 'hide'))) {
    jieqi_printfail(sprintf($jieqiLang['article']['noper_set_chapter'], $jieqiLang['article']['chapter_name']));
}
$candelete = $ismanager;
if (!$candelete && $canedit) {
    $candelete = jieqi_checkpower($jieqiPower['article']['delmyarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
}
jieqi_getconfigs('article', 'rule');
$actrule = true;
if (function_exists('jieqi_rule_article_articledelete')) {
    $actrule = jieqi_rule_article_articledelete($article);
    if ($actrule === false) {
        jieqi_printfail($jieqiLang['article']['noper_clean_article']);
    }
}
$cidary = array();
foreach ($_REQUEST['chapterid'] as $cid) {
    $cid = intval($cid);
    if (!empty($cid) && !in_array($cid, $cidary)) {
        $cidary[] = $cid;
    }
}
if (0 < count($cidary)) {
    include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
    include_once $jieqiModules['article']['path'] . '/class/chapter.php';
    $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('chapterid', '(' . implode(', ', $cidary) . ')', 'IN'));
    switch ($_POST['act']) {
        case 'delete':
            if (!$candelete) {
                $criteria->add(new Criteria('isvip', 0, '='));
            }
            jieqi_article_delchapter($_REQUEST['articleid'], $criteria);
            break;
        case 'vip':
        case 'free':
        case 'hide':
        case 'show':
            $bakConfigs = $jieqiConfigs['article'];
            $jieqiConfigs['article']['makezip'] = 0;
            $jieqiConfigs['article']['makefull'] = 0;
            $jieqiConfigs['article']['maketxtfull'] = 0;
            $jieqiConfigs['article']['makeumd'] = 0;
            $jieqiConfigs['article']['makejar'] = 0;
            $criteria->add(new Criteria('chaptertype', 0, '='));
            $res = $chapter_handler->queryObjects($criteria);
            while ($chapter = $chapter_handler->getObject($res)) {
                jieqi_article_chapterset($chapter, $article, $_POST['act']);
            }
            $jieqiConfigs['article'] = $bakConfigs;
            include_once $jieqiModules['article']['path'] . '/class/package.php';
            $package = new JieqiPackage($article->getVar('articleid'));
            $package->makepack();
            jieqi_article_updateinfo(0);
            break;
    }
}
jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . $_REQUEST['articleid'], LANG_DO_SUCCESS, $jieqiLang['article']['chapter_batchact_success']);