<?php

function jieqi_dologout()
{
    include_once JIEQI_ROOT_PATH . '/class/online.php';
    $online_handler = JieqiOnlineHandler::getInstance('JieqiOnlineHandler');
    $criteria = new CriteriaCompo(new Criteria('sid', session_id()));
    $criteria->add(new Criteria('uid', intval($_SESSION['jieqiUserId'])), 'OR');
    $online_handler->delete($criteria);
    header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
    if (!empty($_COOKIE['jieqiUserInfo'])) {
        setcookie('jieqiUserInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
    }
    if (!empty($_COOKIE['jieqiOnlineInfo'])) {
        setcookie('jieqiOnlineInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
    }
    if (!empty($_COOKIE[session_name()])) {
        setcookie(session_name(), '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
    }
    $_SESSION = array();
    @session_destroy();
}