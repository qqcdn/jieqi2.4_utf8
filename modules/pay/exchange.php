<?php

define('JIEQI_MODULE_NAME', 'pay');
require_once '../../global.php';
jieqi_checklogin();
jieqi_getconfigs('system', 'configs');
jieqi_loadlang('exchange', JIEQI_MODULE_NAME);
$exchangerate = isset($jieqiConfigs['system']['exchangerate']) ? $jieqiConfigs['system']['exchangerate'] : 0;
if ($exchangerate <= 0) {
    jieqi_printfail($jieqiLang['pay']['exchange_is_deny']);
}
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
if (!is_object($jieqiUsers)) {
    jieqi_printfail(LANG_NO_USER);
}
include_once JIEQI_ROOT_PATH . '/include/funusers.php';
$usermoney = $jieqiUsers->getEmoney();
$exchangemin = isset($jieqiConfigs['system']['exchangemin']) ? $jieqiConfigs['system']['exchangemin'] : 1;
if ($exchangemin < 0) {
    $exchangemin = 0;
}
switch ($_POST['act']) {
    case 'post':
        jieqi_checkpost();
        if (!isset($_REQUEST['money']) || !is_numeric($_REQUEST['money'])) {
            jieqi_printfail($jieqiLang['pay']['exchange_need_money']);
        }
        $paymoney = round($_REQUEST['money'] * 100);
        $esilver = intval($usermoney['esilver']);
        $egold = intval($usermoney['egold']);
        if (0 < $exchangemin && $_REQUEST['money'] < $exchangemin) {
            jieqi_printfail(sprintf($jieqiLang['pay']['exchange_over_min'], $exchangemin));
        } else {
            if ($paymoney <= 0) {
                jieqi_printfail($jieqiLang['pay']['exchange_over_zero']);
            } else {
                if ($esilver < $paymoney) {
                    jieqi_printfail($jieqiLang['pay']['exchange_over_max']);
                }
            }
        }
        $addegold = floor($paymoney * $exchangerate / 100);
        $jieqiUsers->setVar('esilver', $esilver - $paymoney);
        $jieqiUsers->setVar('egold', $egold + $addegold);
        $jieqiUsers->saveToSession();
        $ret = $users_handler->insert($jieqiUsers);
        if ($ret) {
            include_once $jieqiModules['pay']['path'] . '/class/paylog.php';
            $paylog_handler = JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
            $paylog = $paylog_handler->create();
            $paylog->setVar('siteid', JIEQI_SITE_ID);
            $paylog->setVar('buytime', JIEQI_NOW_TIME);
            $paylog->setVar('buydate', date('Ymd', JIEQI_NOW_TIME));
            $paylog->setVar('buymonth', date('Ym', JIEQI_NOW_TIME));
            $paylog->setVar('rettime', JIEQI_NOW_TIME);
            $paylog->setVar('buyid', $_SESSION['jieqiUserId']);
            $paylog->setVar('buyname', $_SESSION['jieqiUserName']);
            $paylog->setVar('buyinfo', '');
            $paylog->setVar('moneytype', -11);
            $paylog->setVar('money', $paymoney);
            $paylog->setVar('egoldtype', 0);
            $paylog->setVar('egold', $addegold);
            $paylog->setVar('paytype', 'exchange');
            $paylog->setVar('acttype', 11);
            $paylog->setVar('actlog', '');
            $paylog->setVar('retinfo', '');
            $paylog->setVar('masterid', 0);
            $paylog->setVar('mastername', '');
            $paylog->setVar('masterinfo', '');
            $paylog->setVar('note', '');
            $paylog->setVar('payflag', 1);
            $paylog_handler->insert($paylog);
            jieqi_jumppage(JIEQI_URL . '/userdetail.php', LANG_DO_SUCCESS, $jieqiLang['pay']['exchange_egold_success']);
        } else {
            jieqi_printfail($jieqiLang['pay']['exchange_db_error']);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('url_exchange', $jieqiModules['pay']['url'] . '/exchange.php');
        $uservals = jieqi_system_usersvars($jieqiUsers);
        $uservals['egold'] = $usermoney['egold'];
        $uservals['esilver'] = $usermoney['esilver'];
        $uservals['emoney'] = $usermoney['emoney'];
        $jieqiTpl->assign_by_ref('uservals', $uservals);
        foreach ($uservals as $k => $v) {
            $jieqiTpl->assign_by_ref($k, $uservals[$k]);
        }
        $jieqiTpl->assign('exchangerate', $exchangerate);
        $jieqiTpl->assign('exchangemin', $exchangemin);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/exchange.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}