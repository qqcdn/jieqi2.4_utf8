<?php

define('JIEQI_MODULE_NAME', 'obook');
require_once '../../../global.php';
if (empty($_REQUEST['oid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('paid', JIEQI_MODULE_NAME);
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
$_REQUEST['oid'] = intval($_REQUEST['oid']);
$obook = $obook_handler->get($_REQUEST['oid']);
if (!$obook) {
    jieqi_printfail($jieqiLang['obook']['obook_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
$sumegold = intval($obook->getVar('sumegold', 'n'));
$sumesilver = intval($obook->getVar('sumesilver', 'n'));
$paidemoney = intval($obook->getVar('paidemoney', 'n'));
$paidmoney = intval($obook->getVar('paidmoney', 'n'));
$sumemoney = intval($obook->getVar('sumemoney', 'n'));
$remainemoney = $sumemoney - $paidemoney;
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'post':
        jieqi_checkpost();
        $errtext = '';
        $_POST['payemoney'] = intval(trim($_POST['payemoney']));
        $_POST['paymoney'] = floor(floatval(trim($_POST['paymoney'])) * 100);
        if ($_POST['payemoney'] + $paidemoney < 0) {
            $errtext .= $jieqiLang['obook']['payemoney_over_sub'] . '<br />';
        }
        if ($_POST['paymoney'] + $paidmoney < 0) {
            $errtext .= $jieqiLang['obook']['paymoney_over_sub'] . '<br />';
        }
        if (empty($errtext)) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            $postrows = array();
            $postrows['paytime'] = JIEQI_NOW_TIME;
            $postrows['userid'] = intval($obook->getVar('authorid', 'n'));
            $postrows['username'] = $obook->getVar('author', 'n');
            $postrows['masterid'] = intval($_SESSION['jieqiUserId']);
            $postrows['obookid'] = $obook->getVar('obookid', 'n');
            $postrows['obookname'] = $obook->getVar('obookname', 'n');
            $postrows['articleid'] = $obook->getVar('articleid', 'n');
            $postrows['sumegold'] = $obook->getVar('sumegold', 'n');
            $postrows['sumesilver'] = $obook->getVar('sumesilver', 'n');
            $postrows['sumemoney'] = $obook->getVar('sumemoney', 'n');
            $postrows['paidemoney'] = $obook->getVar('paidemoney', 'n');
            $postrows['payemoney'] = $_POST['payemoney'];
            $postrows['remainemoney'] = $postrows['sumemoney'] - $postrows['paidemoney'] - $postrows['payemoney'];
            $postrows['summoney'] = 0;
            $postrows['paymoney'] = $_POST['paymoney'];
            $postrows['remainmoney'] = 0;
            $postrows['payinfo'] = '';
            $postrows['paidcurrency'] = isset($_POST['paidcurrency']) ? intval($_POST['paidcurrency']) : 0;
            $postrows['paidtype'] = isset($_POST['paidtype']) ? intval($_POST['paidtype']) : 0;
            $postrows['paidflag'] = 0;
            $postrows['paynote'] = $_POST['paynote'];
            $fields = '';
            $values = '';
            foreach ($postrows as $k => $v) {
                if ($fields != '') {
                    $fields .= ', ';
                }
                $fields .= '`' . $k . '`';
                if ($values != '') {
                    $values .= ', ';
                }
                $values .= '\'' . jieqi_dbslashes($v) . '\'';
            }
            $sql = 'INSERT INTO `' . jieqi_dbprefix('obook_paidlog') . '` (' . $fields . ') VALUES (' . $values . ');';
            if (!$query->execute($sql)) {
                jieqi_printfail($jieqiLang['obook']['database_save_error']);
            } else {
                $sql = 'UPDATE ' . jieqi_dbprefix('obook_obook') . ' SET paidemoney = paidemoney + ' . $_POST['payemoney'] . ', paidmoney = paidmoney + ' . $_POST['paymoney'] . ', paytime = ' . intval(JIEQI_NOW_TIME) . ' WHERE obookid = ' . $_REQUEST['oid'];
                $query->execute($sql);
            }
            jieqi_jumppage($jieqiModules['obook']['url'] . '/admin/paidnew.php?oid=' . $_REQUEST['oid'], LANG_DO_SUCCESS, $jieqiLang['obook']['paidnew_save_success']);
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('obook_static_url', $obook_static_url);
        $jieqiTpl->assign('obook_dynamic_url', $obook_dynamic_url);
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_persons') . ' WHERE uid = ' . intval($obook->getVar('authorid', 'n')) . ' LIMIT 0, 1';
        $res = $query->execute($sql);
        $persons = $query->getRow($res);
        if (!$persons) {
            $persons = array();
        }
        include_once JIEQI_ROOT_PATH . '/include/funpersons.php';
        $personsvars = jieqi_system_personsvars($persons, 'e');
        $jieqiTpl->assign_by_ref('personsvars', $personsvars);
        $jieqiTpl->assign('obookid', $obook->getVar('obookid'));
        $jieqiTpl->assign('obookname', $obook->getVar('obookname'));
        $jieqiTpl->assign('articleid', $obook->getVar('articleid'));
        $jieqiTpl->assign('postdate', date(JIEQI_DATE_FORMAT, $obook->getVar('postdate')));
        $jieqiTpl->assign('lastupdate', date(JIEQI_DATE_FORMAT, $obook->getVar('lastupdate')));
        $jieqiTpl->assign('authorid', $obook->getVar('authorid'));
        $jieqiTpl->assign('author', $obook->getVar('author'));
        $jieqiTpl->assign('sumegold', $sumegold);
        $jieqiTpl->assign('sumesilver', $sumesilver);
        $jieqiTpl->assign('sumemoney', $sumemoney);
        $jieqiTpl->assign('paidemoney', $paidemoney);
        $jieqiTpl->assign('remainemoney', $remainemoney);
        $jieqiTpl->assign('summoney', $obook->getVar('summoney'));
        $jieqiTpl->assign('paidmoney', $obook->getVar('paidmoney'));
        $jieqiTpl->assign('remainmoney', intval($obook->getVar('summoney')) - intval($obook->getVar('paidmoney')));
        $jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
        foreach ($jieqiOption['obook'] as $k => $v) {
            $jieqiTpl->assign($k, $jieqiOption['obook'][$k]);
        }
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['obook']['path'] . '/templates/admin/paidnew.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}