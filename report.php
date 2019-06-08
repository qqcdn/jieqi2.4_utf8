<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
if (isset($jieqiPower['system']['newreport'])) {
    jieqi_checkpower($jieqiPower['system']['newreport'], $jieqiUsersStatus, $jieqiUsersGroup);
}
jieqi_loadlang('report', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'rsort', 'jieqiRsort');
if (isset($_POST['act']) && $_POST['act'] == 'post') {
    jieqi_checkpost();
    $errtext = '';
    if (empty($_POST['reporttitle'])) {
        $errtext .= $jieqiLang['system']['report_need_title'] . '<br />';
    }
    if (empty($_POST['reporttext'])) {
        $errtext .= $jieqiLang['system']['report_need_text'] . '<br />';
    }
    if (empty($errtext)) {
        include_once JIEQI_ROOT_PATH . '/class/report.php';
        $report_handler = JieqiReportHandler::getInstance('JieqiReportHandler');
        $newReport = $report_handler->create();
        $newReport->setVar('siteid', JIEQI_SITE_ID);
        $newReport->setVar('reporttime', JIEQI_NOW_TIME);
        if (!empty($_SESSION['jieqiUserId'])) {
            $newReport->setVar('reportuid', $_SESSION['jieqiUserId']);
            $newReport->setVar('reportname', $_SESSION['jieqiUserName']);
        } else {
            $newReport->setVar('reportuid', 0);
            $newReport->setVar('reportname', '');
        }
        $newReport->setVar('authtime', 0);
        $newReport->setVar('authuid', 0);
        $newReport->setVar('authname', '');
        $newReport->setVar('reporttitle', $_POST['reporttitle']);
        $newReport->setVar('reporttext', $_POST['reporttext']);
        $newReport->setVar('reportsize', strlen($_POST['reporttext']));
        if (!isset($_POST['reportfield'])) {
            $_POST['reportfield'] = '';
        }
        $newReport->setVar('reportfield', strval($_POST['reportfield']));
        $newReport->setVar('authnote', '');
        $newReport->setVar('reportsort', intval($_POST['reportsort']));
        $newReport->setVar('reporttype', intval($_POST['reporttype']));
        $newReport->setVar('authflag', 0);
        $report_handler->insert($newReport);
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->setCaching(0);
        $jieqiTpl->assign('jieqi_contents', jieqi_msgbox(LANG_DO_SUCCESS, $jieqiLang['system']['report_submit_success']));
        include_once JIEQI_ROOT_PATH . '/footer.php';
    } else {
        jieqi_printfail($errtext);
    }
} else {
    include_once JIEQI_ROOT_PATH . '/header.php';
    $jieqiTpl->assign_by_ref('rsortrows', $jieqiRsort);
    $jieqiTpl->setCaching(0);
    $jieqiTpl->assign('jieqi_contents', $jieqiTpl->fetch(JIEQI_ROOT_PATH . '/templates/report.html'));
    include_once JIEQI_ROOT_PATH . '/footer.php';
}