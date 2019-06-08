<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/myearnlog.html';
include_once JIEQI_ROOT_PATH . '/header.php';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$jieqiPset = jieqi_get_pageset();
$sql = 'SELECT * FROM ' . jieqi_dbprefix('system_earnlog') . ' WHERE userid = ' . intval($_SESSION['jieqiUserId']) . ' ORDER BY logid DESC LIMIT ' . intval($jieqiPset['start']) . ', ' . intval($jieqiPset['rows']);
$query->execute($sql);
$earnrows = array();
$k = 0;
$growthmax = 0;
while ($row = $query->getRow()) {
    $earnrows[$k] = jieqi_query_rowvars($row);
    $k++;
}
$jieqiTpl->assign_by_ref('earnrows', $earnrows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
$channelerate = isset($jieqiConfigs['system']['channelerate']) ? $jieqiConfigs['system']['channelerate'] : 0;
$jieqiTpl->assign('channelerate', $channelerate);
$channeldlimit = isset($jieqiConfigs['system']['channeldlimit']) && is_numeric($jieqiConfigs['system']['channeldlimit']) ? intval($jieqiConfigs['system']['channeldlimit']) : 0;
$jieqiTpl->assign('channeldlimit', $channeldlimit);
$exchangerate = isset($jieqiConfigs['system']['exchangerate']) ? $jieqiConfigs['system']['exchangerate'] : 0;
$jieqiTpl->assign('exchangerate', $exchangerate);
$settlemin = isset($jieqiConfigs['system']['settlemin']) && is_numeric($jieqiConfigs['system']['settlemin']) ? $jieqiConfigs['system']['settlemin'] : 0;
$jieqiTpl->assign('settlemin', $settlemin);
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';