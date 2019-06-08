<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'gift', 'jieqiGift');
jieqi_loadlang('gift', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs', 'jieqiConfigs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
if (!empty($_POST['type']) && !empty($_POST['num'])) {
    $_POST['act'] = 'post';
}
switch ($_POST['act']) {
    case 'post':
        jieqi_checkpost();
        jieqi_getconfigs('article', 'action', 'jieqiAction');
        $_REQUEST['type'] = trim($_REQUEST['type']);
        if (!is_array($jieqiGift['article']) || !isset($jieqiGift['article'][$_REQUEST['type']])) {
            jieqi_printfail(LANG_ERROR_PARAMETER);
        }
        if (empty($_REQUEST['num'])) {
            $_REQUEST['num'] = 1;
        } else {
            $_REQUEST['num'] = intval($_REQUEST['num']);
        }
        if ($_REQUEST['num'] < 1) {
            $_REQUEST['num'] = 1;
        }
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        $jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
        if (!is_object($jieqiUsers)) {
            jieqi_printfail($jieqiLang['article']['user_not_exists']);
        }
        $userset = jieqi_unserialize($jieqiUsers->getVar('setting', 'n'));
        if (empty($userset['gift'][$_REQUEST['type']])) {
            jieqi_printfail(sprintf($jieqiLang['article']['not_this_gift'], $jieqiGift['article'][$_REQUEST['type']]['caption']));
        } else {
            if (intval($userset['gift'][$_REQUEST['type']]) < $_REQUEST['num']) {
                jieqi_printfail(sprintf($jieqiLang['article']['low_this_gift'], $jieqiGift['article'][$_REQUEST['type']]['caption']));
            }
        }
        include_once JIEQI_ROOT_PATH . '/include/funstat.php';
        $userset['gift'][$_REQUEST['type']] = intval($userset['gift'][$_REQUEST['type']]) - $_REQUEST['num'];
        $taskmodule = 'article';
        $taskname = $_REQUEST['type'];
        jieqi_getconfigs('system', 'tasks', 'jieqiTasks');
        if (!empty($jieqiTasks[$taskmodule][$taskname]['score']) && empty($_SESSION['jieqiUserSet']['tasks'][$taskmodule][$taskname])) {
            $userset['tasks'][$taskmodule][$taskname] = 1;
            $jieqiUsers->setVar('score', intval($jieqiUsers->getVar('score', 'n')) + intval($jieqiTasks[$taskmodule][$taskname]['score']));
        }
        $jieqiUsers->setVar('setting', serialize($userset));
        $jieqiUsers->saveToSession();
        $users_handler->insert($jieqiUsers);
        if (in_array($_REQUEST['type'], array('vipvote', 'flower', 'egg'))) {
            $upfields = array();
            $fieldname = $_REQUEST['type'];
            $addnum = $_REQUEST['num'];
            $lasttime = $article->getVar('last' . $fieldname, 'n');
            $addorup = jieqi_visit_addorup($lasttime);
            $upfields['day' . $fieldname] = $addorup['day'] ? $addnum : $article->getVar('day' . $fieldname, 'n') + $addnum;
            $upfields['week' . $fieldname] = $addorup['week'] ? $addnum : $article->getVar('week' . $fieldname, 'n') + $addnum;
            if (1 < $addorup['month']) {
                $upfields['pre' . $fieldname] = 0;
            } else {
                if ($addorup['month'] == 1) {
                    $upfields['pre' . $fieldname] = $article->getVar('month' . $fieldname, 'n');
                }
            }
            $upfields['month' . $fieldname] = $addorup['month'] ? $addnum : $article->getVar('month' . $fieldname, 'n') + $addnum;
            $upfields['all' . $fieldname] = $article->getVar('all' . $fieldname, 'n') + $addnum;
            $upfields['last' . $fieldname] = JIEQI_NOW_TIME;
            $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['id']));
            $article_handler->updatefields($upfields, $criteria);
            include_once $jieqiModules['article']['path'] . '/include/funaction.php';
            $actions = array('actname' => $_REQUEST['type'], 'actnum' => $_REQUEST['num']);
            jieqi_article_actiondo($actions, $article);
        }
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['gift_save_success']);
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
        $jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
        if (empty($_REQUEST['ajax_request'])) {
            $jieqiTpl->assign('ajax_request', 0);
        } else {
            $jieqiTpl->assign('ajax_request', 1);
        }
        if (empty($_REQUEST['type']) || !isset($jieqiGift['article'][$_REQUEST['type']])) {
            $_REQUEST['type'] = 'flower';
        }
        $jieqiTpl->assign('gift_type', jieqi_htmlstr($_REQUEST['type']));
        $jieqiTpl->assign('giftvals', jieqi_funtoarray('jieqi_htmlstr', $jieqiGift['article'][$_REQUEST['type']]));
        $jieqiTpl->assign_by_ref('giftrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiGift['article']));
        if (!empty($_REQUEST['jumpurl']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_REQUEST['jumpurl'])) {
            $jieqiTpl->assign('jumpurl', urlencode($_REQUEST['jumpurl']));
        } else {
            if (!empty($_SERVER['HTTP_REFERER']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_SERVER['HTTP_REFERER']) && !preg_match('/(giftbuy\\.php)/i', $_SERVER['HTTP_REFERER'])) {
                $jieqiTpl->assign('jumpurl', urlencode($_SERVER['HTTP_REFERER']));
            } else {
                $jieqiTpl->assign('jumpurl', '');
            }
        }
        $jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/gift.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}