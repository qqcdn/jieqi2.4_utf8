<?php

define('JIEQI_MODULE_NAME', 'article');
define('JIEQI_USE_GZIP', '0');
define('JIEQI_NOCONVERT_CHAR', '1');
@set_time_limit(600);
@ini_set('memory_limit', '32M');
require_once '../../global.php';
if (JIEQI_MODULE_VTYPE == '' || JIEQI_MODULE_VTYPE == 'Free') {
    exit;
}
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('article', 'action', 'jieqiAction');
$jieqiAction['article']['down']['earnscore'] = abs(intval($jieqiAction['article']['down']['earnscore']));
if (0 < $jieqiAction['article']['down']['earnscore']) {
    jieqi_checklogin();
    jieqi_loadlang('down', JIEQI_MODULE_NAME);
    if ($_SESSION['jieqiUserScore'] < $jieqiAction['article']['down']['earnscore']) {
        jieqi_printfail(sprintf($jieqiLang['article']['low_txtdown_score'], $jieqiAction['article']['down']['earnscore']));
    }
}
include_once $jieqiModules['article']['path'] . '/class/package.php';
$package = new JieqiPackage($_REQUEST['id']);
if ($package->loadOPF()) {
    if (0 < $jieqiAction['article']['down']['earnscore']) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        $users_handler->changeScore($_SESSION['jieqiUserId'], $jieqiAction['article']['down']['earnscore'], false, false);
        @session_write_close();
    }
    header('Content-type: text/plain');
    header('Accept-Ranges: bytes');
    if ($_REQUEST['fname'] == 'id') {
        header('Content-Disposition: attachment; filename=' . $_REQUEST['id'] . '.txt');
    } else {
        header('Content-Disposition: attachment; filename=' . jieqi_headstr(jieqi_htmlstr($package->metas['articlename'])) . '.txt');
    }
    $br = "\r\n";
    if (!empty($jieqiConfigs['article']['txtarticlehead'])) {
        echo $jieqiConfigs['article']['txtarticlehead'] . $br . $br;
    }
    echo '《' . $package->metas['articlename'] . '》' . $br;
    $volume = '';
    foreach ($package->chapters as $k => $chapter) {
        if ($chapter['chaptertype'] == 1) {
            $volume = $chapter['chaptername'];
        } else {
            echo $br . $br . $volume . ' ' . $chapter['chaptername'] . $br . $br;
            echo jieqi_get_achapterc(array('articleid' => $package->id, 'articlecode' => $package->metas['articlecode'], 'chapterid' => intval($chapter['chapterid']), 'isvip' => intval($chapter['isvip']), 'chaptertype' => 0, 'display' => intval($chapter['display']), 'getformat' => 'url'));
            ob_flush();
            flush();
        }
    }
    if (!empty($jieqiConfigs['article']['txtarticlefoot'])) {
        echo $jieqiConfigs['article']['txtarticlefoot'];
    }
} else {
    jieqi_loadlang('article', JIEQI_MODULE_NAME);
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}