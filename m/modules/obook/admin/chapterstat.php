<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_REQUEST['oid']) || !is_numeric($_REQUEST['oid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['oid'] = intval($_REQUEST['oid']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
include_once $jieqiModules['obook']['path'] . '/class/ochapter.php';
$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiTpl->assign('obook_static_url', $obook_static_url);
$jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
if (!empty($_REQUEST['oname'])) {
    $obookname = jieqi_htmlstr($_REQUEST['oname']);
} else {
    $obookname = '';
}
$criteria = new CriteriaCompo(new Criteria('obookid', $_REQUEST['oid'], '='));
$criteria->add(new Criteria('chaptertype', 0, '='));
$criteria->setSort('chapterorder');
$criteria->setOrder('ASC');
$ochapter_handler->queryObjects($criteria);
$ochapterrows = array();
$k = 0;
include_once $jieqiModules['obook']['path'] . '/include/funochapter.php';
while ($v = $ochapter_handler->getObject()) {
    $ochapterrows[$k] = jieqi_obook_ochaptervars($v);
    if (empty($obookname)) {
        $obookname = $ochapterrows[$k]['obookname'];
    }
    $k++;
}
$jieqiTpl->assign_by_ref('ochapterrows', $ochapterrows);
$jieqiTpl->assign('obookid', $_REQUEST['oid']);
$jieqiTpl->assign('obookname', $obookname);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/admin/chapterstat.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';