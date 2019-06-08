<?php

if (empty($_REQUEST['mod'])) {
    $_REQUEST['mod'] = 'system';
}
define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars($_REQUEST['mod']);
jieqi_checkpower($jieqiPower[$_REQUEST['mod']]['adminpower'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('power', JIEQI_MODULE_NAME);
if (0 < count($jieqiPower[$_REQUEST['mod']])) {
    if (isset($_POST['act']) && $_POST['act'] == 'update') {
        jieqi_checkpost();
        foreach ($jieqiPower[$_REQUEST['mod']] as $k => $v) {
            if (!isset($_POST[$k])) {
                $_POST[$k] = '';
            }
            if ($v['groups'] != $_POST[$k]) {
                $jieqiPower[$_REQUEST['mod']][$k]['groups'] = $_POST[$k];
                $power_handler->execute('UPDATE ' . jieqi_dbprefix('system_power') . ' SET pgroups=\'' . jieqi_dbslashes(serialize($_POST[$k])) . '\' WHERE modname=\'' . jieqi_dbslashes($_REQUEST['mod']) . '\' AND pname=\'' . jieqi_dbslashes($k) . '\'');
            }
        }
        jieqi_setconfigs('power', 'jieqiPower', $jieqiPower, $_REQUEST['mod']);
        include_once JIEQI_ROOT_PATH . '/class/logs.php';
        $logs_handler = JieqiLogsHandler::getInstance('JieqiLogsHandler');
        $logdata = array('logtype' => 3, 'logdata' => 'module:' . $_REQUEST['mod'], 'todata' => serialize($_REQUEST));
        $logs_handler->addlog($logdata);
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['edit_power_success']);
    } else {
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
        include_once JIEQI_ROOT_PATH . '/class/groups.php';
        $groups_handler = JieqiGroupsHandler::getInstance('JieqiGroupsHandler');
        $criteria = new CriteriaCompo();
        $criteria->setSort('groupid');
        $criteria->setOrder('ASC');
        $groups_handler->queryObjects($criteria);
        while ($v = $groups_handler->getObject()) {
            if ($v->getVar('groupid') != JIEQI_GROUP_ADMIN) {
                $groups[] = array('groupid' => $v->getVar('groupid'), 'name' => $v->getVar('name'));
            }
        }
        unset($criteria);
        $power_form = new JieqiThemeForm($jieqiLang['system']['edit_power'], 'power', JIEQI_URL . '/admin/power.php');
        foreach ($jieqiPower[$_REQUEST['mod']] as $k => $v) {
            $_POST[$k] = new JieqiFormCheckBox($v['caption'], $k, $v['groups']);
            foreach ($groups as $group) {
                $_POST[$k]->addOption($group['groupid'], $group['name']);
            }
            $power_form->addElement($_POST[$k], false);
        }
        $power_form->addElement(new JieqiFormHidden('mod', $_REQUEST['mod']));
        $power_form->addElement(new JieqiFormHidden('act', 'update'));
        $power_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $power_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['system']['save_power'], 'submit'));
        $jieqiTpl->setCaching(0);
        $jieqiTpl->assign('jieqi_contents', '<br />' . $power_form->render(JIEQI_FORM_MAX) . '<br />');
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
    }
} else {
    jieqi_msgwin(LANG_NOTICE, $jieqiLang['system']['no_usage_power']);
}