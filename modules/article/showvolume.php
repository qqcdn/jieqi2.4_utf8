<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['aid']) || empty($_REQUEST['vid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
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
    $package->showVolume($_REQUEST['vid']);
} else {
    jieqi_loadlang('article', JIEQI_MODULE_NAME);
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}