<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['newarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false);
@set_time_limit(0);
@session_write_close();
if (empty($_REQUEST['siteid']) || empty($_REQUEST['fromid']) || empty($_REQUEST['toid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (isset($_REQUEST[JIEQI_TOKEN_NAME]) && !isset($_POST[JIEQI_TOKEN_NAME])) {
    $_POST[JIEQI_TOKEN_NAME] = $_REQUEST[JIEQI_TOKEN_NAME];
}
jieqi_checkpost();
jieqi_loadlang('collect', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
include_once $jieqiModules['article']['path'] . '/include/collectarticle.php';
if ($retflag == 1) {
    include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
    jieqi_article_updateinfo(0);
    jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . $_REQUEST['toid'], LANG_DO_SUCCESS, $jieqiLang['article']['update_collect_success']);
} else {
    if ($retflag == 2) {
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('jieqi_contents', '<br />' . jieqi_msgbox(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['collect_no_update'], jieqi_geturl('article', 'article', $_REQUEST['toid'], 'info'), $article_static_url . '/articleclean.php?id=' . $_REQUEST['toid'] . '&collecturl=' . urlencode($article_static_url . '/admin/updatecollect.php?act=collect&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&siteid=' . $_REQUEST['siteid'] . '&fromid=' . $_REQUEST['fromid'] . '&toid=' . $_REQUEST['toid']), $article_static_url . '/admin/collect.php')) . '<br />');
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
    } else {
        if ($retflag == 4) {
            include_once JIEQI_ROOT_PATH . '/admin/header.php';
            $errchapter = '';
            foreach ($retchapinfo as $v) {
                $errchapter .= $v['fchapter'] . ' => ' . $v['tchapter'] . '<br />';
            }
            $jieqiTpl->assign('jieqi_contents', '<br />' . jieqi_msgbox(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['collect_cant_update'], $errchapter, $article_static_url . '/articlemanage.php?id=' . $_REQUEST['toid'], $article_static_url . '/articleclean.php?id=' . $_REQUEST['toid'] . '&collecturl=' . urlencode($article_static_url . '/admin/updatecollect.php?siteid=' . $_REQUEST['siteid'] . '&fromid=' . $_REQUEST['fromid'] . '&toid=' . $_REQUEST['toid']), $article_static_url . '/admin/collect.php')) . '<br />');
            include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        }
    }
}