<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (!isset($_REQUEST['isvip']) || $_REQUEST['isvip'] != 1) {
    $_REQUEST['isvip'] = 0;
} else {
    $_REQUEST['isvip'] = 1;
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['newdraft'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_loadlang('article', JIEQI_MODULE_NAME);
jieqi_loadlang('draft', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('obook', 'power');
$customprice = jieqi_checkpower($jieqiPower['obook']['customprice'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if (!isset($_POST['act'])) {
    $_POST['act'] = 'draft';
}
$canupload = jieqi_checkpower($jieqiPower['article']['articleupattach'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$customprice = jieqi_checkpower($jieqiPower['obook']['customprice'], $jieqiUsersStatus, $jieqiUsersGroup, true);
switch ($_POST['act']) {
    case 'newdraft':
        jieqi_checkpost();
        $_POST = jieqi_funtoarray('trim', $_POST);
        $_POST['chaptertype'] = 0;
        $_POST['canupload'] = $canupload;
        include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
        $attachvars = array();
        $errors = jieqi_article_chapterpcheck($_POST, $attachvars);
        if ($_REQUEST['isvip'] == 1) {
            $_POST['bookid'] = intval($_POST['obookid']);
        } else {
            $_POST['bookid'] = intval($_POST['articleid']);
        }
        if (empty($_POST['bookid'])) {
            $errors[] = $jieqiLang['article']['draft_need_articleid'];
        }
        $_POST['articleid'] = 0;
        $_POST['articlename'] = '';
        $_POST['obookid'] = 0;
        if ($_REQUEST['isvip'] == 1) {
            include_once $jieqiModules['obook']['path'] . '/class/obook.php';
            $obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
            $obook = $obook_handler->get($_POST['bookid']);
            if (!is_object($obook)) {
                $errors[] = $jieqiLang['article']['draft_noe_article'] . '<br />';
            } else {
                $_POST['articleid'] = $obook->getVar('articleid', 'n');
                $_POST['articlename'] = $obook->getVar('obookname', 'n');
                $_POST['obookid'] = $obook->getVar('obookid', 'n');
                include_once $jieqiModules['article']['path'] . '/class/article.php';
                $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
                $article = $article_handler->get($_POST['articleid']);
                if (!is_object($article)) {
                    $errors[] = $jieqiLang['article']['draft_noe_article'] . '<br />';
                } else {
                    if (intval($article->getVar('issign', 'n')) < 10) {
                        $errors[] = $jieqiLang['article']['draft_not_vipsign'] . '<br />';
                    }
                }
            }
        } else {
            include_once $jieqiModules['article']['path'] . '/class/article.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $article = $article_handler->get($_POST['bookid']);
            if (!is_object($article)) {
                $errors[] = $jieqiLang['article']['draft_noe_article'] . '<br />';
            } else {
                $_POST['articleid'] = $article->getVar('articleid', 'n');
                $_POST['articlename'] = $article->getVar('articlename', 'n');
                $_POST['obookid'] = 0;
            }
        }
        if (!isset($_POST['saleprice']) || !is_numeric($_POST['saleprice'])) {
            $_POST['saleprice'] = -1;
        } else {
            $_POST['saleprice'] = intval($_POST['saleprice']);
            if ($_POST['saleprice'] < 0 || 0 < $_POST['saleprice'] && !$customprice) {
                $_POST['saleprice'] = -1;
            }
        }
        if (empty($errors)) {
            $ret = jieqi_article_adddraft($_POST, $attachvars);
            if (is_string($ret)) {
                jieqi_printfail($ret);
            }
            jieqi_jumppage($article_dynamic_url . '/draft.php', LANG_DO_SUCCESS, $jieqiLang['article']['draft_add_success']);
        } else {
            jieqi_printfail(implode('<br />', $errors));
        }
        break;
    case 'draft':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $articlerows = array();
        $obookrows = array();
        include_once $jieqiModules['article']['path'] . '/class/article.php';
        $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
        $criteria = new CriteriaCompo(new Criteria('authorid', $_SESSION['jieqiUserId']));
        $criteria->setSort('articleid');
        $criteria->setOrder('DESC');
        $criteria->setLimit(200);
        $article_handler->queryObjects($criteria);
        $k = 0;
        $o = 0;
        while ($v = $article_handler->getObject()) {
            $articlerows[$k]['articleid'] = $v->getVar('articleid');
            $articlerows[$k]['articlename'] = $v->getVar('articlename');
            $articlerows[$k]['issign'] = intval($v->getVar('issign', 'n'));
            $articlerows[$k]['vipid'] = intval($v->getVar('vipid', 'n'));
            if (10 <= $articlerows[$k]['issign'] && 0 < $articlerows[$k]['vipid']) {
                $obookrows[$o]['obookid'] = $articlerows[$k]['vipid'];
                $obookrows[$o]['obookname'] = $articlerows[$k]['articlename'];
                $o++;
            }
            $k++;
        }
        $jieqiTpl->assign_by_ref('articlerows', $articlerows);
        $jieqiTpl->assign_by_ref('obookrows', $obookrows);
        if ($customprice) {
            $jieqiTpl->assign('customprice', 1);
        } else {
            $jieqiTpl->assign('customprice', 0);
        }
        $jieqiTpl->assign('uptiming', intval($jieqiConfigs['article']['uptiming']));
        $jieqiTpl->assign('authtypeset', intval($jieqiConfigs['article']['authtypeset']));
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
        $jieqiTpl->assign('authorarea', 1);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/newdraft.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}