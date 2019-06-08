<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (isset($_REQUEST['aid'])) {
    $_REQUEST['aid'] = intval($_REQUEST['aid']);
}
if (isset($_REQUEST['acode']) && !preg_match('/^[a-z0-9_]+$/i', $_REQUEST['acode'])) {
    $_REQUEST['acode'] = '';
}
if ((empty($_REQUEST['aid']) || !is_numeric($_REQUEST['aid'])) && empty($_REQUEST['acode'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (empty($_REQUEST['aid']) && !empty($_REQUEST['acode'])) {
    jieqi_includedb();
    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    $sql = 'SELECT articleid FROM ' . jieqi_dbprefix('article_article') . ' WHERE articlecode = \'' . jieqi_dbslashes($_REQUEST['acode']) . '\' LIMIT 0, 1';
    $query->execute($sql);
    $row = $query->getRow();
    if (is_array($row)) {
        $_REQUEST['aid'] = intval($row['articleid']);
    } else {
        jieqi_loadlang('article', JIEQI_MODULE_NAME);
        jieqi_printfail($jieqiLang['article']['article_not_exists']);
    }
}
include_once JIEQI_ROOT_PATH . '/header.php';
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
    if (!empty($_REQUEST['cid'])) {
        $_REQUEST['cid'] = intval($_REQUEST['cid']);
        if (!$package->showChapter($_REQUEST['cid'])) {
            $package->showIndex();
        }
    } else {
        $package->showIndex();
    }
} else {
    jieqi_loadlang('article', JIEQI_MODULE_NAME);
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}