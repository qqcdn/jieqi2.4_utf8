<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['delallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
if (!empty($_POST['act'])) {
    jieqi_checkpost();
}
if (empty($_REQUEST['checkid'])) {
    jieqi_printfail($jieqiLang['article']['need_batch_ids']);
}
if (is_array($_REQUEST['checkid'])) {
    foreach ($_REQUEST['checkid'] as $k => $v) {
        $_REQUEST['checkid'][$k] = intval($v);
        if ($_REQUEST['checkid'][$k] <= 0) {
            unset($_REQUEST['checkid'][$k]);
        }
    }
    if (count($_REQUEST['checkid']) == 0) {
        jieqi_printfail($jieqiLang['article']['need_batch_ids']);
    }
} else {
    $_REQUEST['checkid'] = intval($_REQUEST['checkid']);
    if ($_REQUEST['checkid'] <= 0) {
        jieqi_printfail($jieqiLang['article']['need_batch_ids']);
    } else {
        $_REQUEST['checkid'] = array($_REQUEST['checkid']);
    }
}
@set_time_limit(3600);
@session_write_close();
echo str_repeat(' ', 4096);
ob_flush();
flush();
if (empty($_REQUEST['url_jump'])) {
    $_REQUEST['url_jump'] = $jieqiModules['article']['url'] . '/admin/article.php';
}
include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
switch ($_POST['act']) {
    case 'show':
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET display = 0 WHERE articleid IN (' . implode(',', $_REQUEST['checkid']) . ')';
        $query->execute($sql);
        jieqi_article_updateopf($_REQUEST['checkid'], array('display' => 0));
        jieqi_article_updateinfo(0);
        echo $jieqiLang['article']['batch_show_success'];
        ob_flush();
        flush();
        jieqi_jumppage($_REQUEST['url_jump'], LANG_DO_SUCCESS, $jieqiLang['article']['batch_show_complete']);
        break;
    case 'hide':
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET display = 2 WHERE articleid IN (' . implode(',', $_REQUEST['checkid']) . ')';
        $query->execute($sql);
        jieqi_article_updateopf($_REQUEST['checkid'], array('display' => 2));
        jieqi_article_updateinfo(0);
        echo $jieqiLang['article']['batch_hide_success'];
        ob_flush();
        flush();
        jieqi_jumppage($_REQUEST['url_jump'], LANG_DO_SUCCESS, $jieqiLang['article']['batch_hide_complete']);
        break;
    case 'ready':
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET display = 1 WHERE articleid IN (' . implode(',', $_REQUEST['checkid']) . ')';
        $query->execute($sql);
        jieqi_article_updateopf($_REQUEST['checkid'], array('display' => 1));
        jieqi_article_updateinfo(0);
        echo $jieqiLang['article']['batch_ready_success'];
        ob_flush();
        flush();
        jieqi_jumppage($_REQUEST['url_jump'], LANG_DO_SUCCESS, $jieqiLang['article']['batch_ready_complete']);
        break;
    case 'share':
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET isshare = 1 WHERE display = 0 AND articleid IN (' . implode(',', $_REQUEST['checkid']) . ')';
        $query->execute($sql);
        echo $jieqiLang['article']['batch_share_success'];
        ob_flush();
        flush();
        jieqi_jumppage($_REQUEST['url_jump'], LANG_DO_SUCCESS, $jieqiLang['article']['batch_share_complete']);
        break;
    case 'oneself':
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET isshare = 0 WHERE display = 0 AND articleid IN (' . implode(',', $_REQUEST['checkid']) . ')';
        $query->execute($sql);
        echo $jieqiLang['article']['batch_oneself_success'];
        ob_flush();
        flush();
        jieqi_jumppage($_REQUEST['url_jump'], LANG_DO_SUCCESS, $jieqiLang['article']['batch_oneself_complete']);
        break;
    case 'del':
        foreach ($_REQUEST['checkid'] as $deleteid) {
            $ret = jieqi_article_delete($deleteid, true);
            if (is_object($ret)) {
                echo sprintf($jieqiLang['article']['start_delete_article'], $ret->getVar('articlename'));
                ob_flush();
                flush();
            }
        }
        jieqi_article_updateinfo(0);
        echo $jieqiLang['article']['batch_delete_success'];
        ob_flush();
        flush();
        jieqi_jumppage($_REQUEST['url_jump'], LANG_DO_SUCCESS, $jieqiLang['article']['batch_delete_complete']);
        break;
    default:
        jieqi_printfail(LANG_ERROR_PARAMETER);
        break;
}