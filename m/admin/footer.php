<?php

if (!empty($jieqiTset['jieqi_contents_template'])) {
    if (!isset($jieqiTset['jieqi_contents_cacheid'])) {
        $jieqiTset['jieqi_contents_cacheid'] = NULL;
    }
    if (!isset($jieqiTset['jieqi_contents_compileid'])) {
        $jieqiTset['jieqi_contents_compileid'] = NULL;
    }
    $jieqiTpl->assign('jieqi_contents', $jieqiTpl->fetch($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_cacheid'], $jieqiTset['jieqi_contents_compileid']));
}
if (!empty($_REQUEST['ajax_request']) && !empty($_REQUEST['ajax_gets'])) {
    header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
    header('Cache-Control:no-cache');
    if (is_array($_REQUEST['ajax_gets'])) {
        $out_var = array();
        foreach ($_REQUEST['ajax_gets'] as $v) {
            if (isset($jieqiTpl->_tpl_vars[$v])) {
                $out_var[$v] =& $jieqiTpl->_tpl_vars[$v];
            }
        }
    } else {
        if (isset($jieqiTpl->_tpl_vars[$_REQUEST['ajax_gets']])) {
            $out_var =& $jieqiTpl->_tpl_vars[$_REQUEST['ajax_gets']];
        } else {
            $out_var = '';
        }
    }
    if (is_array($out_var)) {
        echo serialize($out_var);
    }
    echo $out_var;
    exit;
}
$jieqiTpl->setCaching(0);
$jieqiTpl->display(JIEQI_ROOT_PATH . '/templates/admin/main.html');
jieqi_freeresource();
if (defined('JIEQI_DEBUG_MODE') && 0 < JIEQI_DEBUG_MODE) {
    $runtime = explode(' ', microtime());
    $debuginfo = 'Processed in ' . round($runtime[1] + $runtime[0] - JIEQI_START_TIME, 6) . ' second(s), ';
    if (function_exists('memory_get_usage')) {
        $debuginfo .= 'Memory usage ' . round(memory_get_usage() / 1024) . 'K, ';
    }
    $sqllog = array();
    if (defined('JIEQI_DB_CONNECTED')) {
        $instance = JieqiDatabase::retInstance();
        if (!empty($instance)) {
            foreach ($instance as $db) {
                $sqllog = array_merge($sqllog, $db->sqllog('ret'));
            }
        }
    }
    $queries = count($sqllog);
    $debuginfo .= $queries . ' queries, ';
    if (defined('JIEQI_USE_GZIP') && 0 < JIEQI_USE_GZIP) {
        $debuginfo .= 'Gzip enabled.';
    } else {
        $debuginfo .= 'Gzip disabled.';
    }
    if (0 < $queries) {
        foreach ($sqllog as $sql) {
            $debuginfo .= '<br />' . $sql;
        }
    }
    echo '<div class="divbox">' . $debuginfo . '</div>';
}
exit;