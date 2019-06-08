<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
jieqi_loadlang('hurry', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/myhurry.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$slimit = 'uid = ' . intval($_SESSION['jieqiUserId']);
if ($_REQUEST['payflag'] == 1) {
    $slimit .= ' AND payflag = 1';
} else {
    if ($_REQUEST['payflag'] == 2) {
        $slimit .= ' AND payflag = 2';
    } else {
        $slimit .= ' AND payflag = 0';
    }
}
$sql = 'SELECT * FROM ' . jieqi_dbprefix('article_hurry') . ' WHERE ' . $slimit . ' ORDER BY hurryid DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
$query->execute($sql);
$hurryrows = array();
$k = 0;
while ($row = $query->getRow()) {
    $hurryrows[$k] = jieqi_query_rowvars($row);
    $k++;
}
$jieqiTpl->assign_by_ref('hurryrows', $hurryrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$sql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_hurry') . ' WHERE ' . $slimit;
$query->execute($sql);
$row = $query->getRow();
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';