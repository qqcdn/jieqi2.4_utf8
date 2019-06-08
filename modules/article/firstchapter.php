<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['aid'] = intval($_REQUEST['aid']);
include_once $jieqiModules['article']['path'] . '/class/package.php';
$package = new JieqiPackage($_REQUEST['aid']);
if ($package->loadOPF()) {
    if ($package->metas['display'] != 0) {
        jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
        if (!jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
            jieqi_loadlang('article', JIEQI_MODULE_NAME);
            jieqi_printfail($jieqiLang['article']['article_not_audit']);
        }
    }
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
        $url = jieqi_geturl('article', 'chapter', $cid, $_REQUEST['aid'], $isvip);
        header('Location: ' . jieqi_headstr($url));
        exit;
    } else {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
} else {
    jieqi_loadlang('article', JIEQI_MODULE_NAME);
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}