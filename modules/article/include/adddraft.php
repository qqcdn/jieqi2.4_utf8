<?php

if (!defined('JIEQI_ROOT_PATH')) {
    exit;
}
include_once $jieqiModules['article']['path'] . '/class/draft.php';
$draft_handler = JieqiDraftHandler::getInstance('JieqiDraftHandler');
$newDraft = $draft_handler->create();
$draftwords = jieqi_strwords($_POST['chaptercontent']);
$newDraft->setVar('articleid', $_POST['articleid']);
$newDraft->setVar('articlename', $_POST['articlename']);
$newDraft->setVar('volumeid', intval($_POST['volumeid']));
$newDraft->setVar('volumename', $_POST['volumename']);
$newDraft->setVar('chapterid', 0);
$newDraft->setVar('chapterorder', 0);
$newDraft->setVar('chaptertype', 0);
$newDraft->setVar('isvip', intval($_POST['isvip']));
$newDraft->setVar('obookid', $_POST['obookid']);
if (!empty($_SESSION['jieqiUserId'])) {
    $newDraft->setVar('posterid', $_SESSION['jieqiUserId']);
    $newDraft->setVar('poster', $_SESSION['jieqiUserName']);
} else {
    $newDraft->setVar('posterid', 0);
    $newDraft->setVar('poster', '');
}
$newDraft->setVar('postdate', JIEQI_NOW_TIME);
$newDraft->setVar('lastupdate', JIEQI_NOW_TIME);
if (isset($_POST['uptiming']) && $_POST['uptiming'] == 1) {
    $newDraft->setVar('ispub', 1);
    $newDraft->setVar('pubdate', $_POST['pubtime']);
} else {
    $newDraft->setVar('ispub', 0);
    $newDraft->setVar('pubdate', 0);
}
$newDraft->setVar('chaptername', $_POST['chaptername']);
$newDraft->setVar('chaptercontent', $_POST['chaptercontent']);
$newDraft->setVar('words', $draftwords);
if (!isset($customprice)) {
    $customprice = false;
}
if (!isset($_POST['saleprice']) || !is_numeric($_POST['saleprice'])) {
    $_POST['saleprice'] = -1;
} else {
    $_POST['saleprice'] = intval($_POST['saleprice']);
    if ($_POST['saleprice'] < 0 || 0 < $_POST['saleprice'] && !$customprice) {
        $_POST['saleprice'] = -1;
    }
}
if ($_POST['isvip'] <= 0) {
    $_POST['saleprice'] = -1;
}
$newDraft->setVar('saleprice', $_POST['saleprice']);
$newDraft->setVar('note', '');
$newDraft->setVar('attachment', '');
$newDraft->setVar('isimage', 0);
$newDraft->setVar('power', 0);
if ($_POST['needupaudit']) {
    $newDraft->setVar('display', 1);
} else {
    $newDraft->setVar('display', 0);
}
$newDraft->setVar('draftflag', 0);
if (!$draft_handler->insert($newDraft)) {
    jieqi_printfail($jieqiLang['article']['draft_add_failure']);
} else {
    if (!empty($_POST['draftid'])) {
        $draft_handler->delete(intval($_POST['draftid']));
    }
}