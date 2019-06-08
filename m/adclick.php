<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
if ($jieqiUsersGroup != JIEQI_GROUP_GUEST && is_numeric($_REQUEST['id'])) {
    include_once JIEQI_ROOT_PATH . '/class/users.php';
    $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    $jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
    if (!$jieqiUsers) {
        jieqi_printfail(LANG_NO_USER);
    }
    $userset = jieqi_unserialize($jieqiUsers->getVar('setting', 'n'));
    $today = date('Y-m-d');
    jieqi_getconfigs('system', 'action', 'jieqiAction');
    $adclickscore = intval($jieqiAction['system']['adclick']['earnscore']);
    $maxadclick = intval($jieqiAction['system']['adclick']['paymax']);
    if ($maxadclick <= 0) {
        $maxadclick = 1;
    }
    if (isset($userset['addate']) && $userset['addate'] == $today && (int) $maxadclick <= (int) $userset['adnum']) {
    } else {
        $rightclick = true;
        $_REQUEST['id'] = intval($_REQUEST['id']);
        if (empty($_SESSION['jieqiAdClick']) || 1024 < strlen($_SESSION['jieqiAdClick'])) {
            $_SESSION['jieqiAdClick'] = $_REQUEST['id'];
        } else {
            if (strpos($_SESSION['jieqiAdClick'], strval($_REQUEST['id'])) === false) {
                ${$_SESSION}['jieqiAdClick'] .= '|' . $_REQUEST['id'];
            } else {
                $rightclick = false;
            }
        }
        if ($rightclick) {
            if (isset($userset['addate']) && $userset['addate'] == $today) {
                $userset['adnum'] = (int) $userset['adnum'] + 1;
            } else {
                $userset['addate'] = $today;
                $userset['adnum'] = 1;
            }
            $jieqiUsers->setVar('setting', serialize($userset));
            $jieqiUsers->saveToSession();
            $users_handler->insert($jieqiUsers);
            include_once JIEQI_ROOT_PATH . '/include/funaction.php';
            $actions = array('actname' => 'adclick', 'actnum' => 1);
            jieqi_system_actiondo($actions, $jieqiUsers);
        }
    }
}
if (!empty($_REQUEST['url'])) {
    header('Location: ' . jieqi_headstr($_REQUEST['url']));
}