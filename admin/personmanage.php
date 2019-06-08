<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_NO_USER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
jieqi_loadlang('users', 'system');
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$users = $users_handler->get($_REQUEST['id']);
if (!is_object($users)) {
    jieqi_printfail(LANG_NO_USER);
}
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = 'SELECT * FROM ' . jieqi_dbprefix('system_persons') . ' WHERE uid = ' . intval($_REQUEST['id']) . ' LIMIT 0, 1';
$res = $query->execute($sql);
$persons = $query->getRow($res);
if (!$persons) {
    $persons = array();
    $noperson = true;
} else {
    $noperson = false;
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'edit';
}
switch ($_POST['act']) {
    case 'update':
        jieqi_checkpost();
        $errtext = '';
        $_POST = jieqi_funtoarray('trim', $_POST);
        if (!isset($_POST['p_realname']) || strlen($_POST['p_realname']) == 0) {
            $errtext .= $jieqiLang['system']['persons_need_realname'] . '<br />';
        }
        if (empty($errtext)) {
            $postrows = array();
            $postrows['uid'] = $_REQUEST['id'];
            $postrows['realname'] = $_POST['p_realname'];
            $postrows['gender'] = intval($_POST['p_gender']);
            $postrows['birthyear'] = $_POST['p_birthyear'];
            $postrows['birthmonth'] = $_POST['p_birthmonth'];
            $postrows['birthday'] = $_POST['p_birthday'];
            $postrows['telephone'] = $_POST['p_telephone'];
            $postrows['mobilephone'] = $_POST['p_mobilephone'];
            $postrows['idcardtype'] = $_POST['p_idcardtype'];
            $postrows['idcard'] = $_POST['p_idcard'];
            $postrows['idcardimage'] = 0;
            $postrows['address'] = $_POST['p_address'];
            $postrows['zipcode'] = $_POST['p_zipcode'];
            $postrows['areaid'] = intval($_POST['p_areaid']);
            $postrows['country'] = $_POST['p_country'];
            $postrows['city'] = $_POST['p_city'];
            $postrows['district'] = $_POST['p_district'];
            $postrows['banktype'] = $_POST['p_banktype'];
            $postrows['bankname'] = $_POST['p_bankname'];
            $postrows['bankcard'] = $_POST['p_bankcard'];
            $postrows['bankuser'] = $_POST['p_bankuser'];
            $postrows['bankuinfo'] = $_POST['p_bankuinfo'];
            $postrows['myprofile'] = $_POST['p_profile'];
            $postrows['mynote'] = $_POST['p_mynote'];
            $postrows['mynotice'] = $_POST['p_mynotice'];
            $postrows['ownerid'] = intval($_SESSION['jieqiUserId']);
            $postrows['ownermark'] = $_POST['p_ownermark'];
            $postrows['denyedit'] = empty($_POST['p_denyedit']) ? 0 : 1;
            if ($noperson) {
                $postrows['addvars'] = '';
                $postrows['editdata'] = '';
                $postrows['edittime'] = 0;
                $postrows['ugroup'] = 0;
                $postrows['ulevel'] = 0;
                $postrows['authstate'] = 0;
                $postrows['audittime'] = 0;
                $postrows['isaudit'] = 0;
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
                $sql = 'INSERT INTO `' . jieqi_dbprefix('system_persons') . '` (' . $fields . ') VALUES (' . $values . ');';
            } else {
                $fields = '';
                foreach ($postrows as $k => $v) {
                    if ($fields != '') {
                        $fields .= ', ';
                    }
                    $fields .= '`' . $k . '` = \'' . jieqi_dbslashes($v) . '\'';
                }
                $sql = 'UPDATE `' . jieqi_dbprefix('system_persons') . '` SET ' . $fields . ' WHERE uid = ' . intval($_REQUEST['id']);
            }
            if (!$query->execute($sql)) {
                jieqi_printfail($jieqiLang['system']['persons_edit_failure']);
            } else {
                jieqi_jumppage(JIEQI_URL . '/admin/personinfo.php?id=' . $_REQUEST['id'], LANG_DO_SUCCESS, $jieqiLang['system']['persons_edit_success']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'edit':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        include_once JIEQI_ROOT_PATH . '/include/funpersons.php';
        $personsvars = jieqi_system_personsvars($persons, 'e');
        $jieqiTpl->assign_by_ref('personsvars', $personsvars);
        $uservals = jieqi_query_rowvars($users, 's');
        $jieqiTpl->assign_by_ref('uservals', $uservals);
        $jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/personmanage.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}