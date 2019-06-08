<?php

define('JIEQI_MODULE_NAME', 'article');
if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
jieqi_loadlang('article', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
} else {
    if ($article->getVar('display') != 0 && $jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
        jieqi_printfail($jieqiLang['article']['article_not_audit']);
    } else {
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/creditlist.html';
        include_once JIEQI_ROOT_PATH . '/header.php';
        jieqi_getconfigs('article', 'sort', 'jieqiSort');
        jieqi_getconfigs('article', 'option', 'jieqiOption');
        include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
        $articlevals = jieqi_article_vars($article, true);
        $jieqiTpl->assign_by_ref('articlevals', $articlevals);
        foreach ($articlevals as $k => $v) {
            $jieqiTpl->assign($k, $articlevals[$k]);
        }
    }
}
$jieqiPset = jieqi_get_pageset();
jieqi_getconfigs('article', 'credit', 'jieqiCredit');
$creditrows = array();
$slimit = 'articleid = ' . $_REQUEST['id'];
$sort = 'point';
$order = 'DESC';
$limitstart = intval($jieqiPset['start']);
$listpnum = intval($jieqiPset['rows']);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = 'SELECT * FROM ' . jieqi_dbprefix('article_credit') . ' WHERE ' . $slimit . ' ORDER BY ' . $sort . ' ' . $order . ' LIMIT ' . $limitstart . ',' . $listpnum;
$cotsql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_credit') . ' WHERE ' . $slimit;
$creditrows = array();
$query->execute($sql);
$k = 0;
while ($row = $query->getRow()) {
    $creditrows[$k] = jieqi_funtoarray('jieqi_htmlstr', $row);
    $creditrows[$k]['order'] = $limitstart + $k + 1;
    $mincredit = 0;
    $creditrows[$k]['rank'] = '';
    foreach ($jieqiCredit['article'] as $v) {
        if ($v['minnum'] <= $creditrows[$k]['point'] && $mincredit <= $v['minnum']) {
            $creditrows[$k]['rank'] = $v['caption'];
            $mincredit = $v['minnum'];
        }
    }
    $k++;
}
$jieqiTpl->assign_by_ref('creditrows', $creditrows);
$jieqiTpl->assign('articleid', $_REQUEST['id']);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$query->execute($cotsql);
$row = $query->getRow();
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';