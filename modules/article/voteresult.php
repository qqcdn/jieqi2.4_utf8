<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('avote', JIEQI_MODULE_NAME);
$_REQUEST['id'] = intval($_REQUEST['id']);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo();
$criteria->setTables(jieqi_dbprefix('article_avote') . ' AS v LEFT JOIN ' . jieqi_dbprefix('article_avstat') . ' AS s ON v.voteid=s.voteid');
$criteria->add(new Criteria('v.voteid', $_REQUEST['id'], '='));
$criteria->setLimit(1);
$criteria->setSort('v.voteid');
$criteria->setOrder('DESC');
$query->queryObjects($criteria);
$voteres = $query->getObject();
if (!$voteres) {
    jieqi_printfail($jieqiLang['article']['avote_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once JIEQI_ROOT_PATH . '/header.php';
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$articleid = $voteres->getVar('articleid', 'n');
$jieqiTpl->assign('articleid', $articleid);
$jieqiTpl->assign('url_articleinfo', jieqi_geturl('article', 'article', $articleid, 'info'));
$resultrows = array();
$useitem = $voteres->getVar('useitem', 'n');
$statall = $voteres->getVar('statall', 'n');
for ($i = 1; $i <= $useitem; $i++) {
    $resultrows[$i - 1]['item'] = $voteres->getVar('item' . $i);
    $resultrows[$i - 1]['stat'] = $voteres->getVar('stat' . $i);
    if (0 < $statall) {
        $resultrows[$i - 1]['percent'] = sprintf('%0.2f', $voteres->getVar('stat' . $i) * 100 / $statall);
    } else {
        $resultrows[$i - 1]['percent'] = 0;
    }
    $resultrows[$i - 1]['recent'] = 100 - $resultrows[$i - 1]['percent'];
}
$jieqiTpl->assign_by_ref('resultrows', $resultrows);
$jieqiTpl->assign('title', $voteres->getVar('title'));
$jieqiTpl->assign('useitem', $useitem);
$jieqiTpl->assign('statall', $statall);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/voteresult.html';
include_once JIEQI_ROOT_PATH . '/footer.php';