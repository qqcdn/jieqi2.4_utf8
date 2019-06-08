<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['key'])) {
    exit('no key');
} else {
    if (defined('JIEQI_SITE_KEY') && $_REQUEST['key'] != JIEQI_SITE_KEY) {
        exit('error key');
    } else {
        if ($_REQUEST['key'] != md5(JIEQI_DB_USER . JIEQI_DB_PASS . JIEQI_DB_NAME)) {
            exit;
        }
    }
}
if (!is_numeric($_REQUEST['aid']) || !isset($_REQUEST['act']) || !isset($_REQUEST['cid'])) {
    exit;
}
if (empty($_REQUEST['did'])) {
    $_REQUEST['did'] = 0;
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (!$jieqiConfigs['article']['makehtml']) {
    exit;
}
include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/package.php';
$package = new JieqiPackage($_REQUEST['aid']);
if ($_REQUEST['act'] != 'delete') {
    $ret = $package->loadOPF();
    if ($ret === false) {
        exit;
    }
}
@ignore_user_abort(true);
@set_time_limit(3600);
@session_write_close();
echo str_repeat(' ', 4096);
ob_flush();
flush();
$package->makeRead($_REQUEST['act'], $_REQUEST['cid'], $_REQUEST['did'], true);