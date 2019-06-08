<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['forum']['manageforum'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_POST['act'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
} else {
    jieqi_checkpost();
}
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
$_POST['cattitle'] = trim($_POST['cattitle']);
$errtext = '';
if (empty($_POST['cattitle'])) {
    $errtext .= $jieqiLang['forum']['need_forumcat_name'] . '<br />';
}
if (empty($_POST['catorder'])) {
    $_POST['catorder'] = 0;
}
if (empty($errtext)) {
    include_once JIEQI_ROOT_PATH . '/admin/header.php';
    include_once $jieqiModules['forum']['path'] . '/class/forumcat.php';
    $forumcat_handler = JieqiForumcatHandler::getInstance('JieqiForumcatHandler');
    $newforumcat = $forumcat_handler->create();
    $newforumcat->setVar('cattitle', $_POST['cattitle']);
    $newforumcat->setVar('catorder', $_POST['catorder']);
    if (!$forumcat_handler->insert($newforumcat)) {
        jieqi_printfail($jieqiLang['forum']['add_forumcat_failure']);
    } else {
        include_once $jieqiModules['forum']['path'] . '/include/upforumcatset.php';
        jieqi_jumppage($jieqiModules['forum']['url'] . '/admin/forumlist.php', LANG_DO_SUCCESS, $jieqiLang['forum']['add_forumcat_success']);
    }
} else {
    jieqi_printfail($errtext);
}