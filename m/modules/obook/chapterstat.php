<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
if (empty($_REQUEST['oid']) || !is_numeric($_REQUEST['oid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['oid'] = intval($_REQUEST['oid']);
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
$obook = $obook_handler->get($_REQUEST['oid']);
if (!$obook) {
    jieqi_printfail($jieqiLang['obook']['obook_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$canedit = jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($obook->getVar('authorid') == $tmpvar || $obook->getVar('posterid') == $tmpvar || $obook->getVar('agentid') == $tmpvar)) {
        $canedit = true;
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['obook']['noper_manage_obook']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
include_once $jieqiModules['obook']['path'] . '/class/ochapter.php';
$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
$criteria = new CriteriaCompo(new Criteria('obookid', $_REQUEST['oid'], '='));
$criteria->add(new Criteria('chaptertype', 0, '='));
$criteria->setSort('chapterorder');
$criteria->setOrder('ASC');
$ochapter_handler->queryObjects($criteria);
$ochapterrows = array();
$k = 0;
include_once $jieqiModules['obook']['path'] . '/include/funochapter.php';
$ochaptersum = array('count' => 0, 'saleprice' => 0, 'allsale' => 0, 'sumemoney' => 0);
while ($v = $ochapter_handler->getObject()) {
    $ochaptersum['count']++;
    $ochaptersum['saleprice'] += $v->getVar('saleprice', 'n');
    $ochaptersum['allsale'] += $v->getVar('allsale', 'n');
    $ochaptersum['sumemoney'] += $v->getVar('sumegold', 'n') + $v->getVar('sumesilver', 'n');
    $ochapterrows[$k] = jieqi_obook_ochaptervars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('ochapterrows', $ochapterrows);
$jieqiTpl->assign('obookid', $_REQUEST['oid']);
$jieqiTpl->assign('obookname', $obook->getVar('obookname'));
$jieqiTpl->assign_by_ref('ochaptersum', $ochaptersum);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/chapterstat.html';
include_once JIEQI_ROOT_PATH . '/footer.php';