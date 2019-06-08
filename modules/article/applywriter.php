<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
jieqi_loadlang('applywriter', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
if (jieqi_checkpower($jieqiPower['article']['newarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
    jieqi_printfail($jieqiLang['article']['has_been_writer']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = 'SELECT * FROM ' . jieqi_dbprefix('system_persons') . ' WHERE uid = ' . intval($_SESSION['jieqiUserId']) . ' LIMIT 0, 1';
$res = $query->execute($sql);
$persons = $query->getRow($res);
if (!$persons && !empty($jieqiConfigs['article']['writercontactfirst'])) {
    jieqi_jumppage(JIEQI_LOCAL_URL . '/personedit.php?nextstep=applywriter', $jieqiLang['article']['apply_no_contact_title'], $jieqiLang['article']['apply_no_contact']);
}
$sql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_applywriter') . ' WHERE applyuid = ' . intval($_SESSION['jieqiUserId']) . ' AND applyflag = 0';
$res = $query->execute($sql);
$row = $query->getRow($res);
if (1 <= $row['cot']) {
    jieqi_printfail($jieqiLang['article']['apply_already_post']);
}
if (isset($_POST['act']) && $_POST['act'] == 'applywriter' || isset($_REQUEST['agree']) && $_REQUEST['agree'] == '1') {
    jieqi_checkpost();
    include_once $jieqiModules['article']['path'] . '/class/applywriter.php';
    $apply_handler = JieqiApplywriterHandler::getInstance('JieqiApplywriterHandler');
    $newApply = $apply_handler->create();
    $newApply->setVar('siteid', JIEQI_SITE_ID);
    $newApply->setVar('applytime', JIEQI_NOW_TIME);
    $newApply->setVar('applyuid', $_SESSION['jieqiUserId']);
    $newApply->setVar('applyname', $_SESSION['jieqiUserName']);
    $newApply->setVar('authtime', 0);
    $newApply->setVar('authuid', 0);
    $newApply->setVar('authname', '');
    $newApply->setVar('applytitle', '');
    $newApply->setVar('applytext', $_POST['applytext']);
    $newApply->setVar('applywords', jieqi_strwords($_POST['applytext']));
    $newApply->setVar('authnote', '');
    if ($jieqiConfigs['article']['checkappwriter'] == 1) {
        $newApply->setVar('applyflag', 0);
        $apply_handler->insert($newApply);
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->setCaching(0);
        $jieqiTpl->assign('jieqi_contents', jieqi_msgbox(LANG_DO_SUCCESS, $jieqiLang['article']['apply_submit_success']));
        include_once JIEQI_ROOT_PATH . '/footer.php';
    } else {
        include_once JIEQI_ROOT_PATH . '/class/groups.php';
        jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
        $key = array_search($jieqiConfigs['article']['writergroup'], $jieqiGroups);
        if ($key == false) {
            jieqi_printfail($jieqiLang['article']['no_writer_group']);
        } else {
            if ($key == JIEQI_GROUP_ADMIN) {
                jieqi_printfail($jieqiLang['article']['no_writer_admin']);
            } else {
                include_once JIEQI_ROOT_PATH . '/class/users.php';
                $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                $jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
                $jieqiUsers->setVar('groupid', $key);
                $_SESSION['jieqiUserGroup'] = $jieqiUsers->getVar('groupid');
                $users_handler->insert($jieqiUsers);
                $newApply->setVar('applyflag', 1);
                $apply_handler->insert($newApply);
                jieqi_jumppage($jieqiModules['article']['url'] . '/myarticle.php', LANG_DO_SUCCESS, sprintf($jieqiLang['article']['apply_writer_success'], $jieqiConfigs['article']['writergroup']));
            }
        }
    }
} else {
    include_once JIEQI_ROOT_PATH . '/header.php';
    if (!$persons) {
        $nopersons = 1;
        $personsvars = array();
    } else {
        $nopersons = 0;
        include_once JIEQI_ROOT_PATH . '/include/funpersons.php';
        $personsvars = jieqi_system_personsvars($persons, 's');
    }
    $jieqiTpl->assign('nopersons', $nopersons);
    $jieqiTpl->assign_by_ref('personsvars', $personsvars);
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/applywriter.html';
    include_once JIEQI_ROOT_PATH . '/footer.php';
}