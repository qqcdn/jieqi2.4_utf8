<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_loadlang('users', 'system');
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = 'SELECT * FROM ' . jieqi_dbprefix('system_persons') . ' WHERE uid = ' . intval($_SESSION['jieqiUserId']) . ' LIMIT 0, 1';
$res = $query->execute($sql);
$persons = $query->getRow($res);
if (!$persons) {
    $persons = array();
    $noperson = true;
} else {
    $noperson = false;
    if ($persons['denyedit']) {
        jieqi_printfail($jieqiLang['system']['persons_is_denyedit']);
    }
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
            $postrows['uid'] = intval($_SESSION['jieqiUserId']);
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
            if (!empty($persons['addvars'])) {
                $addary = @jieqi_unserialize($persons['addvars']);
            }
            if (empty($addary)) {
                $addary = array();
            }
            if (isset($_POST['p_addvars']) && is_array($_POST['p_addvars'])) {
                foreach ($_POST['p_addvars'] as $k => $v) {
                    $addary[$k] = $v;
                }
            }
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $fk => $fv) {
                    if ($fv['error'] == 0 && preg_match('/^\\w+$/i', $fk) && preg_match('/\\.(gif|jpg|jpeg|png|bmp)$/i', $fv['name'])) {
                        $postfix = strrchr(trim(strtolower($fv['name'])), '.');
                        $sfile = JIEQI_ROOT_PATH . '/files/system/person' . jieqi_getsubdir($postrows['uid']) . '/' . $postrows['uid'];
                        jieqi_checkdir($sfile);
                        if (!empty($addary[$fk]) && is_file($sfile . '/' . $addary[$fk])) {
                            jieqi_delfile($sfile . '/' . $addary[$fk]);
                        }
                        $sfile .= '/' . $fk . $postfix;
                        jieqi_copyfile($fv['tmp_name'], $sfile, 511, true);
                        $addary[$fk] = $fk . $postfix;
                    }
                }
            }
            if (empty($addary)) {
                $postrows['addvars'] = '';
            } else {
                $postrows['addvars'] = serialize($addary);
            }
            if ($noperson) {
                $postrows['ownerid'] = 0;
                $postrows['ownermark'] = '';
                $postrows['editdata'] = '';
                $postrows['edittime'] = 0;
                $postrows['ugroup'] = 0;
                $postrows['ulevel'] = 0;
                $postrows['authstate'] = 0;
                $postrows['audittime'] = 0;
                $postrows['isaudit'] = 0;
                $postrows['denyedit'] = 0;
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
                $sql = 'UPDATE `' . jieqi_dbprefix('system_persons') . '` SET ' . $fields . ' WHERE uid = ' . intval($_SESSION['jieqiUserId']);
            }
            if (!$query->execute($sql)) {
                jieqi_printfail($jieqiLang['system']['persons_edit_failure']);
            } else {
                if ($_REQUEST['nextstep'] == 'applywriter') {
                    jieqi_jumppage($jieqiModules['article']['url'] . '/applywriter.php', LANG_DO_SUCCESS, $jieqiLang['system']['persons_writer_success']);
                } else {
                    jieqi_jumppage(JIEQI_LOCAL_URL . '/persondetail.php', LANG_DO_SUCCESS, $jieqiLang['system']['persons_edit_success']);
                }
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'edit':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        include_once JIEQI_ROOT_PATH . '/include/funpersons.php';
        $personsvars = jieqi_system_personsvars($persons, 'e');
        $jieqiTpl->assign_by_ref('personsvars', $personsvars);
        $nextstep = isset($_REQUEST['nextstep']) ? $_REQUEST['nextstep'] : '';
        $jieqiTpl->assign_by_ref('nextstep', $nextstep);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/personedit.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}