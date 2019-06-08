<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_checklogin();
jieqi_loadlang('tip', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$users = $users_handler->get($_SESSION['jieqiUserId']);
if (!is_object($users)) {
    jieqi_printfail($jieqiLang['article']['user_not_exists']);
}
$userisvip = $users->getVar('isvip', 'n');
$usermoney = $users->getEmoney();
if ($usermoney['egold'] <= 0) {
    jieqi_printfail($jieqiLang['article']['user_no_emoney']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('article', 'tiptype', 'jieqiTiptype');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'post':
        jieqi_checkpost();
        $errtext = '';
        jieqi_getconfigs('article', 'action', 'jieqiAction');
        $jieqiAction['article']['tip']['paymin'] = intval($jieqiAction['article']['tip']['paymin']);
        if (empty($_REQUEST['tiptype'])) {
            $_REQUEST['payegold'] = intval(trim($_REQUEST['payegold']));
            $_REQUEST['tiptype'] = 0;
            $_REQUEST['tipnum'] = $_REQUEST['payegold'];
            if ($_REQUEST['payegold'] <= 0) {
                $errtext .= $jieqiLang['article']['payegold_over_zero'] . '<br />';
            } else {
                if ($_REQUEST['payegold'] < $jieqiAction['article']['tip']['paymin']) {
                    $errtext .= sprintf($jieqiLang['article']['payegold_over_min'], $jieqiAction['article']['tip']['paymin']) . '<br />';
                } else {
                    if ($usermoney['egold'] < $_REQUEST['payegold']) {
                        $errtext .= $jieqiLang['article']['payegold_over_emoney'] . '<br />';
                    }
                }
            }
        } else {
            $_REQUEST['tiptype'] = intval($_REQUEST['tiptype']);
            if (!isset($jieqiTiptype['article'][$_REQUEST['tiptype']])) {
                $errtext .= $jieqiLang['article']['tip_type_error'] . '<br />';
            } else {
                $_REQUEST['tipnum'] = isset($_REQUEST['tipnum']) ? intval($_REQUEST['tipnum']) : 1;
                if ($_REQUEST['tipnum'] < 1) {
                    $_REQUEST['tipnum'] = 1;
                }
                $_REQUEST['payegold'] = $_REQUEST['tipnum'] * $jieqiTiptype['article'][$_REQUEST['tiptype']]['eprice'];
            }
        }
        if (empty($errtext)) {
            $unionid = intval($article->getVar('unionid', 'n'));
            if (0 < $unionid) {
                jieqi_getconfigs('system', 'sites', 'jieqiSites');
                if (!isset($jieqiSites[$unionid])) {
                    jieqi_printfail(LANG_ERROR_CONFIG);
                }
                include_once $jieqiModules['article']['path'] . '/apic/' . $jieqiSites[$unionid]['interface'] . '/apiclient.php';
                $jieqiapi = new JieqiApiClient($jieqiSites[$unionid]);
                $params = array('aid' => intval($article->getVar('sourceid', 'n')), 'egold' => $_REQUEST['payegold'], 'caid' => intval($article->getVar('articleid', 'n')), 'cuid' => intval($_SESSION['jieqiUserId']), 'cip' => jieqi_userip(), 'caname' => $article->getVar('articlename', 'n'));
                $ret = $jieqiapi->api('articletip', $params);
                if ($ret['ret'] < 0) {
                    jieqi_printfail(jieqi_htmlstr($ret['msg']));
                }
                if (!is_array($ret['msg'])) {
                    jieqi_printfail($jieqiLang['article']['jieqiapi_return_formaterror']);
                }
                $result = $ret['msg'];
                if (isset($result['ret']) && $result['ret'] < 0) {
                    jieqi_printfail(jieqi_htmlstr($result['msg']));
                }
            }
            $ret = $users_handler->payout($users, $_REQUEST['payegold']);
            if (!$ret) {
                jieqi_printfail($jieqiLang['article']['user_payout_failure']);
            }
            $tid = 0 < $article->getVar('authorid', 'n') ? $article->getVar('authorid', 'n') : $article->getVar('posterid', 'n');
            $tname = 0 < $article->getVar('authorid', 'n') ? $article->getVar('author', 'n') : $article->getVar('poster', 'n');
            include_once $jieqiModules['obook']['path'] . '/include/funbuy.php';
            jieqi_obook_upincome(array('articleid' => $article->getVar('articleid', 'n'), 'egold' => $_REQUEST['payegold'], 'etype' => 0, 'intype' => 'tip', 'salenum' => 0));
            include_once $jieqiModules['article']['path'] . '/include/funaction.php';
            $actions = array('actname' => 'tip', 'acttype' => $_REQUEST['tiptype'], 'actnum' => $_REQUEST['tipnum'], 'actegold' => $_REQUEST['payegold'], 'actbuy' => 0, 'tid' => $tid, 'tname' => $tname, 'aname' => $article->getVar('articlename', 'n'));
            jieqi_loadlang('action', 'article');
            if (empty($_REQUEST['tiptype'])) {
                $actions['review_title'] = sprintf($jieqiLang['article']['tip_review_title'], $_SESSION['jieqiUserName'], $article->getVar('author', 'n'), $_REQUEST['payegold'] . JIEQI_EGOLD_NAME);
                $actions['review_content'] = empty($_REQUEST['tipnote']) ? sprintf($jieqiLang['article']['tip_review_content'], $_SESSION['jieqiUserName'], $article->getVar('author', 'n'), $_REQUEST['payegold'] . JIEQI_EGOLD_NAME) : $_REQUEST['tipnote'];
            } else {
                $actions['review_title'] = sprintf($jieqiTiptype['article'][$_REQUEST['tiptype']]['notetitle'], $_SESSION['jieqiUserName'], $article->getVar('author', 'n'), $_REQUEST['tipnum']);
                $actions['review_content'] = empty($_REQUEST['tipnote']) ? sprintf($jieqiTiptype['article'][$_REQUEST['tiptype']]['notecontent'], $_SESSION['jieqiUserName'], $article->getVar('author', 'n'), $_REQUEST['tipnum']) : $_REQUEST['tipnote'];
            }
            $actions['message_title'] = sprintf($jieqiLang['article']['tip_message_title'], $_SESSION['jieqiUserName'], $article->getVar('articlename', 'n'), $_REQUEST['payegold'] . JIEQI_EGOLD_NAME);
            $actions['message_content'] = sprintf($jieqiLang['article']['tip_message_content'], $_SESSION['jieqiUserName'], $article->getVar('articlename', 'n'), $_REQUEST['payegold'] . JIEQI_EGOLD_NAME);
            jieqi_article_actiondo($actions, $article);
            include_once JIEQI_ROOT_PATH . '/include/funactivity.php';
            jieqi_activity_update(array('acttype' => 'tip', 'userid' => $users->getVar('uid', 'n'), 'joindate' => date('Ymd', $users->getVar('regdate', 'n'))));
            if (empty($_REQUEST['tiptype'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['tip_save_success']);
            } else {
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['tiptype_save_success']);
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
        $jieqiTpl->assign('articleid', $article->getVar('articleid'));
        $jieqiTpl->assign('articlename', $article->getVar('articlename'));
        $jieqiTpl->assign('articlecode', $article->getVar('articlecode'));
        $jieqiTpl->assign('vipid', $article->getVar('vipid'));
        $jieqiTpl->assign('postdate', date(JIEQI_DATE_FORMAT, $article->getVar('postdate')));
        $jieqiTpl->assign('lastupdate', date(JIEQI_DATE_FORMAT, $article->getVar('lastupdate')));
        $jieqiTpl->assign('authorid', $article->getVar('authorid'));
        $jieqiTpl->assign('author', $article->getVar('author'));
        $jieqiTpl->assign('useregold', $usermoney['egold']);
        $jieqiTpl->assign('useresilver', $usermoney['esilver']);
        $jieqiTpl->assign('useremoney', $usermoney['egold']);
        $jieqiTpl->assign('usermoney', $usermoney);
        $jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
        $jieqiTpl->assign('tiptyperows', jieqi_funtoarray('jieqi_htmlstr', $jieqiTiptype['article']));
        if (empty($_REQUEST['ajax_request'])) {
            $jieqiTpl->assign('ajax_request', 0);
        } else {
            $jieqiTpl->assign('ajax_request', 1);
        }
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/tip.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}