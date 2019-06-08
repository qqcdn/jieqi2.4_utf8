<?php

function checkSiteUrl($url)
{
    if ($url == '') {
        return false;
    }
    $sourceStr = @file_get_contents($url);
    preg_match_all('@<title>(.*?)</title>@isx', $sourceStr, $tpl, PREG_PATTERN_ORDER);
    if (trim(@implode('', $tpl[1])) != '') {
        return true;
    } else {
        return false;
    }
}
function checkSiteLogo($url)
{
    if ($url == '' || !in_array(strtolower(strrchr($url, '.')), array('.jpg', '.gif', '.swf', '.jpeg', '.bmp'))) {
        return false;
    }
    $handle = @fopen($url, 'r');
    if ($handle) {
        return true;
    } else {
        return false;
    }
}
define('JIEQI_MODULE_NAME', 'link');
require_once '../../../global.php';
jieqi_checklogin();
if ($jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
    jieqi_printfail(LANG_NEED_ADMIN);
}
jieqi_loadlang('link', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/admin/header.php';
if ($_POST['act']) {
    jieqi_checkpost();
    $linktype = 0;
    if (trim($_REQUEST['logo']) != '' && !checksitelogo($_REQUEST['logo'])) {
        jieqi_printfail($jieqiLang['link']['logo_not_exists']);
    } else {
        if (trim($_REQUEST['logo']) != '') {
            $linktype = 1;
        }
    }
    include_once $jieqiModules['link']['path'] . '/class/link.php';
    $link_handler = JieqiLinkHandler::getInstance('JieqiLinkHandler');
    switch ($_POST['act']) {
        case 'add':
            $newLink = $link_handler->create();
            $link_failure = $jieqiLang['link']['add_link_failure'];
            $link_success = $jieqiLang['link']['add_link_success'];
            $link_url = $jieqiModules['link']['url'] . '/admin/addlink.php';
            break;
        case 'edit':
            $newLink = $link_handler->get($_REQUEST['id']);
            if (!is_object($newLink)) {
                jieqi_printfail($jieqiLang['link']['link_not_exists']);
            }
            $newLink->unsetNew();
            $link_failure = $jieqiLang['link']['link_edit_failure'];
            $link_success = $jieqiLang['link']['link_edit_success'];
            $link_url = $jieqiModules['link']['url'] . '/admin/link.php';
            break;
    }
    $newLink->setVar('name', $_REQUEST['name']);
    $newLink->setVar('namecolor', $_REQUEST['namecolor']);
    $newLink->setVar('linktype', $linktype);
    $newLink->setVar('url', $_REQUEST['url']);
    $newLink->setVar('logo', $_REQUEST['logo']);
    $newLink->setVar('introduce', $_REQUEST['introduce']);
    $newLink->setVar('userid', $_SESSION['jieqiUserId']);
    $newLink->setVar('username', $_SESSION['jieqiUserUname']);
    $newLink->setVar('mastername', $_REQUEST['mastername']);
    $newLink->setVar('mastertell', $_REQUEST['mastertell']);
    $newLink->setVar('listorder', $_REQUEST['listorder']);
    $newLink->setVar('passed', 1);
    $newLink->setVar('addtime', time());
    $newLink->setVar('hits', 0);
    if (!$link_handler->insert($newLink)) {
        jieqi_printfail($link_failure);
    } else {
        jieqi_jumppage($link_url, LANG_DO_SUCCESS, $link_success);
    }
}
if ($_REQUEST['id']) {
    include_once $jieqiModules['link']['path'] . '/class/link.php';
    $link_handler = JieqiLinkHandler::getInstance('JieqiLinkHandler');
    $linkObject = $link_handler->get($_REQUEST['id']);
    if (is_object($linkObject)) {
        $link['linkid'] = $linkObject->getVar('linkid');
        $link['name'] = $linkObject->getVar('name');
        $link['namecolor'] = $linkObject->getVar('namecolor');
        $link['url'] = $linkObject->getVar('url');
        $link['logo'] = $linkObject->getVar('logo');
        $link['introduce'] = $linkObject->getVar('introduce');
        $link['mastername'] = $linkObject->getVar('mastername');
        $link['mastertell'] = $linkObject->getVar('mastertell');
        $link['listorder'] = $linkObject->getVar('listorder');
        $jieqiTpl->assign('link', $link);
    } else {
        jieqi_printfail($jieqiLang['link']['link_not_exists']);
    }
}
$jieqiTpl->setCaching(0);
$jieqiTpl->assign('jieqi_lang', $jieqiLang['link']);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['link']['path'] . '/templates/admin/addlink.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';