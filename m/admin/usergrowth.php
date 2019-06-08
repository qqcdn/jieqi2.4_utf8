<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/usergrowth.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$slimit = '1';
$datefield = '';
if (!empty($_REQUEST['datestart'])) {
    $_REQUEST['datestart'] = trim(str_replace(array('-', '/', ' '), '', $_REQUEST['datestart']));
    if (strlen($_REQUEST['datestart']) == 6) {
        $datefield = 'joinmonth';
    } else {
        if (strlen($_REQUEST['datestart']) == 8) {
            $datefield = 'joindate';
        }
    }
}
if (empty($datefield)) {
    $datefield = 'joindate';
    $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
    $_REQUEST['datestart'] = date('Ymd', mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2] - 30, (int) $tmpvar[0]));
}
if (!empty($_REQUEST['dateend'])) {
    $_REQUEST['dateend'] = trim(str_replace(array('-', '/', ' '), '', $_REQUEST['dateend']));
}
if (empty($_REQUEST['dateend'])) {
    $_REQUEST['dateend'] = $datefield == 'joindate' ? date('Ymd', JIEQI_NOW_TIME) : date('Ym', JIEQI_NOW_TIME);
}
$_REQUEST['datestart'] = intval($_REQUEST['datestart']);
$_REQUEST['dateend'] = intval($_REQUEST['dateend']);
$slimit = $datefield . ' >= ' . $_REQUEST['datestart'] . ' AND ' . $datefield . ' <= ' . $_REQUEST['dateend'];
if (!empty($_REQUEST['channel'])) {
    $slimit .= ' AND channel = \'' . jieqi_dbslashes($_REQUEST['channel']) . '\'';
}
$sql = 'SELECT ' . $datefield . ', count(*) AS growthnum FROM ' . jieqi_dbprefix('system_activity') . ' WHERE ' . $slimit . ' GROUP BY ' . $datefield . ' ORDER BY ' . $datefield . ' DESC LIMIT 0, 100';
$query->execute($sql);
$growthrows = array();
$k = 0;
$growthmax = 0;
$growthsum = array('cot' => 0, 'sumgrowth' => 0);
while ($row = $query->getRow()) {
    $growthrows[$k] = jieqi_query_rowvars($row);
    $growthrows[$k]['datevalue'] = $growthrows[$k][$datefield];
    if ($growthmax < $growthrows[$k]['growthnum']) {
        $growthmax = $growthrows[$k]['growthnum'];
    }
    $growthsum['cot']++;
    $growthsum['sumgrowth'] += $growthrows[$k]['growthnum'];
    $k++;
}
foreach ($growthrows as $k => $v) {
    $growthrows[$k]['growthpercent'] = round($growthrows[$k]['growthnum'] * 100 / $growthmax, 2);
}
$jieqiTpl->assign_by_ref('growthrows', $growthrows);
$jieqiTpl->assign('growthsum', jieqi_funtoarray('jieqi_htmlstr', $growthsum));
$jieqiTpl->assign('datefield', $datefield);
$jieqiTpl->assign('growthmax', $growthmax);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
jieqi_getconfigs('system', 'channels', 'jieqiChannels');
if (empty($jieqiChannels)) {
    $jieqiChannels = array();
}
$jieqiTpl->assign('jieqi_channels', jieqi_funtoarray('jieqi_htmlstr', $jieqiChannels));
$jieqiTpl->assign('jieqi_channelnum', count($jieqiChannels));
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';