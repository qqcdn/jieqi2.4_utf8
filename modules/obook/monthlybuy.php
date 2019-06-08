<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_loadlang('monthly', JIEQI_MODULE_NAME);
if (empty($jieqiConfigs['obook']['monthly'])) {
    jieqi_printfail($jieqiLang['obook']['monthly_not_open']);
}
jieqi_checklogin();
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$users = $users_handler->get($_SESSION['jieqiUserId']);
if (!is_object($users)) {
    jieqi_printfail($jieqiLang['obook']['need_user_login']);
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'monthly', 'jieqiMonthly');
jieqi_loadlang('list', JIEQI_MODULE_NAME);
switch ($_POST['act']) {
    case 'buy':
        jieqi_checkpost();
        $errtext = '';
        $_REQUEST['buytype'] = intval($_REQUEST['buytype']);
        if ($_REQUEST['buytype'] <= 0 || $jieqiMonthly['obook']['mode'] == 1 && !isset($jieqiMonthly['obook']['options'][$_REQUEST['buytype']])) {
            $errtext .= $jieqiLang['obook']['monthly_buytype_error'];
        }
        $needegold = $jieqiMonthly['obook']['mode'] == 1 ? intval($jieqiMonthly['obook']['options'][$_REQUEST['buytype']]) : intval($jieqiMonthly['obook']['megold']) * $_REQUEST['buytype'];
        if ($needegold <= 0) {
            $errtext .= $jieqiLang['obook']['monthly_needegold_error'];
        }
        $usermoney = $users->getEmoney();
        if ($usermoney['emoney'] < $needegold) {
            $errtext .= sprintf($jieqiLang['obook']['monthly_egold_low'], $jieqiModules['pay']['url'] . 'buyegold.php');
        }
        if (empty($errtext)) {
            $ret = $users_handler->payout($users, $needegold);
            if (!$ret) {
                jieqi_printfail($jieqiLang['obook']['user_payout_failure']);
            }
            $begintime = $users->getVar('overtime');
            if ($begintime < JIEQI_NOW_TIME) {
                $begintime = JIEQI_NOW_TIME;
            }
            $overtime = mktime(date('H', $begintime), date('i', $begintime), date('s', $begintime), date('m', $begintime) + $_REQUEST['buytype'], date('d', $begintime), date('Y', $begintime));
            $users->setVar('overtime', $overtime);
            $users_handler->insert($users);
            $_SESSION['jieqiUserOvertime'] = $overtime;
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            $fieldrows = array();
            $fieldrows['buytime'] = JIEQI_NOW_TIME;
            $fieldrows['userid'] = $users->getVar('uid', 'n');
            $fieldrows['username'] = $users->getVar('name', 'n');
            $fieldrows['month'] = $_REQUEST['buytype'];
            $fieldrows['vipbegin'] = $begintime;
            $fieldrows['vipend'] = $overtime;
            $fieldrows['egold'] = $needegold;
            $fieldrows['money'] = 0;
            $fieldrows['paytype'] = 0;
            $fieldrows['paynote'] = '';
            $fieldrows['payflag'] = 0;
            $sql = $query->makeupsql(jieqi_dbprefix('obook_monthlylog'), $fieldrows, 'INSERT');
            $query->execute($sql);
            include_once JIEQI_ROOT_PATH . '/header.php';
            $jieqiTpl->assign('jieqi_contents', jieqi_msgbox(LANG_DO_SUCCESS, sprintf($jieqiLang['obook']['monthly_buy_success'], date('Y-m-d', $overtime))));
            include_once JIEQI_ROOT_PATH . '/footer.php';
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $usermoney = $users->getEmoney();
        $jieqiTpl->assign('egold', $usermoney['egold']);
        $jieqiTpl->assign('esilver', $usermoney['esilver']);
        $jieqiTpl->assign('useregold', $usermoney['egold']);
        $jieqiTpl->assign('useresilver', $usermoney['esilver']);
        $jieqiTpl->assign('useremoney', $usermoney['egold']);
        $jieqiTpl->assign('usermoney', $usermoney);
        $overtime = $users->getVar('overtime');
        if (0 < $overtime && JIEQI_NOW_TIME < $overtime) {
            $monthly = 1;
        } else {
            $monthly = 0;
        }
        $jieqiTpl->assign('overtime', $overtime);
        $jieqiTpl->assign('monthly', $monthly);
        $jieqiTpl->assign('state', $users->getVar('state'));
        $jieqimonthly = jieqi_funtoarray('jieqi_htmlstr', $jieqiMonthly['obook']);
        $jieqiTpl->assign_by_ref('jieqimonthly', $jieqimonthly);
        $buytype = isset($_REQUEST['buytype']) ? intval($_REQUEST['buytype']) : intval($jieqiMonthly['obook']['default']);
        $jieqiTpl->assign('buytype', $buytype);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/monthlybuy.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}