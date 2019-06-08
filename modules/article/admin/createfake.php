<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (!empty($_POST['act'])) {
    jieqi_checkpost();
}
@set_time_limit(0);
@session_write_close();
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once $jieqiModules['article']['path'] . '/include/funstatic.php';
echo str_repeat('        ', 1024);
if (empty($_REQUEST['startid']) || !is_numeric($_REQUEST['startid'])) {
    $_REQUEST['startid'] = 1;
} else {
    $_REQUEST['startid'] = intval($_REQUEST['startid']);
}
if (empty($_REQUEST['stopid']) || !is_numeric($_REQUEST['stopid'])) {
    $_REQUEST['stopid'] = 0;
} else {
    $_REQUEST['stopid'] = intval($_REQUEST['stopid']);
}
if ($_REQUEST['filetype'] == 'static') {
    $static = true;
} else {
    $static = false;
}
switch ($_POST['act']) {
    case 'makeinfo':
        echo sprintf($jieqiLang['article']['create_info_doing'], $_REQUEST['startid'], $_REQUEST['stopid']);
        ob_flush();
        flush();
        article_make_binfo($_REQUEST['startid'], $_REQUEST['stopid'], $static, true);
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['create_info_success']);
        break;
    case 'makesort':
        echo sprintf($jieqiLang['article']['create_sort_doing'], $_REQUEST['startid'], $_REQUEST['stopid']);
        ob_flush();
        flush();
        article_make_asort($_REQUEST['startid'], $_REQUEST['stopid'], $static, true);
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['create_sort_success']);
        break;
    case 'makeinitial':
        echo sprintf($jieqiLang['article']['create_initial_doing'], $_REQUEST['startid'], $_REQUEST['stopid']);
        ob_flush();
        flush();
        article_make_ainitial($_REQUEST['startid'], $_REQUEST['stopid'], $static, true);
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['create_initial_success']);
        break;
    case 'maketoplist':
        echo sprintf($jieqiLang['article']['create_toplist_doing'], $_REQUEST['startid'], $_REQUEST['stopid']);
        ob_flush();
        flush();
        article_make_atoplist($_REQUEST['startid'], $_REQUEST['stopid'], $static, true);
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['create_toplist_success']);
        break;
    default:
        jieqi_printfail($jieqiLang['article']['create_para_error']);
}