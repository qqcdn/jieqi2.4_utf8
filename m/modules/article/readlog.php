<?php

if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
if (!empty($_SESSION['jieqiUserId'])) {
    if (!defined('JIEQI_GLOBAL_INCLUDE')) {
        include_once '../global.php';
    }
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (empty($_REQUEST['id']) && is_numeric($_REQUEST['aid'])) {
        $_REQUEST['id'] = $_REQUEST['aid'];
    }
    $_REQUEST['id'] = intval($_REQUEST['id']);
    $_SESSION['jieqiUserId'] = intval($_SESSION['jieqiUserId']);
    if (!isset($jieqiConfigs['system']['usersetpath'])) {
        $jieqiConfigs['system']['usersetpath'] = 'userdata';
    }
    if (!isset($jieqiConfigs['article']['readlognum'])) {
        $jieqiConfigs['article']['readlognum'] = 20;
    }
    jieqi_getfilevars('system', $jieqiConfigs['system']['usersetpath'], $_SESSION['jieqiUserId'], 'jieqiUserdata');
    $jieqiUserdata['article']['lastread'] = array('articleid' => $_REQUEST['id'], 'time' => JIEQI_NOW_TIME);
    if (!isset($jieqiUserdata['article']['readlog'])) {
        $jieqiUserdata['article']['readlog'] = array();
    }
    $isexists = -1;
    foreach ($jieqiUserdata['article']['readlog'] as $k => $v) {
        if ($v['articleid'] == $_REQUEST['id']) {
            $isexists = $k;
            break;
        }
    }
    if (0 <= $isexists) {
        $tmpary = array();
        $tmpkey = 0;
        foreach ($jieqiUserdata['article']['readlog'] as $k => $v) {
            if ($k != $isexists) {
                $tmpary[$tmpkey] = $v;
                $tmpkey++;
            }
        }
        $jieqiUserdata['article']['readlog'] = $tmpary;
    }
    array_unshift($jieqiUserdata['article']['readlog'], array('articleid' => $_REQUEST['id'], 'time' => JIEQI_NOW_TIME));
    if ($jieqiConfigs['article']['readlognum'] < count($jieqiUserdata['article']['readlog'])) {
        array_pop($jieqiUserdata['article']['readlog']);
    }
    jieqi_setfilevars($jieqiConfigs['system']['usersetpath'], $_SESSION['jieqiUserId'], 'jieqiUserdata', $jieqiUserdata, 'system');
}