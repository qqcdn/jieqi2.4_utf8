<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_checklogin();
if (empty($_REQUEST['oid']) || !is_numeric($_REQUEST['oid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('buy', 'obook');
include_once $jieqiModules['obook']['path'] . '/include/funbuy.php';
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
$obook = $obook_handler->get($_REQUEST['oid']);
if (!is_object($obook) || $obook->getVar('display') != 0) {
    jieqi_printfail($jieqiLang['obook']['obook_not_sale']);
}
$saleprice = intval($obook->getVar('saleprice', 'n'));
jieqi_getconfigs('article', 'configs');
if (empty($jieqiConfigs['article']['wholebuy']) || $saleprice <= 0) {
    jieqi_printfail($jieqiLang['obook']['obook_not_wholesale']);
}
jieqi_getconfigs('obook', 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$users = $users_handler->get($_SESSION['jieqiUserId']);
if (!is_object($users)) {
    jieqi_printfail($jieqiLang['obook']['need_user_login']);
}
$articleid = intval($obook->getVar('articleid', 'n'));
$obookid = intval($obook->getVar('obookid', 'n'));
$obookname = $obook->getVar('obookname');
$usermoney = $users->getEmoney();
if ($usermoney['egold'] < $saleprice) {
    jieqi_printfail(sprintf($jieqiLang['obook']['article_money_notenough'], $obookname, $saleprice . ' ' . JIEQI_EGOLD_NAME, $usermoney['egold'] . ' ' . JIEQI_EGOLD_NAME, $jieqiModules['pay']['url'] . '/buyegold.php?jumpurl=' . urlencode(jieqi_geturl('article', 'article', $articleid, 'index'))));
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'buy':
        jieqi_checkpost();
        $obuyinfo = jieqi_obook_getobuyinfo(0, $obookid);
        if (is_object($obuyinfo)) {
            jieqi_printfail(sprintf($jieqiLang['obook']['article_has_buyed'], $obookname, jieqi_geturl('article', 'article', $articleid, 'index')));
        }
        jieqi_obook_wholebuy($obook, $users);
        header('Location: ' . jieqi_headstr(jieqi_geturl('article', 'article', $articleid, 'index')));
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('obook_static_url', $obook_static_url);
        $jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
        $jieqiTpl->assign('articleid', $articleid);
        $jieqiTpl->assign('obookid', $obookid);
        $jieqiTpl->assign('oid', $_REQUEST['oid']);
        $jieqiTpl->assign('url_buywhole', $obook_dynamic_url . '/buywhole.php');
        $jieqiTpl->assign('url_obookinfo', $obook_dynamic_url . '/obookinfo.php?id=' . $obook->getVar('obookid', 'n'));
        $jieqiTpl->assign('url_buyegold', $jieqiModules['pay']['url'] . '/buyegold.php?jumpurl=' . urlencode(jieqi_geturl('article', 'article', $articleid, 'index')));
        $jieqiTpl->assign('obookname', $obookname);
        $jieqiTpl->assign('saleprice', $saleprice);
        $jieqiTpl->assign('useregold', $usermoney['egold']);
        $jieqiTpl->assign('useresilver', $usermoney['esilver']);
        $jieqiTpl->assign('useremoney', $usermoney['egold']);
        $jieqiTpl->assign('usermoney', $usermoney);
        $jieqiTpl->assign('username', $users->getVar('uname'));
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/buywhole.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}