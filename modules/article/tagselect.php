<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
include_once JIEQI_ROOT_PATH . '/header.php';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = 'SELECT * FROM ' . jieqi_dbprefix('article_tag') . ' WHERE addtime = 0 AND userid = 0 ORDER BY tagid ASC LIMIT 0, 200';
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
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/tagselect.html';
include_once JIEQI_ROOT_PATH . '/footer.php';