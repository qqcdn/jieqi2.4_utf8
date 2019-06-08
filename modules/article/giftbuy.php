<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
jieqi_loadlang('gift', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'gift', 'jieqiGift');
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
if (!is_object($jieqiUsers)) {
    jieqi_printfail($jieqiLang['article']['user_not_exists']);
}
$userisvip = $jieqiUsers->getVar('isvip', 'n');
$userscore = $jieqiUsers->getVar('score', 'n');
$usermoney = $jieqiUsers->getEmoney();
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
        $_REQUEST['type'] = trim($_REQUEST['type']);
        if (!is_array($jieqiGift['article']) || !isset($jieqiGift['article'][$_REQUEST['type']])) {
            jieqi_printfail(LANG_ERROR_PARAMETER);
        }
        $_REQUEST['currency'] = trim($_REQUEST['currency']);
        if (!in_array($_REQUEST['currency'], array('egold', 'score'))) {
            jieqi_printfail($jieqiLang['article']['giftbuy_currency_error']);
        }
        if ($_REQUEST['currency'] == 'egold' && empty($jieqiGift['article'][$_REQUEST['type']]['eprice']) || $_REQUEST['currency'] == 'score' && empty($jieqiGift['article'][$_REQUEST['type']]['sprice'])) {
            jieqi_printfail($jieqiLang['article']['giftbuy_currency_deny']);
        }
        $_POST['num'] = isset($_POST['num']) ? intval(trim($_POST['num'])) : 0;
        if ($_POST['num'] <= 0) {
            $errtext .= $jieqiLang['article']['giftbuy_over_zero'] . '<br />';
        }
        if ($_REQUEST['currency'] == 'egold' && $usermoney['egold'] < $jieqiGift['article'][$_REQUEST['type']]['eprice'] * $_POST['num']) {
            $errtext .= $jieqiLang['article']['giftbuy_egold_low'] . '<br />';
        } else {
            if ($_REQUEST['currency'] == 'score' && $userscore < $jieqiGift['article'][$_REQUEST['type']]['sprice'] * $_POST['num']) {
                $errtext .= $jieqiLang['article']['giftbuy_score_low'] . '<br />';
            }
        }
        if (empty($errtext)) {
            if ($_REQUEST['currency'] == 'egold') {
                $ret = $users_handler->payout($jieqiUsers, intval($jieqiGift['article'][$_REQUEST['type']]['eprice'] * $_POST['num']));
                if (!$ret) {
                    jieqi_printfail($jieqiLang['article']['user_payout_failure']);
                }
            }
            $userset = jieqi_unserialize($jieqiUsers->getVar('setting', 'n'));
            $userset['gift'][$_REQUEST['type']] = intval($userset['gift'][$_REQUEST['type']]) + $_REQUEST['num'];
            $jieqiUsers->setVar('setting', serialize($userset));
            if ($_REQUEST['currency'] == 'score') {
                $scorenew = intval($jieqiUsers->getVar('score', 'n')) - $jieqiGift['article'][$_REQUEST['type']]['sprice'] * $_POST['num'];
                $jieqiUsers->setVar('score', $scorenew);
            }
            $jieqiUsers->saveToSession();
            $users_handler->insert($jieqiUsers);
            if (!empty($_REQUEST['jumpurl']) && preg_match('/^(\\/\\w+|https?:\\/\\/)/i', $_REQUEST['jumpurl'])) {
                jieqi_jumppage($_REQUEST['jumpurl'], LANG_DO_SUCCESS, $jieqiLang['article']['giftbuy_save_success']);
            } else {
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['giftbuy_save_success']);
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
        $jieqiTpl->assign('usermoney', $usermoney);
        foreach ($usermoney as $k => $v) {
            $jieqiTpl->assign('user' . $v, $v);
        }
        $jieqiTpl->assign('useremoney', $usermoney['egold']);
        $jieqiTpl->assign('userscore', $userscore);
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
        if (0 < $jieqiGift['article'][$_REQUEST['type']]['sprice']) {
            $jieqiTpl->assign('dft_money', $userscore);
            $jieqiTpl->assign('dft_price', $jieqiGift['article'][$_REQUEST['type']]['sprice']);
            $jieqiTpl->assign('dft_maxnum', floor($userscore / $jieqiGift['article'][$_REQUEST['type']]['sprice']));
        } else {
            $jieqiTpl->assign('dft_money', $usermoney['egold']);
            $jieqiTpl->assign('dft_price', $jieqiGift['article'][$_REQUEST['type']]['eprice']);
            if (0 < $jieqiGift['article'][$_REQUEST['type']]['eprice']) {
                $jieqiTpl->assign('dft_maxnum', floor($usermoney['egold'] / $jieqiGift['article'][$_REQUEST['type']]['eprice']));
            } else {
                $jieqiTpl->assign('dft_maxnum', 0);
            }
        }
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
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/giftbuy.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}