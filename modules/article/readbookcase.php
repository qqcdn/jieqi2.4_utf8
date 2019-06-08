<?php

function jieqi_upbookcasevisit()
{
    global $jieqiModules;
    if (!empty($_REQUEST['bid']) && is_numeric($_REQUEST['bid'])) {
        include_once $jieqiModules['article']['path'] . '/class/bookcase.php';
        $bookcase_handler = JieqiBookcaseHandler::getInstance('JieqiBookcaseHandler');
        $bookcase_handler->execute('UPDATE ' . jieqi_dbprefix('article_bookcase') . ' SET lastvisit=' . JIEQI_NOW_TIME . ' WHERE caseid=' . intval($_REQUEST['bid']) . ' AND userid = ' . intval($_SESSION['jieqiUserId']));
    }
}
define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['aid'] = intval($_REQUEST['aid']);
if (empty($_REQUEST['acode']) || !preg_match('/^\\w+$/', $_REQUEST['acode'])) {
    $_REQUEST['acode'] = '';
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if (!empty($_REQUEST['cid'])) {
    $_REQUEST['cid'] = intval($_REQUEST['cid']);
    include_once $jieqiModules['article']['path'] . '/class/chapter.php';
    $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
    $chapter = $chapter_handler->get($_REQUEST['cid']);
    if (is_object($chapter)) {
        if (!isset($jieqiConfigs['article']['visitstatnum']) || !empty($jieqiConfigs['article']['visitstatnum'])) {
            include_once $jieqiModules['article']['path'] . '/articlevisit.php';
        }
        header('Location: ' . jieqi_headstr(jieqi_geturl('article', 'chapter', $_REQUEST['cid'], $_REQUEST['aid'], $chapter->getVar('isvip', 'n'), $_REQUEST['acode'])));
        jieqi_upbookcasevisit();
        exit;
    } else {
        $_REQUEST['indexflag'] = 1;
    }
}
if (empty($_REQUEST['indexflag'])) {
    header('Location: ' . jieqi_headstr(jieqi_geturl('article', 'article', $_REQUEST['aid'], 'info', $_REQUEST['acode'])));
} else {
    if (!isset($jieqiConfigs['article']['visitstatnum']) || !empty($jieqiConfigs['article']['visitstatnum'])) {
        include_once $jieqiModules['article']['path'] . '/articlevisit.php';
    }
    header('Location: ' . jieqi_headstr(jieqi_geturl('article', 'article', $_REQUEST['aid'], 'index', $_REQUEST['acode'])));
}
jieqi_upbookcasevisit();