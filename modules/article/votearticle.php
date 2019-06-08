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
$_REQUEST['id'] = intval($_REQUEST['id']);
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
if (!empty($_POST['act']) && !empty($_REQUEST['vid'])) {
    jieqi_checkpost();
    $_REQUEST['vid'] = intval($_REQUEST['vid']);
    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    switch ($_POST['act']) {
        case 'delete':
            $sql = 'DELETE FROM ' . jieqi_dbprefix('article_avote') . ' WHERE voteid=' . $_REQUEST['vid'];
            $query->execute($sql);
            $sql = 'DELETE FROM ' . jieqi_dbprefix('article_avstat') . ' WHERE voteid=' . $_REQUEST['vid'];
            $query->execute($sql);
            $setting = jieqi_unserialize($article->getVar('setting', 'n'));
            $setting['avoteid'] = 0;
            $article->setVar('setting', serialize($setting));
            $article_handler->insert($article);
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'publish':
            $sql = 'UPDATE ' . jieqi_dbprefix('article_avote') . ' SET ispublish=0, endtime=' . intval(JIEQI_NOW_TIME) . ' WHERE articleid=' . $_REQUEST['id'] . ' AND ispublish=1';
            $query->execute($sql);
            $sql = 'UPDATE ' . jieqi_dbprefix('article_avote') . ' SET ispublish=1, starttime=' . intval(JIEQI_NOW_TIME) . ' WHERE voteid=' . $_REQUEST['vid'];
            $query->execute($sql);
            $setting = jieqi_unserialize($article->getVar('setting', 'n'));
            $setting['avoteid'] = $_REQUEST['vid'];
            $article->setVar('setting', serialize($setting));
            $article_handler->insert($article);
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'cancel':
            $sql = 'UPDATE ' . jieqi_dbprefix('article_avote') . ' SET ispublish=0, endtime=' . intval(JIEQI_NOW_TIME) . ' WHERE voteid=' . $_REQUEST['vid'];
            $query->execute($sql);
            $setting = jieqi_unserialize($article->getVar('setting', 'n'));
            $setting['avoteid'] = 0;
            $article->setVar('setting', serialize($setting));
            $article_handler->insert($article);
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
    }
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once JIEQI_ROOT_PATH . '/header.php';
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$jieqiTpl->assign('articleid', $article->getVar('articleid'));
$jieqiTpl->assign('articlename', $article->getVar('articlename'));
$jieqiTpl->assign('url_articleinfo', jieqi_geturl('article', 'article', $article->getVar('articleid'), 'info', $article->getVar('articlecode', 'n')));
include_once $jieqiModules['article']['path'] . '/class/avote.php';
$avote_handler = JieqiAvoteHandler::getInstance('JieqiAvoteHandler');
$criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['id'], '='));
$criteria->setSort('voteid');
$criteria->setOrder('DESC');
$avote_handler->queryObjects($criteria);
$voterows = array();
$k = 0;
while ($vote = $avote_handler->getObject()) {
    $voterows[$k]['order'] = $k + 1;
    $voterows[$k]['voteid'] = $vote->getVar('voteid');
    $voterows[$k]['posttime'] = $vote->getVar('posttime');
    $voterows[$k]['title'] = $vote->getVar('title');
    $voterows[$k]['useitem'] = $vote->getVar('useitem');
    $voterows[$k]['ispublish'] = $vote->getVar('ispublish');
    $voterows[$k]['mulselect'] = $vote->getVar('mulselect');
    $voterows[$k]['starttime'] = $vote->getVar('starttime');
    $voterows[$k]['endtime'] = $vote->getVar('endtime');
    $k++;
}
$jieqiTpl->assign_by_ref('voterows', $voterows);
$jieqiTpl->assign('authorarea', 1);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/votearticle.html';
include_once JIEQI_ROOT_PATH . '/footer.php';