<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_checklogin();
jieqi_loadlang('hurry', JIEQI_MODULE_NAME);
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
        $jieqiAction['article']['hurry']['paymin'] = intval($jieqiAction['article']['hurry']['paymin']);
        $jieqiAction['article']['hurry']['paymax'] = intval($jieqiAction['article']['hurry']['paymax']);
        $_POST['payegold'] = isset($_POST['payegold']) ? intval(trim($_POST['payegold'])) : 0;
        if ($_POST['payegold'] <= 0) {
            $errtext .= $jieqiLang['article']['payegold_over_zero'] . '<br />';
        } else {
            if ($_POST['payegold'] < $jieqiAction['article']['hurry']['paymin']) {
                $errtext .= sprintf($jieqiLang['article']['payegold_over_min'], $jieqiAction['article']['hurry']['paymin']) . '<br />';
            } else {
                if (0 < $jieqiAction['article']['hurry']['paymax'] && $jieqiAction['article']['hurry']['paymax'] < $_POST['payegold']) {
                    $errtext .= sprintf($jieqiLang['article']['payegold_over_max'], $jieqiAction['article']['hurry']['paymax']) . '<br />';
                } else {
                    if ($usermoney['egold'] < $_POST['payegold']) {
                        $errtext .= $jieqiLang['article']['payegold_over_emoney'] . '<br />';
                    }
                }
            }
        }
        $_POST['minwords'] = isset($_POST['minwords']) ? intval(trim($_POST['minwords'])) : 0;
        if ($_POST['minwords'] <= 0) {
            $errtext .= $jieqiLang['article']['minwords_over_zero'] . '<br />';
        }
        if (isset($_POST['indays']) && !empty($_POST['indays'])) {
            $overtime = JIEQI_NOW_TIME + 86400 * intval($_POST['indays']);
        } else {
            $_POST['overyear'] = isset($_POST['overyear']) ? intval(trim($_POST['overyear'])) : 0;
            $_POST['overmonth'] = isset($_POST['overmonth']) ? intval(trim($_POST['overmonth'])) : 0;
            $_POST['overday'] = isset($_POST['overday']) ? intval(trim($_POST['overday'])) : 0;
            $_POST['overhour'] = empty($_POST['overhour']) ? 0 : intval(trim($_POST['overhour']));
            $_POST['overminute'] = empty($_POST['overminute']) ? 0 : intval(trim($_POST['overminute']));
            $_POST['oversecond'] = empty($_POST['oversecond']) ? 0 : intval(trim($_POST['oversecond']));
            $overtime = @mktime($_POST['overhour'], $_POST['overminute'], $_POST['oversecond'], $_POST['overmonth'], $_POST['overday'], $_POST['overyear']);
        }
        if (isset($jieqiAction['article']['hurry']['minhour']) && is_numeric($jieqiAction['article']['hurry']['minhour']) && 0 < floatval($jieqiAction['article']['hurry']['minhour'])) {
            if ($overtime - round(floatval($jieqiAction['article']['hurry']['minhour']) * 3600) < JIEQI_NOW_TIME) {
                $errtext .= sprintf($jieqiLang['article']['overtime_over_minhour'], $jieqiAction['article']['hurry']['minhour']) . '<br />';
            }
        } else {
            if ($overtime <= JIEQI_NOW_TIME) {
                $errtext .= $jieqiLang['article']['overtime_over_now'] . '<br />';
            }
        }
        if (empty($errtext)) {
            $ret = $users_handler->payout($users, $_POST['payegold']);
            if (!$ret) {
                jieqi_printfail($jieqiLang['article']['user_payout_failure']);
            }
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            $fieldrows = array();
            $fieldrows['articleid'] = $article->getVar('articleid', 'n');
            $fieldrows['articlename'] = $article->getVar('articlename', 'n');
            $fieldrows['vipid'] = $article->getVar('vipid', 'n');
            $fieldrows['authorid'] = $article->getVar('authorid', 'n');
            $fieldrows['author'] = $article->getVar('author', 'n');
            $fieldrows['uid'] = intval($_SESSION['jieqiUserId']);
            $fieldrows['uname'] = $_SESSION['jieqiUserName'];
            $fieldrows['addtime'] = JIEQI_NOW_TIME;
            $fieldrows['minwords'] = intval($_POST['minwords']);
            $fieldrows['overtime'] = $overtime;
            $fieldrows['toolnum'] = 0;
            $fieldrows['payegold'] = $_POST['payegold'];
            $fieldrows['taxegold'] = 0;
            $fieldrows['winegold'] = 0;
            $fieldrows['payflag'] = 0;
            $sql = $query->makeupsql(jieqi_dbprefix('article_hurry'), $fieldrows, 'INSERT');
            if (!$query->execute($sql)) {
                $users_handler->payback($users->getVar('uid', 'n'), $_POST['payegold']);
                jieqi_printfail($jieqiLang['article']['database_save_error']);
            } else {
                $hurryid = intval($query->db->getInsertId());
                jieqi_loadlang('action', 'article');
                include_once $jieqiModules['article']['path'] . '/include/funaction.php';
                $actions = array('actname' => 'hurry', 'actnum' => $_POST['payegold'], 'actegold' => $_POST['payegold'], 'actbuy' => 0, 'tname' => $article->getVar('author', 'n'), 'aname' => $article->getVar('articlename', 'n'));
                $actions['review_title'] = sprintf($jieqiLang['article']['hurry_review_title'], $_SESSION['jieqiUserName'], $_REQUEST['payegold'] . JIEQI_EGOLD_NAME, $actions['aname']);
                $actions['review_content'] = sprintf($jieqiLang['article']['hurry_review_content'], $_SESSION['jieqiUserName'], $_REQUEST['payegold'] . JIEQI_EGOLD_NAME, $actions['aname'], date('Y-m-d H:i', $fieldrows['overtime']), $fieldrows['minwords']);
                $actions['message_title'] = sprintf($jieqiLang['article']['hurry_message_title'], $_SESSION['jieqiUserName'], $_REQUEST['payegold'] . JIEQI_EGOLD_NAME, $actions['aname']);
                $actions['message_content'] = sprintf($jieqiLang['article']['hurry_message_content'], $_SESSION['jieqiUserName'], $_REQUEST['payegold'] . JIEQI_EGOLD_NAME, $actions['aname'], date('Y-m-d H:i', $fieldrows['overtime']), $fieldrows['minwords']);
                $actions['no_earn'] = true;
                jieqi_article_actiondo($actions, $article);
            }
            jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['hurry_save_success']);
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
        $overtime = JIEQI_NOW_TIME + 86400;
        $jieqiTpl->assign('overyear', date('Y', $overtime));
        $jieqiTpl->assign('overmonth', date('m', $overtime));
        $jieqiTpl->assign('overday', date('d', $overtime));
        $jieqiTpl->assign('overhour', date('H', $overtime));
        $jieqiTpl->assign('overminute', date('i', $overtime));
        $jieqiTpl->assign('oversecond', date('s', $overtime));
        if (empty($_REQUEST['ajax_request'])) {
            $jieqiTpl->assign('ajax_request', 0);
        } else {
            $jieqiTpl->assign('ajax_request', 1);
        }
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/hurry.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}