<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
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
jieqi_getconfigs(JIEQI_MODULE_NAME, 'action');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/articleactlog.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$slimit = 'articleid = ' . $_REQUEST['id'];
if (!empty($_REQUEST['uid'])) {
    $slimit .= ' AND uid = ' . intval($_REQUEST['uid']);
}
if (!empty($_REQUEST['uname'])) {
    $slimit .= ' AND uname = \'' . jieqi_dbslashes($_REQUEST['uname']) . '\'';
}
$actname = '';
if (!empty($_REQUEST['act']) && isset($jieqiAction['article'][$_REQUEST['act']])) {
    $actname = jieqi_htmlstr($jieqiAction['article'][$_REQUEST['act']]['acttitle']);
    $slimit .= ' AND actname = \'' . jieqi_dbslashes($_REQUEST['act']) . '\'';
}
$jieqiTpl->assign('actname', $actname);
if (!empty($_REQUEST['datestart'])) {
    $tmpvar = @strtotime($_REQUEST['datestart']);
    if (0 < $tmpvar) {
        $slimit .= ' AND addtime >= ' . intval($tmpvar);
    }
}
if (!empty($_REQUEST['dateend'])) {
    $tmpvar = @strtotime($_REQUEST['dateend']);
    if (0 < $tmpvar) {
        $slimit .= ' AND addtime <= ' . intval($tmpvar);
    }
}
$sql = 'SELECT * FROM ' . jieqi_dbprefix('article_actlog') . ' WHERE ' . $slimit . ' ORDER BY actlogid DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
$query->execute($sql);
$actlogrows = array();
$k = 0;
while ($row = $query->getRow()) {
    $actlogrows[$k] = jieqi_query_rowvars($row);
    $actlogrows[$k]['actname_n'] = $actlogrows[$k]['actname'];
    if (isset($jieqiAction['article'][$actlogrows[$k]['actname_n']])) {
        $actlogrows[$k]['actname'] = jieqi_htmlstr($jieqiAction['article'][$actlogrows[$k]['actname_n']]['acttitle']);
    }
    $k++;
}
$jieqiTpl->assign_by_ref('actlogrows', $actlogrows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
$jieqiTpl->assign('actionrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiAction['article']));
$jieqiTpl->assign('articleid', $_REQUEST['id']);
$jieqiTpl->assign('isvip', $article->getVar('isvip'));
$jieqiTpl->assign('vipid', $article->getVar('vipid'));
$jieqiTpl->assign('articlename', $article->getVar('articlename'));
$sql = 'SELECT count(*) as count, sum(actnum) as actnum  FROM ' . jieqi_dbprefix('article_actlog') . ' WHERE ' . $slimit . ' LIMIT 0,1';
$query->execute($sql);
$row = $query->getRow();
$actlogsum = is_array($row) ? jieqi_query_rowvars($row) : array();
$jieqiTpl->assign_by_ref('actlogsum', $actlogsum);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$sql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_actlog') . ' WHERE ' . $slimit;
$query->execute($sql);
$row = $query->getRow();
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';