<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
if (empty($_REQUEST['uid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('settle', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
$_REQUEST['uid'] = intval($_REQUEST['uid']);
$users = $users_handler->get($_REQUEST['uid']);
if (!$users) {
    jieqi_printfail($jieqiLang['system']['users_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$usermoney = $users->getEmoney();
$esilver = intval($usermoney['esilver']);
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'post':
        jieqi_checkpost();
        $errtext = '';
        if (!isset($_REQUEST['payamount']) || !is_numeric($_REQUEST['payamount'])) {
            jieqi_printfail($jieqiLang['system']['settle_need_payamount']);
        }
        $payamount = round($_REQUEST['payamount'] * 100);
        if ($payamount <= 0) {
            jieqi_printfail($jieqiLang['system']['settle_over_zero']);
        } else {
            if ($esilver < $payamount) {
                jieqi_printfail($jieqiLang['system']['settle_over_max']);
            }
        }
        $paymoney = isset($_REQUEST['paymoney']) && is_numeric($_REQUEST['paymoney']) ? round($_REQUEST['paymoney'] * 100) : $payamount;
        if (empty($errtext)) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET esilver = esilver - ' . $payamount . ' WHERE uid = ' . $_REQUEST['uid'];
            if (!$query->execute($sql)) {
                jieqi_printfail($jieqiLang['system']['database_save_error']);
            } else {
                $postrows = array();
                $postrows['logtime'] = JIEQI_NOW_TIME;
                $postrows['userid'] = intval($users->getVar('uid', 'n'));
                $postrows['username'] = $users->getVar('name', 'n');
                $postrows['masterid'] = intval($_SESSION['jieqiUserId']);
                $postrows['master'] = $_SESSION['jieqiUserName'];
                $postrows['payamount'] = $payamount;
                $postrows['paymoney'] = $paymoney;
                $postrows['paytype'] = isset($_REQUEST['paytype']) ? intval($_REQUEST['paytype']) : 0;
                $postrows['payinfo'] = isset($_REQUEST['payinfo']) ? $_REQUEST['payinfo'] : '';
                $postrows['paynote'] = isset($_REQUEST['paynote']) ? $_REQUEST['paynote'] : '';
                $sql = $query->makeupsql(jieqi_dbprefix('system_settlelog'), $postrows, 'INSERT');
                $query->execute($sql);
            }
            jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['settle_save_success']);
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_persons') . ' WHERE uid = ' . intval($users->getVar('uid', 'n')) . ' LIMIT 0, 1';
        $res = $query->execute($sql);
        $persons = $query->getRow($res);
        if (!$persons) {
            $persons = array();
        }
        include_once JIEQI_ROOT_PATH . '/include/funpersons.php';
        $personsvars = jieqi_system_personsvars($persons, 'e');
        $jieqiTpl->assign_by_ref('personsvars', $personsvars);
        $jieqiTpl->assign('uid', $users->getVar('uid'));
        $jieqiTpl->assign('uname', $users->getVar('uname'));
        $jieqiTpl->assign('name', $users->getVar('name'));
        $jieqiTpl->assign('esilver', $esilver);
        $jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
        foreach ($jieqiOption['system'] as $k => $v) {
            $jieqiTpl->assign($k, $jieqiOption['system'][$k]);
        }
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['system']['path'] . '/templates/admin/settlenew.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}