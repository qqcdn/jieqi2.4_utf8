<?php

function jieqi_apis_checkparams()
{
    global $jieqiLang;
    global $query;
    if (empty($jieqiLang['system']['apis'])) {
        jieqi_loadlang('apis', 'system');
    }
    if (!isset($_GET['siteid']) || !isset($_GET['sign'])) {
        jieqi_apis_printfail($jieqiLang['system']['apis_error_parameter']);
    }
    $_GET['siteid'] = intval($_GET['siteid']);
    if (!is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_libsite') . ' WHERE siteid = ' . $_GET['siteid'] . ' LIMIT 0, 1';
    $query->execute($sql);
    $libsite = $query->getRow();
    if (!$libsite) {
        jieqi_apis_printfail($jieqiLang['system']['apis_libsite_notexists']);
    }
    $libsite['siteset'] = jieqi_unserialize($libsite['siteset']);
    if (!is_array($libsite['siteset'])) {
        $libsite['siteset'] = array();
    }
    $get_sign = $_GET['sign'];
    unset($_GET['sign']);
    $my_sign = JieqiApiSign::makeSign($_GET, $libsite['getkey']);
    if ($get_sign != $my_sign) {
        jieqi_apis_printfail($jieqiLang['system']['apis_error_sign']);
    }
    $fieldrows = array();
    $fieldrows['logtime'] = JIEQI_NOW_TIME;
    $fieldrows['siteid'] = $_GET['siteid'];
    $fieldrows['fromip'] = jieqi_userip();
    if (!empty($_SERVER['PHP_SELF'])) {
        $fieldrows['logapi'] = $_SERVER['PHP_SELF'];
    } else {
        if (!empty($_SERVER['SCRIPT_NAME']) && substr($_SERVER['SCRIPT_NAME'], -4) == '.php') {
            $fieldrows['logapi'] = $_SERVER['SCRIPT_NAME'];
        } else {
            $fieldrows['logapi'] = '';
        }
    }
    $fieldrows['getstr'] = $_SERVER['QUERY_STRING'];
    $sql = $query->makeupsql(jieqi_dbprefix('system_apilog'), $fieldrows, 'INSERT');
    $query->execute($sql);
    return $libsite;
}
function jieqi_apis_printfail($error, $isarray = true)
{
    if ($isarray && is_string($error)) {
        $error = array('ret' => -1, 'msg' => $error);
    }
    jieqi_apis_return($error);
}
function jieqi_apis_return($ret)
{
    if (is_array($ret)) {
        $ret = serialize($ret);
        exit($ret);
    } else {
        exit($ret);
    }
}
include_once dirname(__FILE__) . '/apicommon.php';