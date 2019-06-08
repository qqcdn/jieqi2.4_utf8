<?php

$logstart = explode(' ', microtime());
define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_checklogin();
if (empty($_REQUEST['cid'])) {
    exit;
}
$_REQUEST['cid'] = intval($_REQUEST['cid']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if ($jieqiConfigs['obook']['obkimagetype'] != 'txt') {
    exit;
}
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
if (isset($_SESSION['jieqiVisitedObooks'])) {
    $arysession = jieqi_unserialize($_SESSION['jieqiVisitedObooks']);
} else {
    $arysession = array();
}
if (!is_array($arysession)) {
    $arysession = array();
}
if (!isset($arysession[$_REQUEST['cid']]) || $arysession[$_REQUEST['cid']] != 1) {
    exit;
}
@session_write_close();
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
include_once $jieqiModules['obook']['path'] . '/class/ocontent.php';
$content_handler = JieqiOcontentHandler::getInstance('JieqiOcontentHandler');
$criteria = new CriteriaCompo(new Criteria('ochapterid', $_REQUEST['cid']));
$criteria->setLimit(1);
$content_handler->queryObjects($criteria);
unset($criteria);
$content = $content_handler->getObject();
if (!is_object($content)) {
    exit;
} else {
    $outstr = $content->getVar('ocontent');
    if (!empty($jieqiConfigs['obook']['obookreadhead'])) {
        $outstr = jieqi_htmlstr($jieqiConfigs['obook']['obookreadhead']) . '<br />' . $outstr;
    }
    if (!empty($jieqiConfigs['obook']['obookreadfoot'])) {
        $outstr .= '<br />' . jieqi_htmlstr($jieqiConfigs['obook']['obookreadfoot']);
    }
    $outstr = jieqi_htmlclickable($outstr);
    $outstr = str_replace(array("\r", "\n"), '', $outstr);
    $contentrows = explode('<br /><br />', $outstr);
    $arynum = count($contentrows);
    $k = 0;
    for ($i = 0; $i < $arynum; $i++) {
        if ($contentrows[$i] == '') {
            unset($contentrows[$i]);
        } else {
            $k++;
            $contentrows[$i] = '<p>' . $contentrows[$i] . '</p>';
        }
    }
    include_once JIEQI_ROOT_PATH . '/header.php';
    $jieqiTpl->assign_by_ref('contentrows', $contentrows);
    $jieqiTpl->setCaching(0);
    $jieqiTpl->display($jieqiModules['obook']['path'] . '/templates/obooktext.html');
    jieqi_freeresource();
}