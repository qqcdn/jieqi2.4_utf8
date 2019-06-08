<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('applywriter', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/applywriter.php';
$apply_handler = JieqiApplywriterHandler::getInstance('JieqiApplywriterHandler');
$applywriter = $apply_handler->get($_REQUEST['id']);
if (!is_object($applywriter)) {
    jieqi_printfail($jieqiLang['article']['applywriter_not_exists']);
}
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTpl->setCaching(0);
$jieqiTpl->assign(jieqi_query_rowvars($applywriter));
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/applyinfo.html';
include_once JIEQI_ROOT_PATH . '/footer.php';