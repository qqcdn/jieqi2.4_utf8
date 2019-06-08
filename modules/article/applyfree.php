<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
if (empty($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('ltfree', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$_REQUEST['aid'] = intval($_REQUEST['aid']);
$article = $article_handler->get($_REQUEST['aid']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$canedit = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($article->getVar('authorid') == $tmpvar || $article->getVar('posterid') == $tmpvar || $article->getVar('agentid') == $tmpvar)) {
        $canedit = true;
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['article']['noper_article_applyfree']);
}
if (intval($article->getVar('isvip', 'n')) == 0) {
    jieqi_printfail($jieqiLang['article']['only_vip_applyfree']);
}
if (intval($article->getVar('freestart', 'n')) <= JIEQI_NOW_TIME && JIEQI_NOW_TIME <= intval($article->getVar('freeend', 'n'))) {
    jieqi_printfail($jieqiLang['article']['article_in_free']);
}
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
if (isset($jieqiPower['article']['ltfreeaudit'])) {
    $noaudit = jieqi_checkpower($jieqiPower['article']['ltfreeaudit'], $jieqiUsersStatus, $jieqiUsersGroup, true);
} else {
    $noaudit = true;
}
if (!$noaudit) {
    $sql = 'SELECT applyid FROM ' . jieqi_dbprefix('article_applyfree') . ' WHERE articleid = ' . $_REQUEST['aid'] . ' AND applyflag = 1 LIMIT 0, 1';
    $res = $query->execute($sql);
    $ret = $query->getRow($res);
    if (is_array($ret)) {
        jieqi_printfail($jieqiLang['article']['article_in_applyfree']);
    }
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (!isset($jieqiConfigs['article']['maxfreedays']) || !is_numeric($jieqiConfigs['article']['maxfreedays'])) {
    $jieqiConfigs['article']['maxfreedays'] = 10;
} else {
    $jieqiConfigs['article']['maxfreedays'] = intval($jieqiConfigs['article']['maxfreedays']);
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
switch ($_POST['act']) {
    case 'apply':
        jieqi_checkpost();
        $errtext = '';
        $_POST = jieqi_funtoarray('trim', $_POST);
        if (!isset($_POST['freestart']) && !empty($_POST['fsyear']) && !empty($_POST['fsmonth']) && !empty($_POST['fsday'])) {
            $_POST['fshour'] = isset($_POST['fshour']) ? intval(trim($_POST['fshour'])) : 0;
            $_POST['fsminute'] = isset($_POST['fsminute']) ? intval(trim($_POST['fsminute'])) : 0;
            $_POST['fssecond'] = isset($_POST['fssecond']) ? intval(trim($_POST['fssecond'])) : 0;
            $_POST['freestart'] = strval(intval(trim($_POST['fsyear']))) . '-' . sprintf('%02d', intval(trim($_POST['fsmonth']))) . '-' . sprintf('%02d', intval(trim($_POST['fsday']))) . ' ' . sprintf('%02d', intval(trim($_POST['fshour']))) . ':' . sprintf('%02d', intval(trim($_POST['fsminute']))) . ':' . sprintf('%02d', intval(trim($_POST['fssecond'])));
        }
        if (empty($_POST['freestart'])) {
            $errtext .= $jieqiLang['article']['need_free_starttime'] . '<br />';
        }
        $_POST['freedays'] = intval($_POST['freedays']);
        if ($_POST['freedays'] <= 0) {
            $errtext .= $jieqiLang['article']['freedays_over_zero'] . '<br />';
        } else {
            if ($jieqiConfigs['article']['maxfreedays'] < $_POST['freedays']) {
                $errtext .= sprintf($jieqiLang['article']['freedays_over_max'], $jieqiConfigs['article']['maxfreedays']) . '<br />';
            }
        }
        if (empty($errtext)) {
            $fieldrows = array();
            $fieldrows['siteid'] = JIEQI_SITE_ID;
            $fieldrows['applytime'] = JIEQI_NOW_TIME;
            $fieldrows['applyuid'] = $_SESSION['jieqiUserId'];
            $fieldrows['applyname'] = $_SESSION['jieqiUserName'];
            $fieldrows['articleid'] = $article->getVar('articleid', 'n');
            $fieldrows['articlename'] = $article->getVar('articlename', 'n');
            $fieldrows['freestart'] = strtotime($_POST['freestart']);
            $fieldrows['freeend'] = $fieldrows['freestart'] + $_POST['freedays'] * 3600 * 24;
            $fieldrows['freedays'] = $_POST['freedays'];
            $fieldrows['applynote'] = '';
            $fieldrows['replynote'] = '';
            $fieldrows['applyflag'] = $noaudit ? 0 : 1;
            $sql = $query->makeupsql(jieqi_dbprefix('article_applyfree'), $fieldrows, 'INSERT');
            $ret = $query->execute($sql);
            if (!$ret) {
                jieqi_printfail($jieqiLang['article']['applyfree_insert_dberror']);
            }
            if ($noaudit) {
                $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET freestart = ' . $fieldrows['freestart'] . ', freeend = ' . $fieldrows['freeend'] . ' WHERE articleid = ' . $_REQUEST['aid'];
                $query->execute($sql);
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['applyfree_do_success']);
            } else {
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['applyfree_submit_success']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
        $articlevals = jieqi_article_vars($article, true);
        $jieqiTpl->assign_by_ref('articlevals', $articlevals);
        foreach ($articlevals as $k => $v) {
            $jieqiTpl->assign($k, $articlevals[$k]);
        }
        $fstime = mktime(0, 0, 0, intval(date('m')), intval(date('d')) + 1, intval(date('Y')));
        $jieqiTpl->assign('fstime', $fstime);
        $jieqiTpl->assign('maxfreedays', $jieqiConfigs['article']['maxfreedays']);
        $jieqiTpl->assign('aid', $_REQUEST['aid']);
        $jieqiTpl->assign('authorarea', 1);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/applyfree.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}