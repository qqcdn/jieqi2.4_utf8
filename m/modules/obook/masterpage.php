<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/masterpage.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
if (jieqi_getconfigs('article', 'configs')) {
    $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
    $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
    $jieqiTpl->assign('article_static_url', $article_static_url);
    $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
}
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('authorid', $_SESSION['jieqiUserId']), 'OR');
$criteria->add(new Criteria('agentid', $_SESSION['jieqiUserId']), 'OR');
$criteria->add(new Criteria('posterid', $_SESSION['jieqiUserId']), 'OR');
$criteria->setSort('lastupdate');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$obook_handler->queryObjects($criteria);
$obookrows = array();
$k = 0;
include_once $jieqiModules['obook']['path'] . '/include/funobook.php';
while ($v = $obook_handler->getObject()) {
    $obookrows[$k] = jieqi_obook_obookvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('obookrows', $obookrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$uid = intval($_SESSION['jieqiUserId']);
$sql = 'SELECT count(*) AS cot, sum(sumegold) as sumegold, sum(sumtip) as sumtip, sum(sumemoney) as sumemoney, sum(paidemoney) as paidemoney FROM ' . jieqi_dbprefix('obook_obook') . ' WHERE authorid = ' . $uid . ' OR agentid = ' . $uid . ' OR posterid = ' . $uid;
$query->execute($sql);
$row = $query->getRow();
$row['remainemoney'] = $row['sumemoney'] - $row['paidemoney'];
$jieqiTpl->assign('obookstat', jieqi_funtoarray('jieqi_htmlstr', $row));
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->assign('authorarea', 1);
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';