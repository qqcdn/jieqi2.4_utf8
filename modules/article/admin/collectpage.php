<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['adminconfig'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_loadlang('collect', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'collectsite');
if (empty($_REQUEST['config']) || !file_exists(JIEQI_ROOT_PATH . '/configs/article/site_' . $_REQUEST['config'] . '.php')) {
    jieqi_printfail($jieqiLang['article']['rule_not_exists']);
}
include_once JIEQI_ROOT_PATH . '/configs/article/site_' . $_REQUEST['config'] . '.php';
include_once $jieqiModules['article']['path'] . '/include/collectfunction.php';
switch ($_POST['act']) {
    case 'new':
        jieqi_checkpost();
        $tmpary = array();
        $tmpary['title'] = trim($_POST['title']);
        $tmpary['urlpage'] = trim($_POST['urlpage']);
        $tmpary['articleid'] = jieqi_collectptos($_POST['articleid']);
        $tmpary['startpageid'] = trim($_POST['startpageid']);
        $tmpary['nextpageid'] = jieqi_collectptos($_POST['nextpageid']);
        $_POST['maxpagenum'] = trim($_POST['maxpagenum']);
        if (is_numeric($_POST['maxpagenum'])) {
            $tmpary['maxpagenum'] = intval($_POST['maxpagenum']);
        } else {
            $tmpary['maxpagenum'] = '';
        }
        $jieqiCollect['listcollect'][] = $tmpary;
        jieqi_setconfigs('site_' . $_POST['config'], 'jieqiCollect', $jieqiCollect, JIEQI_MODULE_NAME);
        break;
    case 'del':
        jieqi_checkpost();
        if (isset($_REQUEST['cid']) && isset($jieqiCollect['listcollect'][$_REQUEST['cid']])) {
            unset($jieqiCollect['listcollect'][$_REQUEST['cid']]);
            jieqi_setconfigs('site_' . $_REQUEST['config'], 'jieqiCollect', $jieqiCollect, JIEQI_MODULE_NAME);
        }
        jieqi_jumppage($jieqiModules['article']['url'] . '/admin/collectpage.php', '', '', true);
        break;
}
include_once JIEQI_ROOT_PATH . '/admin/header.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$jieqiTpl->assign('sitename', $jieqiCollect['sitename']);
$jieqiTpl->assign('config', $_REQUEST['config']);
$jieqiTpl->assign_by_ref('collectrows', $jieqiCollect['listcollect']);
include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
$collect_form = new JieqiThemeForm($jieqiLang['article']['add_batch_collectrule'], 'collectnew', $article_static_url . '/admin/collectpage.php');
$collect_form->addElement(new JieqiFormLabel($jieqiLang['article']['collect_rule_note'], $jieqiLang['article']['page_rule_description']));
$collect_form->addElement(new JieqiFormText($jieqiLang['article']['collect_rule_name'], 'title', 60, 60, ''), true);
$collect_form->addElement(new JieqiFormText($jieqiLang['article']['collect_rule_url'], 'urlpage', 60, 250, ''), true);
$collect_form->addElement(new JieqiFormTextArea($jieqiLang['article']['collect_rule_articleid'], 'articleid', '', 5, 60), true);
$nextpageid = new JieqiFormTextArea($jieqiLang['article']['rule_next_pageid'], 'nextpageid', '', 5, 60);
$nextpageid->setDescription($jieqiLang['article']['rule_nextpage_note']);
$collect_form->addElement($nextpageid);
$collect_form->addElement(new JieqiFormText($jieqiLang['article']['rule_start_pageid'], 'startpageid', 60, 60, ''));
$collect_form->addElement(new JieqiFormText($jieqiLang['article']['rule_max_pagenum'], 'maxpagenum', 60, 10, ''));
$collect_form->addElement(new JieqiFormHidden('config', jieqi_htmlchars($_REQUEST['config'], ENT_QUOTES)));
$collect_form->addElement(new JieqiFormHidden('act', 'new'));
$collect_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
$collect_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['article']['rule_add_new'], 'submit'));
$jieqiTpl->assign('addnewtable', $collect_form->render(JIEQI_FORM_MAX));
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/collectpage.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';