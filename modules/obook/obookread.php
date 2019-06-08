<?php

$logstart = explode(' ', microtime());
define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
if (empty($_REQUEST['aid']) && empty($_REQUEST['oid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_checklogin();
if (isset($_REQUEST['oid'])) {
    $_REQUEST['oid'] = intval($_REQUEST['oid']);
}
if (isset($_REQUEST['aid'])) {
    $_REQUEST['aid'] = intval($_REQUEST['aid']);
}
if (isset($_REQUEST['cid'])) {
    $_REQUEST['cid'] = intval($_REQUEST['cid']);
}
if (empty($_REQUEST['acode']) || !preg_match('/^\\w+$/', $_REQUEST['acode'])) {
    $_REQUEST['acode'] = '';
}
jieqi_loadlang('obook', 'obook');
jieqi_loadlang('article', 'article');
jieqi_getconfigs('obook', 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
if (empty($_REQUEST['aid']) && !empty($_REQUEST['oid'])) {
    include_once $jieqiModules['obook']['path'] . '/class/obook.php';
    $obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
    $obook = $obook_handler->get($_REQUEST['oid']);
    if (is_object($obook)) {
        $_REQUEST['aid'] = intval($obook->getVar('articleid', 'n'));
    } else {
        jieqi_printfail($jieqiLang['article']['article_not_exists']);
    }
}
$gourl = '';
if ($_REQUEST['page'] == 'index') {
    $gourl = jieqi_geturl('article', 'article', $_REQUEST['aid'], 'index', $_REQUEST['acode']);
}
if (!empty($_REQUEST['cid']) && !empty($_REQUEST['page'])) {
    include_once $jieqiModules['article']['path'] . '/class/package.php';
    $package = new JieqiPackage($_REQUEST['aid']);
    if (!$package->loadOPF()) {
        jieqi_printfail($jieqiLang['article']['article_not_exists']);
    }
    $prechapter = array();
    $nexchapter = array();
    $searchflag = false;
    foreach ($package->chapters as $chapter) {
        if ($chapter['chaptertype'] == 0) {
            if ($chapter['chapterid'] == $_REQUEST['cid']) {
                $searchflag = true;
            } else {
                if ($searchflag == false) {
                    $prechapter = $chapter;
                } else {
                    if (empty($nexchapter)) {
                        $nexchapter = $chapter;
                    }
                }
            }
            if (!empty($nexchapter)) {
                break;
            }
        }
    }
    if ($_REQUEST['page'] == 'previous' || $_REQUEST['page'] == 'preview') {
        if (!empty($prechapter)) {
            $gourl = jieqi_geturl('article', 'chapter', $prechapter['chapterid'], $_REQUEST['aid'], $prechapter['isvip'], $_REQUEST['acode']);
        }
    } else {
        if (!empty($nexchapter)) {
            $gourl = jieqi_geturl('article', 'chapter', $nexchapter['chapterid'], $_REQUEST['aid'], $nexchapter['isvip'], $_REQUEST['acode']);
        }
    }
}
if ($gourl == '') {
    $gourl = jieqi_geturl('article', 'article', $_REQUEST['aid'], 'index', $_REQUEST['acode']);
}
header('Location: ' . jieqi_headstr($gourl));