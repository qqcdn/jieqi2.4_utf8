<?php

define('JIEQI_MODULE_NAME', 'obook');
if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
if (empty($_REQUEST['id']) && empty($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
$_REQUEST['aid'] = intval($_REQUEST['aid']);
jieqi_loadlang('obook', JIEQI_MODULE_NAME);
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
if (!empty($_REQUEST['id'])) {
    $obook = $obook_handler->get($_REQUEST['id']);
} else {
    $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['aid'], '='));
    $obook_handler->queryObjects($criteria);
    $obook = $obook_handler->getObject();
}
if (!is_object($obook) || $obook->getVar('display') != 0 && $jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
    jieqi_printfail($jieqiLang['obook']['obook_not_exists']);
} else {
    jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
    jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
    jieqi_getconfigs(JIEQI_MODULE_NAME, 'publisher');
    $jieqi_pagetitle = $obook->getVar('obookname');
    if ($obook->getVar('lastvolume') != '') {
        $jieqi_pagetitle .= '-' . $obook->getVar('lastvolume');
    }
    $jieqi_pagetitle .= '-' . $obook->getVar('lastchapter') . '-' . JIEQI_SITE_NAME;
    include_once JIEQI_ROOT_PATH . '/header.php';
    $obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
    $obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
    $jieqiTpl->assign('obook_static_url', $obook_static_url);
    $jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
    if (jieqi_getconfigs('article', 'configs')) {
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
    }
    include_once $jieqiModules['obook']['path'] . '/include/funobook.php';
    $obookvals = jieqi_obook_obookvars($obook);
    $jieqiTpl->assign_by_ref('obookvals', $obookvals);
    foreach ($obookvals as $k => $v) {
        $jieqiTpl->assign($k, $obookvals[$k]);
    }
    $jieqiTpl->assign('checkall', '<input type="checkbox" id="checkall" name="checkall" value="checkall" onclick="javascript: for (var i=0;i<this.form.elements.length;i++){ if (this.form.elements[i].name != \'checkkall\') this.form.elements[i].checked = form.checkall.checked; }">');
    $buyary = array();
    if (!empty($_SESSION['jieqiUserId'])) {
        include_once $jieqiModules['obook']['path'] . '/class/obuyinfo.php';
        $obuyinfo_handler = JieqiObuyinfoHandler::getInstance('JieqiObuyinfoHandler');
        $criteria = new CriteriaCompo(new Criteria('obookid', $obook->getVar('obookid')));
        $criteria->add(new Criteria('userid', $_SESSION['jieqiUserId']));
        $obuyinfo_handler->queryObjects($criteria);
        while ($obuyinfo = $obuyinfo_handler->getObject()) {
            $buyary[] = $obuyinfo->getVar('ochapterid', 'n');
        }
        unset($criteria);
    }
    $jieqiTpl->assign('url_buyobook', $obook_dynamic_url . '/buyobook.php');
    include_once $jieqiModules['obook']['path'] . '/class/ochapter.php';
    $ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('obookid', $obook->getVar('obookid'), '='));
    $criteria->add(new Criteria('chaptertype', 0, '='));
    $criteria->add(new Criteria('display', 0, '='));
    $criteria->setSort('ochapterid');
    $criteria->setOrder('DESC');
    $ochapter_handler->queryObjects($criteria);
    $isvip = 1;
    $ochapterrows = array();
    $k = 0;
    include_once $jieqiModules['obook']['path'] . '/include/funochapter.php';
    while ($v = $ochapter_handler->getObject()) {
        $ochapterrows[$k] = jieqi_obook_ochaptervars($v);
        if (in_array($v->getVar('ochapterid'), $buyary)) {
            $ochapterrows[$k]['isbuy'] = 1;
        } else {
            $ochapterrows[$k]['isbuy'] = 0;
        }
        $ochapterrows[$k]['checkid'] = $k;
        $k++;
    }
    $jieqiTpl->assign_by_ref('ochapterrows', $ochapterrows);
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/obookinfo.html';
    include_once JIEQI_ROOT_PATH . '/footer.php';
}