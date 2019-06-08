<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_loadlang('index', JIEQI_MODULE_NAME);
if ($jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
    jieqi_printfail(LANG_NEED_ADMIN);
}
if (JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET) {
    jieqi_printfail(sprintf($jieqiLang['system']['sindex_need_charset'], JIEQI_CHAR_SET));
}
if (empty($_REQUEST['confirm'])) {
    jieqi_msgwin(LANG_NOTICE, sprintf($jieqiLang['system']['sindex_confirm_notice'], jieqi_addurlvars(array('confirm' => 1))));
}
if (empty($_REQUEST['target']) || 32 < strlen($_REQUEST['target']) || !preg_match('/^\\w+\\.html?$/', $_REQUEST['target'])) {
    $_REQUEST['target'] = 'index.html';
}
$pagecontent = @file_get_contents(JIEQI_LOCAL_URL . '/index.php');
if ($pagecontent == false || $pagecontent == '') {
    jieqi_printfail($jieqiLang['system']['get_content_failure']);
}
$ret = jieqi_writefile(JIEQI_ROOT_PATH . '/' . $_REQUEST['target'], $pagecontent);
if ($ret) {
    jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['system']['make_static_success'], JIEQI_URL . '/' . $_REQUEST['target']));
} else {
    jieqi_printfail(sprintf($jieqiLang['system']['make_static_failure'], $_REQUEST['target']));
}