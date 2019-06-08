<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('article', JIEQI_MODULE_NAME);
jieqi_loadlang('draft', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['aid']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
jieqi_getconfigs('article', 'power');
jieqi_getconfigs('obook', 'power');
$canedit = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($article->getVar('authorid') == $tmpvar || $article->getVar('posterid') == $tmpvar || $article->getVar('agentid') == $tmpvar)) {
        $canedit = true;
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['article']['noper_manage_article']);
}
$canupload = jieqi_checkpower($jieqiPower['article']['articleupattach'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$customprice = jieqi_checkpower($jieqiPower['obook']['customprice'], $jieqiUsersStatus, $jieqiUsersGroup, true);
jieqi_getconfigs('article', 'configs');
jieqi_getconfigs('obook', 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$_POST['needupaudit'] = false;
if (!empty($jieqiConfigs['article']['upaudittimes'])) {
    include_once JIEQI_ROOT_PATH . '/include/checker.php';
    $checker = new JieqiChecker();
    $_POST['needupaudit'] = !$checker->deny_time($jieqiConfigs['article']['upaudittimes']);
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'chapter';
}
switch ($_POST['act']) {
    case 'newchapter':
        jieqi_checkpost();
        $_POST = jieqi_funtoarray('trim', $_POST);
        $_POST['chaptertype'] = 0;
        $_POST['canupload'] = $canupload;
        $_POST['uptiming'] = $_POST['posttype'] == 2 ? 1 : 0;
        include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
        $attachvars = array();
        $errors = jieqi_article_chapterpcheck($_POST, $attachvars);
        if (empty($errors)) {
            $_POST['posttype'] = intval($_POST['posttype']);
            if (isset($_SESSION['jieqiAutoSave'])) {
                unset($_SESSION['jieqiAutoSave']);
            }
            @session_write_close();
            $_POST['articleid'] = $_REQUEST['aid'];
            $_POST['isvip'] = empty($_POST['isvip']) || floatval(JIEQI_VERSION) < 2.1 || intval($article->getVar('issign', 'n')) < 10 ? 0 : 1;
            if (0 < $_POST['isvip']) {
                if (!isset($_POST['saleprice']) || !is_numeric($_POST['saleprice'])) {
                    $_POST['saleprice'] = -1;
                } else {
                    $_POST['saleprice'] = intval($_POST['saleprice']);
                    if ($_POST['saleprice'] < 0 || 0 < $_POST['saleprice'] && !$customprice) {
                        $_POST['saleprice'] = -1;
                    }
                }
            } else {
                $_POST['saleprice'] = 0;
            }
            if (!empty($_REQUEST['draftid'])) {
                $_POST['draftid'] = intval($_REQUEST['draftid']);
            }
            if (!$_POST['needupaudit'] && $_POST['posttype'] == 0) {
                $ret = jieqi_article_addchapter($_POST, $attachvars, $article);
                if (is_string($ret)) {
                    jieqi_printfail($ret);
                }
                jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . $_REQUEST['aid'], LANG_DO_SUCCESS, sprintf($jieqiLang['article']['add_chapter_success'], $article_static_url . '/articlemanage.php?id=' . $_REQUEST['aid'], jieqi_geturl('article', 'article', $_REQUEST['aid'], 'info', $article->getVar('articlecode', 'n')), $article_static_url . '/newchapter.php?aid=' . $_REQUEST['aid']));
            } else {
                $_POST['articleid'] = $_REQUEST['aid'];
                $_POST['articlename'] = $article->getVar('articlename');
                $_POST['obookid'] = intval($article->getVar('vipid', 'n'));
                $ret = jieqi_article_adddraft($_POST, $attachvars);
                if (is_string($ret)) {
                    jieqi_printfail($ret);
                }
                if ($_POST['needupaudit'] && $_POST['posttype'] == 0) {
                    jieqi_jumppage($article_dynamic_url . '/draft.php?type=3', LANG_DO_SUCCESS, sprintf($jieqiLang['article']['add_chapter_needaudit'], $article_static_url . '/articlemanage.php?id=' . $_REQUEST['aid'], jieqi_geturl('article', 'article', $_REQUEST['aid'], 'info', $article->getVar('articlecode', 'n')), $article_static_url . '/newchapter.php?aid=' . $_REQUEST['aid']));
                } else {
                    jieqi_jumppage($article_dynamic_url . '/draft.php', LANG_DO_SUCCESS, $jieqiLang['article']['draft_add_success']);
                }
            }
        } else {
            jieqi_printfail(implode('<br />', $errors));
        }
        break;
    case 'chapter':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $jieqiTpl->assign('url_newchapter', $article_static_url . '/newchapter.php?do=submit');
        $vipid = intval($article->getVar('vipid', 'n'));
        if (!empty($_REQUEST['cvip']) || 0 < $vipid) {
            $jieqiTpl->assign('cvip', 1);
        } else {
            $jieqiTpl->assign('cvip', 0);
        }
        if ($customprice) {
            $jieqiTpl->assign('customprice', 1);
        } else {
            $jieqiTpl->assign('customprice', 0);
        }
        $jieqiTpl->assign('articleid', $_REQUEST['aid']);
        $jieqiTpl->assign('articlename', $article->getVar('articlename'));
        $jieqiTpl->assign('obookid', $article->getVar('vipid'));
        $jieqiTpl->assign('issign', $article->getVar('issign'));
        $jieqiTpl->assign('isvip', $article->getVar('isvip'));
        $volumerows = array();
        $chapterorder = $article->getVar('chapters') + 1;
        include_once $jieqiModules['article']['path'] . '/class/chapter.php';
        $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
        $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['aid']));
        $criteria->setSort('chapterorder');
        $criteria->setOrder('DESC');
        $chapter_handler->queryObjects($criteria);
        $tmpvar = $chapterorder;
        $k = 0;
        while ($v = $chapter_handler->getObject()) {
            if ($v->getVar('chaptertype') == 1) {
                $volumerows[] = array('chapterorder' => $tmpvar, 'volumeid' => $v->getVar('chapterid'), 'volumename' => $v->getVar('chaptername'));
                $tmpvar = $chapterorder - $k - 1;
            }
            $k++;
        }
        $jieqiTpl->assign_by_ref('chapterorder', $chapterorder);
        $jieqiTpl->assign_by_ref('volumerows', $volumerows);
        $chaptername = '';
        $chaptercontent = '';
        $from_draft = false;
        $attachnum = 0;
        $attachrows = array();
        if (!empty($_REQUEST['draftid'])) {
            include_once $jieqiModules['article']['path'] . '/class/draft.php';
            $draft_handler = JieqiDraftHandler::getInstance('JieqiDraftHandler');
            $draft = $draft_handler->get($_REQUEST['draftid']);
            if (is_object($draft)) {
                $chaptername = $draft->getVar('chaptername', 'e');
                $chaptercontent = $draft->getVar('chaptercontent', 'e');
                $saleprice = $draft->getVar('saleprice', 'e');
                if ($saleprice < 0) {
                    $saleprice = '';
                }
                $cvip = intval($draft->getVar('isvip', 'n'));
                if (0 < $cvip) {
                    $jieqiTpl->assign('cvip', 1);
                } else {
                    $jieqiTpl->assign('cvip', 0);
                }
                $attachrows = @jieqi_unserialize($draft->getVar('attachment', 'n'));
                if (!is_array($attachrows)) {
                    $attachrows = array();
                }
                $attachnum = count($attachrows);
                $attachrows = jieqi_funtoarray('jieqi_htmlstr', $attachrows);
                $from_draft = true;
            } else {
                $_REQUEST['draftid'] = 0;
            }
        }
        $jieqiTpl->assign_by_ref('chaptername', $chaptername);
        $jieqiTpl->assign_by_ref('chaptercontent', $chaptercontent);
        $jieqiTpl->assign('authtypeset', $jieqiConfigs['article']['authtypeset']);
        $jieqiTpl->assign('canupload', $canupload);
        if ($canupload && is_numeric($jieqiConfigs['article']['maxattachnum']) && 0 < $jieqiConfigs['article']['maxattachnum']) {
            $maxattachnum = intval($jieqiConfigs['article']['maxattachnum']);
        } else {
            $maxattachnum = 0;
        }
        $jieqiTpl->assign('maxattachnum', $maxattachnum);
        $jieqiTpl->assign('attachtype', $jieqiConfigs['article']['attachtype']);
        $jieqiTpl->assign('maximagesize', $jieqiConfigs['article']['maximagesize']);
        $jieqiTpl->assign('maxfilesize', $jieqiConfigs['article']['maxfilesize']);
        $jieqiTpl->assign('uptiming', intval($jieqiConfigs['article']['uptiming']));
        if ($from_draft) {
            $jieqiTpl->assign('draftid', intval($_REQUEST['draftid']));
        } else {
            $jieqiTpl->assign('draftid', 0);
        }
        $jieqiTpl->assign('attachnum', $attachnum);
        $jieqiTpl->assign_by_ref('attachrows', $attachrows);
        if ($_POST['needupaudit']) {
            $jieqiTpl->assign('needupaudit', 1);
        } else {
            $jieqiTpl->assign('needupaudit', 0);
        }
        if (!empty($_SESSION['jieqiAutoSave'])) {
            $jieqiTpl->assign('jieqi_autosave', $_SESSION['jieqiAutoSave']);
        } else {
            $jieqiTpl->assign('jieqi_autosave', '');
        }
        $jieqiTpl->assign('authorarea', 1);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/newchapter.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}