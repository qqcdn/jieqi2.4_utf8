<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false);
@set_time_limit(0);
@session_write_close();
jieqi_loadlang('collect', JIEQI_MODULE_NAME);
if (!isset($_REQUEST['siteid']) || !is_numeric($_REQUEST['siteid'])) {
    jieqi_printfail($jieqiLang['article']['need_collect_siteid']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'collectsite');
if (!array_key_exists($_REQUEST['siteid'], $jieqiCollectsite) || $jieqiCollectsite[$_REQUEST['siteid']]['enable'] != '1') {
    jieqi_printfail($jieqiLang['article']['collect_notsupport_site']);
}
if (!file_exists(JIEQI_ROOT_PATH . '/configs/article/site_' . $jieqiCollectsite[$_REQUEST['siteid']]['config'] . '.php')) {
    jieqi_printfail($jieqiLang['article']['rule_not_exists']);
}
include_once JIEQI_ROOT_PATH . '/configs/article/site_' . $jieqiCollectsite[$_REQUEST['siteid']]['config'] . '.php';
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
jieqi_getconfigs('article', 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if (!isset($_REQUEST['act'])) {
    $_REQUEST['act'] = 'show';
}
if (isset($_REQUEST[JIEQI_TOKEN_NAME]) && !isset($_POST[JIEQI_TOKEN_NAME])) {
    $_POST[JIEQI_TOKEN_NAME] = $_REQUEST[JIEQI_TOKEN_NAME];
}
switch ($_REQUEST['act']) {
    case 'collect':
        jieqi_checkpost();
        include_once $jieqiModules['article']['path'] . '/include/collectfunction.php';
        $_REQUEST['collectname'] = intval($_REQUEST['collectname']);
        if (!isset($jieqiCollect['listcollect'][$_REQUEST['collectname']])) {
            jieqi_printfail($jieqiLang['article']['rule_not_exists']);
        }
        if (empty($_REQUEST['collectpagenum']) || !is_numeric($_REQUEST['collectpagenum'])) {
            $_REQUEST['collectpagenum'] = 1;
        }
        if (!empty($_REQUEST['startpageid'])) {
            $startpageid = $_REQUEST['startpageid'];
        } else {
            $startpageid = trim($jieqiCollect['listcollect'][$_REQUEST['collectname']]['startpageid']);
        }
        if (!empty($_REQUEST['maxpagenum']) && is_numeric($_REQUEST['maxpagenum'])) {
            $maxpagenum = intval($_REQUEST['maxpagenum']);
        } else {
            $maxpagenum = intval($jieqiCollect['listcollect'][$_REQUEST['collectname']]['maxpagenum']);
        }
        $url = str_replace('<{pageid}>', $startpageid, $jieqiCollect['listcollect'][$_REQUEST['collectname']]['urlpage']);
        $colary = array('repeat' => 2, 'referer' => $jieqiCollect['referer'], 'proxy_host' => $jieqiCollect['proxy_host'], 'proxy_port' => $jieqiCollect['proxy_port'], 'proxy_user' => $jieqiCollect['proxy_user'], 'proxy_pass' => $jieqiCollect['proxy_pass']);
        if (!empty($jieqiCollect['pagecharset'])) {
            $colary['charset'] = $jieqiCollect['pagecharset'];
        }
        $source = jieqi_urlcontents($url, $colary);
        if (empty($source)) {
            jieqi_printfail(sprintf($jieqiLang['article']['collect_url_failure'], $url, $url));
        }
        $pregstr = jieqi_collectstoe($jieqiCollect['listcollect'][$_REQUEST['collectname']]['articleid']);
        if (!empty($pregstr)) {
            $matchvar = jieqi_cmatchall($pregstr, $source);
        }
        if (empty($matchvar)) {
            jieqi_printfail($jieqiLang['article']['parse_articleid_failure']);
        }
        if (is_array($matchvar)) {
            $aidsary = $matchvar;
        } else {
            $aidsary = array();
        }
        $nextpageid = '';
        if ($jieqiCollect['listcollect'][$_REQUEST['collectname']]['nextpageid'] == '++') {
            $nextpageid = intval($startpageid) + 1;
        } else {
            $pregstr = jieqi_collectstoe($jieqiCollect['listcollect'][$_REQUEST['collectname']]['nextpageid']);
            if (!empty($pregstr)) {
                $matchvar = jieqi_cmatchone($pregstr, $source);
            }
            if (!empty($matchvar)) {
                $nextpageid = trim(jieqi_textstr($matchvar));
            }
        }
        include_once $jieqiModules['article']['path'] . '/class/article.php';
        include_once $jieqiModules['article']['path'] . '/class/package.php';
        $aid = 0;
        echo str_repeat(' ', 4096);
        echo sprintf($jieqiLang['article']['page_collect_doing'], $jieqiCollect['sitename'], $jieqiCollect['listcollect'][$_REQUEST['collectname']]['title'], $_REQUEST['collectpagenum'], count($aidsary));
        ob_flush();
        flush();
        $cpoint = 1;
        foreach ($aidsary as $v) {
            $aid = trim($v);
            echo sprintf($jieqiLang['article']['page_checkid_doing'], $cpoint, $aid);
            ob_flush();
            flush();
            $cpoint++;
            include $jieqiModules['article']['path'] . '/include/updateone.php';
        }
        if ($nextpageid == '' || $maxpagenum <= $_REQUEST['collectpagenum']) {
            jieqi_article_updateinfo(0);
            jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['batch_collect_success']);
            exit;
        } else {
            ${$_REQUEST}['startid']++;
            $url = 'pagecollect.php?act=collect&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&siteid=' . $_REQUEST['siteid'] . '&collectname=' . $_REQUEST['collectname'] . '&startpageid=' . urlencode($nextpageid) . '&maxpagenum=' . $maxpagenum . '&collectpagenum=' . ($_REQUEST['collectpagenum'] + 1) . '&notaddnew=' . urlencode($_REQUEST['notaddnew']);
            $showinfo = sprintf($jieqiLang['article']['page_collect_next'], $_REQUEST['collectpagenum'], $maxpagenum);
            echo sprintf($jieqiLang['article']['page_collect_html'], JIEQI_CHAR_SET, $showinfo, $url, $url);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
        $collect_form = new JieqiThemeForm($jieqiLang['article']['batch_collect_usepage'], 'frmcollect', $article_static_url . '/admin/pagecollect.php');
        $collect_form->addElement(new JieqiFormLabel($jieqiLang['article']['collect_siteid'], $jieqiCollect['sitename']));
        $collectname = new JieqiFormSelect($jieqiLang['article']['collect_name'], 'collectname', '0');
        if (is_array($jieqiCollect['listcollect'])) {
            foreach ($jieqiCollect['listcollect'] as $k => $v) {
                $collectname->addOption($k, $v['title']);
            }
        }
        $collect_form->addElement($collectname);
        $startpageid = new JieqiFormText($jieqiLang['article']['collect_start_pageid'], 'startpageid', 30, 11);
        $startpageid->setDescription($jieqiLang['article']['collect_page_emptynote']);
        $collect_form->addElement($startpageid);
        $maxpagenum = new JieqiFormText($jieqiLang['article']['collect_max_pagenum'], 'maxpagenum', 30, 11);
        $maxpagenum->setDescription($jieqiLang['article']['collect_page_note']);
        $collect_form->addElement($maxpagenum);
        $notaddnew = new JieqiFormRadio($jieqiLang['article']['collect_or_addnew'], 'notaddnew', 0);
        $notaddnew->addOption(0, $jieqiLang['article']['collect_is_addnew']);
        $notaddnew->addOption(1, $jieqiLang['article']['collect_not_addnew']);
        $collect_form->addElement($notaddnew);
        $collect_form->addElement(new JieqiFormLabel($jieqiLang['article']['collect_not_addnew'], $jieqiLang['article']['collect_page_note']));
        $collect_form->addElement(new JieqiFormHidden('siteid', $_REQUEST['siteid']));
        $collect_form->addElement(new JieqiFormHidden('act', 'collect'));
        $collect_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $collect_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['article']['collect_start_button'], 'submit'));
        $jieqiTpl->assign('jieqi_contents', '<br />' . $collect_form->render(JIEQI_FORM_MAX) . '<br />');
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}