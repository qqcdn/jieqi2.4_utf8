<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
include_once JIEQI_ROOT_PATH . '/header.php';
include_once $jieqiModules['article']['path'] . '/class/package.php';
$package = new JieqiPackage($_REQUEST['id']);
if ($package->loadOPF()) {
    $cid = 0;
    $isvip = 0;
    foreach ($package->chapters as $chapter) {
        if ($chapter['chaptertype'] == 0) {
            $cid = intval($chapter['chapterid']);
            $isvip = intval($chapter['isvip']);
            break;
        }
    }
    if (0 < $cid) {
        header('Location: ' . jieqi_headstr(jieqi_geturl('article', 'chapter', $cid, $_REQUEST['id'], $isvip)));
    } else {
        jieqi_loadlang('article', JIEQI_MODULE_NAME);
        jieqi_printfail($jieqiLang['article']['noper_manage_article']);
    }
} else {
    jieqi_loadlang('article', JIEQI_MODULE_NAME);
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}