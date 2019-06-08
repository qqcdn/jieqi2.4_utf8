<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../global.php';
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_loadlang('news', JIEQI_MODULE_NAME);
include_once $jieqiModules['news']['path'] . '/class/topic.php';
$topic_handler = JieqiNewstopicHandler::getInstance('JieqiNewstopicHandler');
$topic = $topic_handler->get($_REQUEST['id']);
if (!is_object($topic)) {
    jieqi_printfail($jieqiLang['news']['news_not_exist']);
} else {
    if ($topic->getVar('display', 'n') != 0 && $jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
        jieqi_printfail($jieqiLang['news']['news_not_audit']);
    }
}
$gourl = trim($topic->getVar('gourl', 'n'));
if (!empty($gourl)) {
    header('Location: ' . jieqi_headstr($gourl));
    exit;
}
$_REQUEST['sortid'] = $topic->getVar('sortid', 'n');
include_once JIEQI_ROOT_PATH . '/header.php';
include_once $jieqiModules['news']['path'] . '/include/funnews.php';
$news = jieqi_news_vars($topic);
include_once $jieqiModules['news']['path'] . '/class/content.php';
$content_handler = JieqiNewscontentHandler::getInstance('JieqiNewscontentHandler');
$content = $content_handler->get($_REQUEST['id']);
if (is_object($content)) {
    $news['contents'] = $content->getVar('contents', 'n');
}
$jieqiTpl->assign_by_ref('news', $news);
include_once JIEQI_ROOT_PATH . '/include/funsort.php';
$sortroutes = jieqi_sort_routes($jieqiSort['news'], $topic->getVar('sortid'));
$jieqiTpl->assign_by_ref('sortroutes', $sortroutes);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/templates/newsinfo.html';
if (!isset($jieqiConfigs['news']['visitstatnum']) || !empty($jieqiConfigs['news']['visitstatnum'])) {
    include_once $jieqiModules['news']['path'] . '/newsvisit.php';
}
include_once JIEQI_ROOT_PATH . '/footer.php';