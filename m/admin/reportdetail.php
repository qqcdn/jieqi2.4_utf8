<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminreport'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('report', JIEQI_MODULE_NAME);
if (empty($_REQUEST['id'])) {
    jieqi_printfail($jieqiLang['system']['report_no_exists']);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
include_once JIEQI_ROOT_PATH . '/class/report.php';
$report_handler = JieqiReportHandler::getInstance('JieqiReportHandler');
$report = $report_handler->get($_REQUEST['id']);
if (!$report) {
    jieqi_printfail($jieqiLang['system']['report_no_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'rsort', 'jieqiRsort');
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiTpl->assign('reportid', $_REQUEST['id']);
$jieqiTpl->assign('siteid', $report->getVar('siteid'));
$jieqiTpl->assign('reporttime', $report->getVar('reporttime'));
$jieqiTpl->assign('reportuid', $report->getVar('reportuid'));
$jieqiTpl->assign('reportname', $report->getVar('reportname'));
$jieqiTpl->assign('authtime', $report->getVar('authtime'));
$jieqiTpl->assign('authuid', $report->getVar('authuid'));
$jieqiTpl->assign('authname', $report->getVar('authname'));
$jieqiTpl->assign('reporttitle', $report->getVar('reporttitle'));
$jieqiTpl->assign('reporttext', jieqi_htmlclickable($report->getVar('reporttext')));
$jieqiTpl->assign('reportsize', $report->getVar('reportsize'));
$jieqiTpl->assign('reportfield', $report->getVar('reportfield'));
$jieqiTpl->assign('authnote', $report->getVar('authnote'));
$jieqiTpl->assign('reportsort', $report->getVar('reportsort'));
$jieqiTpl->assign('reporttype', $report->getVar('reporttype'));
$jieqiTpl->assign('authflag', $report->getVar('authflag'));
$jieqiTpl->assign('sortname', $jieqiRsort[$report->getVar('reportsort')]['caption']);
if (isset($jieqiRsort[$report->getVar('reportsort')]['types'][$report->getVar('reporttype')])) {
    $jieqiTpl->assign('typename', $jieqiRsort[$report->getVar('reportsort')]['types'][$report->getVar('reporttype')]);
} else {
    $reportrows[$k]['typename'] = $jieqiTpl->assign('typename', $jieqiLang['system']['report_type_other']);
}
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/reportdetail.html';
if ($report->getVar('authflag') == 0) {
    $report->setVar('authflag', '1');
    $report_handler->insert($report);
}
include_once JIEQI_ROOT_PATH . '/admin/footer.php';