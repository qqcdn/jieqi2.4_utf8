<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_loadlang('message', JIEQI_MODULE_NAME);
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    jieqi_printfail($jieqiLang['system']['message_no_exists']);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
include_once JIEQI_ROOT_PATH . '/class/message.php';
$message_handler = JieqiMessageHandler::getInstance('JieqiMessageHandler');
$message = $message_handler->get($_REQUEST['id']);
if (!$message) {
    jieqi_printfail($jieqiLang['system']['message_no_exists']);
}
if ($message->getVar('fromid') != $_SESSION['jieqiUserId'] && $message->getVar('toid') != $_SESSION['jieqiUserId']) {
    jieqi_printfail($jieqiLang['system']['message_no_exists']);
}
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/messagedetail.html';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTpl->assign('id', $_REQUEST['id']);
$messagevals = jieqi_query_rowvars($message);
$messagevals['content'] = jieqi_htmlclickable($messagevals['content']);
$jieqiTpl->assign_by_ref('messagevals', $messagevals);
if ($message->getVar('toid') == $_SESSION['jieqiUserId']) {
    $jieqiTpl->assign('box', 'inbox');
} else {
    $jieqiTpl->assign('box', 'outbox');
}
if ($message->getVar('isread') != 1 && $message->getVar('toid') == $_SESSION['jieqiUserId']) {
    $message->setVar('isread', '1');
    $message_handler->insert($message);
}
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/footer.php';