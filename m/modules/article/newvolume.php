<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
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
    jieqi_printfail($jieqiLang['article']['noper_manage_article']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if (!isset($_POST['act'])) {
    $_POST['act'] = 'volume';
}
switch ($_POST['act']) {
    case 'newvolume':
        jieqi_checkpost();
        $_POST = jieqi_funtoarray('trim', $_POST);
        $errtext = '';
        if (strlen($_POST['chaptername']) == 0) {
            $errtext .= $jieqiLang['article']['need_colume_title'] . '<br />';
        }
        if (empty($errtext)) {
            $_REQUEST['draftid'] = 0;
            $_POST['draftid'] = 0;
            $_POST['chaptertype'] = 1;
            $_POST['chapterorder'] = $article->getVar('chapters') + 1;
            $_POST['chaptercontent'] = '';
            $_POST['isvip'] = 0;
            $_POST['articleid'] = $_REQUEST['aid'];
            $attachvars = array();
            include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
            jieqi_article_addchapter($_POST, $attachvars, $article);
            jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . $_REQUEST['aid'], LANG_DO_SUCCESS, sprintf($jieqiLang['article']['add_volume_success'], $article_static_url . '/articlemanage.php?id=' . $_REQUEST['aid'], jieqi_geturl('article', 'article', $_REQUEST['aid'], 'info', $article->getVar('articlecode', 'n')), $article_static_url . '/newchapter.php?aid=' . $_REQUEST['aid']));
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'volume':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $volumerows = array();
        include_once $jieqiModules['article']['path'] . '/class/chapter.php';
        $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
        $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['aid']));
        $criteria->add(new Criteria('chaptertype', 1));
        $criteria->setSort('chapterorder');
        $criteria->setOrder('ASC');
        $chapter_handler->queryObjects($criteria);
        $k = 0;
        while ($v = $chapter_handler->getObject()) {
            $volumerows[$k]['chapterid'] = $v->getVar('chapterid');
            $volumerows[$k]['chaptername'] = $v->getVar('chaptername');
            $k++;
        }
        $jieqiTpl->assign_by_ref('volumerows', $volumerows);
        $jieqiTpl->assign('articleid', $article->getVar('articleid'));
        $jieqiTpl->assign('articlename', $article->getVar('articlename'));
        $jieqiTpl->assign('aid', $_REQUEST['aid']);
        $jieqiTpl->assign('authorarea', 1);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/newvolume.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}