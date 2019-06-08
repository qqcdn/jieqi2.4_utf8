<?php

if (function_exists('jieqi_hooks_footer')) {
    jieqi_hooks_footer();
}
if (!empty($jieqiTset['jieqi_contents_template']) && !defined('JIEQI_INCLUDE_COMPILED_INC')) {
    define('JIEQI_INCLUDE_COMPILED_INC', 1);
    if (defined('JIEQI_THEME_ROOTNEW') && is_file(str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $jieqiTset['jieqi_contents_template']))) {
        $jieqiTset['jieqi_contents_template'] = str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $jieqiTset['jieqi_contents_template']);
    }
    if (!isset($jieqiTset['jieqi_contents_cacheid'])) {
        $jieqiTset['jieqi_contents_cacheid'] = NULL;
    }
    if (!isset($jieqiTset['jieqi_contents_compileid'])) {
        $jieqiTset['jieqi_contents_compileid'] = NULL;
    }
    $jieqiTpl->include_compiled_inc($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_compileid'], true);
}
if (!empty($jieqiTset['jieqi_blocks_config'])) {
    if (!empty($jieqiTset['jieqi_blocks_module'])) {
        jieqi_getconfigs($jieqiTset['jieqi_blocks_module'], $jieqiTset['jieqi_blocks_config'], 'jieqiBlocks');
    } else {
        jieqi_getconfigs(JIEQI_MODULE_NAME, $jieqiTset['jieqi_blocks_config'], 'jieqiBlocks');
    }
}
$jieqi_pageblocks = array();
$jieqi_sideblocks = array();
$jieqi_showblock = 0;
if (isset($jieqiBlocks) && is_array($jieqiBlocks)) {
    reset($jieqiBlocks);
    foreach ($jieqiBlocks as $k => $v) {
        $blockvalue = jieqi_get_block($jieqiBlocks[$k]);
        if (!empty($blockvalue)) {
            $jieqi_pageblocks[$k] = $blockvalue;
            $sideindex = intval($jieqiBlocks[$k]['side']);
            if (0 <= $sideindex) {
                $jieqi_sideblocks[$sideindex][] =& $jieqi_pageblocks[$k];
            }
            $jieqi_showblock++;
        }
    }
}
$jieqiTpl->assign_by_ref('jieqi_pageblocks', $jieqi_pageblocks);
$jieqiTpl->assign_by_ref('jieqi_sideblocks', $jieqi_sideblocks);
$jieqiTpl->assign('jieqi_showblock', $jieqi_showblock);
if (!empty($jieqiTset['jieqi_contents_template'])) {
    if (!isset($jieqiTset['jieqi_contents_cacheid'])) {
        $jieqiTset['jieqi_contents_cacheid'] = NULL;
    }
    if (!isset($jieqiTset['jieqi_contents_compileid'])) {
        $jieqiTset['jieqi_contents_compileid'] = NULL;
    }
    $jieqiTpl->assign('jieqi_contents', $jieqiTpl->fetch($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_cacheid'], $jieqiTset['jieqi_contents_compileid']));
    $jieqiTpl->include_compiled_inc($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_compileid'], true);
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
$tmpvar = explode(' ', microtime());
$jieqiTpl->assign('jieqi_exetime', round($tmpvar[1] + $tmpvar[0] - JIEQI_START_TIME, 6));
$jieqiTpl->setCaching(0);
if (empty($jieqiTset['jieqi_page_template'])) {
    $jieqiTpl->display(JIEQI_THEME_ROOTPATH . '/themes/' . JIEQI_THEME_NAME . '/theme.html');
} else {
    if ($jieqiTset['jieqi_page_template'][0] != '/' && $jieqiTset['jieqi_page_template'][1] != ':') {
        if (strpos($jieqiTset['jieqi_page_template'], '/') === false) {
            if (defined('JIEQI_THEME_ROOTNEW') && is_file(JIEQI_THEME_ROOTPATH . '/themes/' . JIEQI_THEME_NAME . '/' . $jieqiTset['jieqi_page_template'])) {
                $jieqiTpl->display(JIEQI_THEME_ROOTPATH . '/themes/' . JIEQI_THEME_NAME . '/' . $jieqiTset['jieqi_page_template']);
            } else {
                $jieqiTpl->display(JIEQI_ROOT_PATH . '/themes/' . JIEQI_THEME_NAME . '/' . $jieqiTset['jieqi_page_template']);
            }
        } else {
            if (defined('JIEQI_THEME_ROOTNEW') && is_file(JIEQI_THEME_ROOTPATH . '/' . $jieqiTset['jieqi_page_template'])) {
                $jieqiTpl->display(JIEQI_THEME_ROOTPATH . '/' . $jieqiTset['jieqi_page_template']);
            } else {
                $jieqiTpl->display(JIEQI_ROOT_PATH . '/' . $jieqiTset['jieqi_page_template']);
            }
        }
    } else {
        $jieqiTpl->display($jieqiTset['jieqi_page_template']);
    }
}
if (!empty($_GET[$jieqi_channel_vname]) && is_numeric($_GET[$jieqi_channel_vname]) && defined('JIEQI_PROMOTION_VISIT') && 0 < JIEQI_PROMOTION_VISIT) {
    jieqi_includedb();
    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    if (0 < JIEQI_PROMOTION_VISIT) {
        $query->execute('REPLACE INTO ' . jieqi_dbprefix('system_promotions') . ' (ip, uid, username) VALUES (\'' . jieqi_userip() . '\', \'' . intval($_GET[$jieqi_channel_vname]) . '\', \'\')');
    }
}
if (defined('JIEQI_PROMOTION_VISIT') && 0 < JIEQI_PROMOTION_VISIT && substr(date('is', JIEQI_NOW_TIME), -3) == '000') {
    jieqi_includedb();
    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    $uidarray = array();
    $query->execute('SELECT * FROM ' . jieqi_dbprefix('system_promotions'));
    while ($promotion = $query->getRow()) {
        if (is_numeric($promotion['uid'])) {
            $uidarray[] = intval($promotion['uid']);
        }
    }
    if ($uidarray) {
        $countarray = array();
        $countvalues = array_count_values($uidarray);
        foreach ($countvalues as $uid => $count) {
            $countarray[$count][] = $uid;
        }
        foreach ($countarray as $count => $uids) {
            $query->execute('UPDATE ' . jieqi_dbprefix('system_users') . ' SET credit=credit+' . intval($count * JIEQI_PROMOTION_VISIT) . ' WHERE uid IN (' . implode(',', $uids) . ')');
        }
        $query->execute('DELETE FROM ' . jieqi_dbprefix('system_promotions'));
    }
}
if (function_exists('jieqi_hooks_end')) {
    jieqi_hooks_end();
}
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