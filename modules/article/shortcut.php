<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
if (empty($_REQUEST['acode']) || !preg_match('/^\\w+$/', $_REQUEST['acode'])) {
    $_REQUEST['acode'] = '';
}
jieqi_loadlang('article', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
} else {
    if ($article->getVar('display') != 0) {
        jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
        if (!jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
            if ($article->getVar('display') == 1) {
                jieqi_printfail($jieqiLang['article']['article_not_audit']);
            } else {
                jieqi_printfail($jieqiLang['article']['article_not_exists']);
            }
        }
    }
}
if (!isset($_REQUEST['page']) || $_REQUEST['page'] != 'index') {
    $_REQUEST['page'] = 'info';
}
$url = jieqi_geturl('article', 'article', $_REQUEST['id'], $_REQUEST['page'], $_REQUEST['acode']);
if (substr($url, 0, 4) != 'http') {
    $url = JIEQI_LOCAL_URL . $url;
}
if (is_file(JIEQI_ROOT_PATH . '/favicon.ico')) {
    $icostr = '' . "\r\n" . 'IconFile=' . JIEQI_LOCAL_URL . '/favicon.ico';
} else {
    $icostr = '';
}
$shortcut = '[InternetShortcut]' . "\r\n" . 'URL=' . $url . $icostr . '' . "\r\n" . 'IDList= ' . "\r\n" . '[{000214A0-0000-0000-C000-000000000046}] ' . "\r\n" . 'Prop3=19,2 ' . "\r\n" . '';
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . jieqi_headstr($article->getVar('articlename', 'n')) . '.url;');
echo $shortcut;