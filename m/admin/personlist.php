<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/personlist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$slimit = '1';
$ssort = 'uid';
$sorder = 'DESC';
$spstart = intval($jieqiPset['start']);
$sprows = intval($jieqiPset['rows']);
if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
    switch ($_REQUEST['keytype']) {
        case 'idcard':
            $slimit = 'idcard = \'' . jieqi_dbslashes($_REQUEST['keyword']) . '\'';
            break;
        case 'telephone':
            $slimit = 'telephone LIKE \'%' . jieqi_dbslashes($_REQUEST['keyword']) . '%\'';
            break;
        case 'mobilephone':
            $slimit = 'mobilephone = \'' . jieqi_dbslashes($_REQUEST['keyword']) . '\'';
            break;
        case 'realname':
        default:
            $slimit = 'realname = \'' . jieqi_dbslashes($_REQUEST['keyword']) . '\'';
            break;
    }
}
$sql = 'SELECT * FROM ' . jieqi_dbprefix('system_persons') . ' WHERE ' . $slimit . ' ORDER BY ' . $ssort . ' ' . $sorder . ' LIMIT ' . $spstart . ',' . $sprows;
$sqlcot = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('system_persons') . ' WHERE ' . $slimit;
$query->execute($sql);
$personsrows = array();
$k = 0;
include_once JIEQI_ROOT_PATH . '/include/funpersons.php';
while ($row = $query->getRow()) {
    $personsrows[$k] = jieqi_system_personsvars($row);
    $k++;
}
$jieqiTpl->assign_by_ref('personsrows', $personsrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$query->execute($sqlcot);
$row = $query->getRow();
$jieqiPset['count'] = intval($row['cot']);
$jieqiTpl->assign_by_ref('rowcount', $jieqiPset['count']);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';