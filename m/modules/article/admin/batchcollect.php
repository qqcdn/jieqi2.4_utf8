<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_loadlang('collect', JIEQI_MODULE_NAME);
@set_time_limit(0);
@session_write_close();
$self_filename = 'batchcollect.php';
if (!empty($_SERVER['SCRIPT_NAME']) && substr($_SERVER['SCRIPT_NAME'], -4) == '.php') {
    $tmpary = explode('/', $_SERVER['SCRIPT_NAME']);
    if (!empty($tmpary[count($tmpary) - 1])) {
        $self_filename = $tmpary[count($tmpary) - 1];
    }
}
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'collectsite');
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
        $errtext = '';
        if (!is_numeric($_REQUEST['siteid'])) {
            $errtext .= $jieqiLang['article']['need_source_site'] . '<br />';
        }
        if (!is_numeric($_REQUEST['startid'])) {
            $errtext .= $jieqiLang['article']['need_start_articleid'] . '<br />';
        }
        if (!is_numeric($_REQUEST['endid'])) {
            $errtext .= $jieqiLang['article']['need_end_articleid'] . '<br />';
        }
        if (empty($errtext)) {
            if ($_REQUEST['endid'] < $_REQUEST['startid']) {
                jieqi_article_updateinfo(0);
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['batch_collect_success']);
                exit;
            }
            if (array_key_exists($_REQUEST['siteid'], $jieqiCollectsite) && $jieqiCollectsite[$_REQUEST['siteid']]['enable'] == '1') {
                include_once JIEQI_ROOT_PATH . '/configs/article/site_' . $jieqiCollectsite[$_REQUEST['siteid']]['config'] . '.php';
                if (empty($jieqiCollect['articletitle'])) {
                    jieqi_printfail($jieqiLang['article']['collect_rule_notfull']);
                }
                include_once $jieqiModules['article']['path'] . '/class/article.php';
                include_once $jieqiModules['article']['path'] . '/class/package.php';
                echo sprintf($jieqiLang['article']['checking_article_now'], $_REQUEST['startid']) . '<br />';
                ob_flush();
                flush();
                $aid = $_REQUEST['startid'];
                include $jieqiModules['article']['path'] . '/include/updateone.php';
                ${$_REQUEST}['startid']++;
                $url = $self_filename . '?act=collect&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&siteid=' . urlencode($_REQUEST['siteid']) . '&startid=' . urlencode($_REQUEST['startid']) . '&endid=' . urlencode($_REQUEST['endid']) . '&notaddnew=' . urlencode($_REQUEST['notaddnew']);
                echo sprintf($jieqiLang['article']['collect_next_html'], JIEQI_CHAR_SET, $showinfo, $url, $url);
            } else {
                jieqi_printfail($jieqiLang['article']['not_support_collectsite']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'bcollect':
        jieqi_checkpost();
        $errtext = '';
        if (!is_numeric($_REQUEST['siteid'])) {
            $errtext .= $jieqiLang['article']['need_source_site'] . '<br />';
        }
        if (empty($_REQUEST['batchids'])) {
            $errtext .= $jieqiLang['article']['need_collect_articleid'] . '<br />';
        }
        if (empty($errtext)) {
            if (array_key_exists($_REQUEST['siteid'], $jieqiCollectsite) && $jieqiCollectsite[$_REQUEST['siteid']]['enable'] == '1') {
                include_once JIEQI_ROOT_PATH . '/configs/article/site_' . $jieqiCollectsite[$_REQUEST['siteid']]['config'] . '.php';
                if (empty($jieqiCollect['articletitle'])) {
                    jieqi_printfail($jieqiLang['article']['collect_rule_notfull']);
                }
                include_once $jieqiModules['article']['path'] . '/class/article.php';
                include_once $jieqiModules['article']['path'] . '/class/package.php';
                $idsary = explode(',', $_REQUEST['batchids']);
                foreach ($idsary as $aid) {
                    $aid = trim($aid);
                    if (!empty($aid)) {
                        echo sprintf($jieqiLang['article']['checking_article_now'], $aid) . '<br />';
                        ob_flush();
                        flush();
                        include $jieqiModules['article']['path'] . '/include/updateone.php';
                    }
                }
                jieqi_article_updateinfo(0);
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['batch_collect_success']);
            } else {
                jieqi_printfail($jieqiLang['article']['not_support_collectsite']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
        $collect_form = new JieqiThemeForm($jieqiLang['article']['batch_collect_useid'], 'frmcollect', $article_static_url . '/admin/' . $self_filename);
        $siteid = new JieqiFormSelect($jieqiLang['article']['collect_siteid'], 'siteid', '1');
        foreach ($jieqiCollectsite as $k => $v) {
            $siteid->addOption($k, $v['name']);
        }
        $collect_form->addElement($siteid);
        $collect_form->addElement(new JieqiFormText($jieqiLang['article']['collect_start_id'], 'startid', 30, 11), true);
        $collect_form->addElement(new JieqiFormText($jieqiLang['article']['collect_end_id'], 'endid', 30, 11), true);
        $notaddnew = new JieqiFormRadio($jieqiLang['article']['collect_or_addnew'], 'notaddnew', 0);
        $notaddnew->addOption(0, $jieqiLang['article']['collect_is_addnew']);
        $notaddnew->addOption(1, $jieqiLang['article']['collect_not_addnew']);
        $collect_form->addElement($notaddnew);
        $collect_form->addElement(new JieqiFormLabel($jieqiLang['article']['collect_note'], $jieqiLang['article']['collect_addnew_note']));
        $collect_form->addElement(new JieqiFormHidden('act', 'collect'));
        $collect_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $collect_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['article']['collect_start_button'], 'submit'));
        $page_form = new JieqiThemeForm($jieqiLang['article']['batch_collect_usepage'], 'frmpcollect', $article_static_url . '/admin/pagecollect.php');
        $siteid = new JieqiFormSelect($jieqiLang['article']['collect_siteid'], 'siteid', '1');
        foreach ($jieqiCollectsite as $k => $v) {
            $siteid->addOption($k, $v['name']);
        }
        $page_form->addElement($siteid);
        $page_form->addElement(new JieqiFormLabel($jieqiLang['article']['collect_note'], $jieqiLang['article']['collect_page_note']));
        $page_form->addElement(new JieqiFormButton('&nbsp;', 'psubmit', $jieqiLang['article']['collect_next_button'], 'submit'));
        $batchid_form = new JieqiThemeForm($jieqiLang['article']['batch_collect_uselist'], 'frmbcollect', $article_static_url . '/admin/' . $self_filename);
        $siteid = new JieqiFormSelect($jieqiLang['article']['collect_siteid'], 'siteid', '1');
        foreach ($jieqiCollectsite as $k => $v) {
            $siteid->addOption($k, $v['name']);
        }
        $batchid_form->addElement($siteid);
        $batchids = new JieqiFormTextArea($jieqiLang['article']['collect_batch_id'], 'batchids', '', 5, 60);
        $batchid_form->addElement($batchids, true);
        $batchid_form->addElement(new JieqiFormLabel($jieqiLang['article']['collect_note'], $jieqiLang['article']['collect_batchid_note']));
        $batchid_form->addElement(new JieqiFormHidden('act', 'bcollect'));
        $batchid_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $batchid_form->addElement(new JieqiFormButton('&nbsp;', 'bsubmit', $jieqiLang['article']['collect_start_button'], 'submit'));
        $jieqiTpl->assign('jieqi_contents', '<br />' . $collect_form->render(JIEQI_FORM_MAX) . '<br />' . $page_form->render(JIEQI_FORM_MAX) . '<br />' . $batchid_form->render(JIEQI_FORM_MAX) . '<br />');
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}