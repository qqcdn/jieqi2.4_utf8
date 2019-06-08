<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
if (empty($_REQUEST['aid']) || empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('avote', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$_REQUEST['id'] = intval($_REQUEST['id']);
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
    jieqi_printfail($jieqiLang['article']['noper_article_votenew']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiConfigs['article']['articlevote'] = intval($jieqiConfigs['article']['articlevote']);
if ($jieqiConfigs['article']['articlevote'] <= 0) {
    jieqi_printfail($jieqiLang['article']['article_vote_close']);
}
include_once $jieqiModules['article']['path'] . '/class/avote.php';
$avote_handler = JieqiAvoteHandler::getInstance('JieqiAvoteHandler');
$avote = $avote_handler->get($_REQUEST['id']);
if (!$avote) {
    jieqi_printfail($jieqiLang['article']['avote_not_exists']);
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'edit';
}
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
switch ($_POST['act']) {
    case 'update':
        jieqi_checkpost();
        $errtext = '';
        $_POST['title'] = trim($_POST['title']);
        $useitem = 0;
        $itemary = array();
        for ($i = 1; $i <= $jieqiConfigs['article']['articlevote']; $i++) {
            $_POST['item' . $i] = isset($_POST['item' . $i]) ? trim($_POST['item' . $i]) : '';
            if ($_POST['item' . $i] != '') {
                $itemary[$useitem] = $_POST['item' . $i];
                $useitem++;
            }
        }
        if (strlen($_POST['title']) == 0) {
            $errtext .= $jieqiLang['article']['avote_need_title'] . '<br />';
        }
        if ($useitem < 2) {
            $errtext .= $jieqiLang['article']['avote_need_moreitem'] . '<br />';
        }
        if (empty($errtext)) {
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            $avote->setVar('posterid', $_SESSION['jieqiUserId']);
            $avote->setVar('poster', $_REQUEST['jieqiUserName']);
            $avote->setVar('title', $_POST['title']);
            foreach ($itemary as $k => $v) {
                $avote->setVar('item' . ($k + 1), $v);
            }
            $i = $useitem + 1;
            while ($i <= 10) {
                $avote->setVar('item' . $i, '');
                $i++;
            }
            $avote->setVar('useitem', $useitem);
            if ($avote->getVar('ispublish', 'n') != $_POST['ispublish']) {
                $changepublish = true;
            } else {
                $changepublish = false;
            }
            if ($changepublish) {
                if ($_POST['ispublish'] == 1) {
                    $avote->setVar('ispublish', 1);
                    $avote->setVar('starttime', JIEQI_NOW_TIME);
                    $sql = 'UPDATE ' . jieqi_dbprefix('article_avote') . ' SET ispublish=0, endtime=' . intval(JIEQI_NOW_TIME) . ' WHERE articleid=' . $_REQUEST['aid'] . ' AND ispublish=1';
                    $query->execute($sql);
                    $setting = jieqi_unserialize($article->getVar('setting', 'n'));
                    $setting['avoteid'] = $_REQUEST['id'];
                    $article->setVar('setting', serialize($setting));
                    $article_handler->insert($article);
                } else {
                    $avote->setVar('ispublish', 0);
                    $avote->setVar('endtime', JIEQI_NOW_TIME);
                    $setting = jieqi_unserialize($article->getVar('setting', 'n'));
                    $setting['avoteid'] = 0;
                    $article->setVar('setting', serialize($setting));
                    $article_handler->insert($article);
                }
            }
            if ($_POST['mulselect'] == 1) {
                $avote->setVar('mulselect', 1);
            } else {
                $avote->setVar('mulselect', 0);
            }
            $avote->setVar('timelimit', 0);
            $avote->setVar('needlogin', 0);
            $avote->setVar('endtime', 0);
            if (!$avote_handler->insert($avote)) {
                jieqi_printfail($jieqiLang['article']['avote_edit_failure']);
            } else {
                jieqi_jumppage($article_static_url . '/votearticle.php?id=' . $_REQUEST['aid'], LANG_DO_SUCCESS, $jieqiLang['article']['avote_edit_success']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'edit':
    default:
        jieqi_getconfigs('article', 'authorblocks', 'jieqiBlocks');
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $voterows = array();
        for ($i = 1; $i <= $jieqiConfigs['article']['articlevote']; $i++) {
            $voterows[] = $avote->getVar('item' . $i, 'e');
        }
        $jieqiTpl->assign_by_ref('voterows', $voterows);
        $jieqiTpl->assign('votetitle', $avote->getVar('title', 'e'));
        $jieqiTpl->assign('mulselect', $avote->getVar('mulselect', 'e'));
        $jieqiTpl->assign('ispublish', $avote->getVar('ispublish', 'e'));
        $jieqiTpl->assign('id', $_REQUEST['id']);
        $jieqiTpl->assign('aid', $_REQUEST['aid']);
        $jieqiTpl->assign('authorarea', 1);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/voteedit.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}