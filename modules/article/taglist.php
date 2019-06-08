<?php

define('JIEQI_MODULE_NAME', 'article');
if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
jieqi_loadlang('tag', JIEQI_MODULE_NAME);
jieqi_getconfigs('article', 'configs');
$sortary = array('tagid', 'linknum', 'dayvisit', 'weekvisit', 'monthvisit', 'allvisit');
if (empty($_REQUEST['sort']) || !in_array($_REQUEST['sort'], $sortary)) {
    $_REQUEST['sort'] = 'tagid';
}
if (strtoupper($_REQUEST['order']) == 'ASC') {
    $_REQUEST['order'] = 'ASC';
} else {
    $_REQUEST['order'] = 'DESC';
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/taglist.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiPset = jieqi_get_pageset();
$tagtitle = $jieqiLang['article']['tag_list_' . $_REQUEST['sort']];
$jieqiTpl->assign('tagtitle', $tagtitle);
$tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
$daystart = mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2], (int) $tmpvar[0]);
$monthstart = mktime(0, 0, 0, (int) $tmpvar[1], 1, (int) $tmpvar[0]);
$tmpvar = date('w', JIEQI_NOW_TIME);
if ($tmpvar == 0) {
    $tmpvar = 7;
}
$weekstart = $daystart;
if (1 < $tmpvar) {
    $weekstart -= ($tmpvar - 1) * 86400;
}
$where = '1';
switch ($_REQUEST['sort']) {
    case 'monthvisit':
        $where = 'lastvisit >= ' . $monthstart;
        break;
    case 'weekvisit':
        $where = 'lastvisit >= ' . $weekstart;
        break;
    case 'dayvisit':
        $where = 'lastvisit >= ' . $daystart;
        break;
}
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sqlcot = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_tag') . ' WHERE ' . $where;
$query->execute($sqlcot);
$rowcot = $query->getRow();
$tagcount = intval($rowcot['cot']);
$sql = 'SELECT * FROM ' . jieqi_dbprefix('article_tag') . ' WHERE ' . $where . ' ORDER BY ' . $_REQUEST['sort'] . ' ' . $_REQUEST['order'] . ' LIMIT ' . intval($jieqiPset['start']) . ', ' . intval($jieqiPset['rows']);
$query->execute($sql);
$tagrows = array();
$k = 0;
while ($row = $query->getRow()) {
    $tagrows[$k] = jieqi_query_rowvars($row);
    $tagrows[$k]['tagname_n'] = $row['tagname'];
    $tagrows[$k]['tagname_u'] = urlencode($row['tagname']);
    $k++;
}
$jieqiTpl->assign_by_ref('tagrows', $tagrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $tagcount;
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink(jieqi_geturl('article', 'taglist', 0, $_REQUEST['sort'], $_REQUEST['order']));
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';