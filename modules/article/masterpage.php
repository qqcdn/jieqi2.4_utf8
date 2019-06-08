<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_getconfigs('article', 'power');
jieqi_checkpower($jieqiPower['article']['authorpanel'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_loadlang('list', JIEQI_MODULE_NAME);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/masterpage.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$criteria = new CriteriaCompo();
$articletitle = $jieqiLang['article']['my_all_article'];
if (empty($_REQUEST['display'])) {
    $_REQUEST['display'] = 'all';
}
switch ($_REQUEST['display']) {
    case 'author':
        $criteria->add(new Criteria('authorid', $_SESSION['jieqiUserId'], '='));
        $articletitle = $jieqiLang['article']['my_post_article'];
        break;
    case 'poster':
        $criteria->add(new Criteria('authorid', $_SESSION['jieqiUserId'], '!='));
        $criteria->add(new Criteria('posterid', $_SESSION['jieqiUserId'], '='));
        $articletitle = $jieqiLang['article']['my_trans_article'];
        break;
    case 'agent':
        $criteria->add(new Criteria('authorid', $_SESSION['jieqiUserId'], '!='));
        $criteria->add(new Criteria('posterid', $_SESSION['jieqiUserId'], '!='));
        $criteria->add(new Criteria('agentid', $_SESSION['jieqiUserId'], '='));
        $articletitle = $jieqiLang['article']['my_agent_article'];
        break;
    case 'all':
    default:
        $criteria->add(new Criteria('authorid', $_SESSION['jieqiUserId'], '='), 'OR');
        $criteria->add(new Criteria('posterid', $_SESSION['jieqiUserId'], '='), 'OR');
        $criteria->add(new Criteria('agentid', $_SESSION['jieqiUserId'], '='), 'OR');
        $articletitle = $jieqiLang['article']['my_all_article'];
}
$jieqiTpl->assign('articletitle', $articletitle);
$jieqiTpl->assign('url_article', $article_dynamic_url . '/masterpage.php');
$criteria->setSort('greatest(lastupdate, postdate)');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$article_handler->queryObjects($criteria);
$articlerows = array();
$k = 0;
while ($v = $article_handler->getObject()) {
    $articlerows[$k] = jieqi_article_vars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('articlerows', $articlerows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $article_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->assign('authorarea', 1);
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';