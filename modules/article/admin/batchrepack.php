<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
@set_time_limit(0);
@session_write_close();
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$sortids = array();
if (is_array($_REQUEST['sortid']) && 0 < count($_REQUEST['sortid'])) {
    foreach ($_REQUEST['sortid'] as $v) {
        if (is_numeric($v)) {
            $sortids[] = intval($v);
        }
    }
}
if (isset($_REQUEST[JIEQI_TOKEN_NAME]) && !isset($_POST[JIEQI_TOKEN_NAME])) {
    $_POST[JIEQI_TOKEN_NAME] = $_REQUEST[JIEQI_TOKEN_NAME];
}
if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'packwithid') {
    jieqi_checkpost();
    if (!empty($_REQUEST['flagary'])) {
        $_REQUEST['flagary'] = jieqi_unserialize(urldecode($_REQUEST['flagary']));
    } else {
        $_REQUEST['flagary'] = $_REQUEST['packflag'];
    }
    if (!is_array($_REQUEST['flagary']) || count($_REQUEST['flagary']) < 1) {
        jieqi_printfail($jieqiLang['article']['need_repack_option']);
    }
    if (empty($_REQUEST['fromid']) || !is_numeric($_REQUEST['fromid'])) {
        jieqi_printfail($jieqiLang['article']['repack_start_id']);
    }
    if (empty($_REQUEST['toid'])) {
        $_REQUEST['toid'] = 0;
    }
    if ($_REQUEST['toid'] < $_REQUEST['fromid']) {
        jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['batch_repack_success'], $article_static_url . '/admin/batchrepack.php'));
        exit;
    }
    include_once $jieqiModules['article']['path'] . '/class/article.php';
    $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
    $article = $article_handler->get($_REQUEST['fromid']);
    if (is_object($article) && $article->getVar('display', 'n') != 2 && (empty($sortids) || in_array($article->getVar('sortid', 'n'), $sortids))) {
        $articlename = $article->getVar('articlename');
        include_once $jieqiModules['article']['path'] . '/include/repack.php';
        $ptypes = array();
        foreach ($_REQUEST['flagary'] as $v) {
            $ptypes[$v] = 1;
        }
        echo str_repeat(' ', 4096);
        echo sprintf($jieqiLang['article']['repack_fromto_id'], $articlename, $_REQUEST['fromid'], $_REQUEST['toid']);
        ob_flush();
        flush();
        article_repack($_REQUEST['fromid'], $ptypes, 1);
        $showinfo = $jieqiLang['article']['repack_success_next'];
    } else {
        $showinfo = $jieqiLang['article']['repack_noid_next'];
    }
    ${$_REQUEST}['fromid']++;
    $url = $article_static_url . '/admin/batchrepack.php?act=packwithid&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&fromid=' . $_REQUEST['fromid'] . '&toid=' . $_REQUEST['toid'];
    foreach ($_REQUEST['packflag'] as $k => $v) {
        $url .= '&packflag[' . $k . ']=' . $v;
    }
    foreach ($sortids as $k => $v) {
        $url .= '&sortid[' . $k . ']=' . $v;
    }
    echo sprintf($jieqiLang['article']['repack_next_html'], JIEQI_CHAR_SET, $showinfo, $url, $url);
} else {
    if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'packwithtime') {
        jieqi_checkpost();
        if (!empty($_REQUEST['flagary'])) {
            $_REQUEST['flagary'] = jieqi_unserialize(urldecode($_REQUEST['flagary']));
        } else {
            $_REQUEST['flagary'] = $_REQUEST['packflag'];
        }
        if (!is_array($_REQUEST['flagary']) || count($_REQUEST['flagary']) < 1) {
            jieqi_printfail($jieqiLang['article']['need_repack_option']);
        }
        $starttime = trim($_REQUEST['starttime']);
        $stoptime = trim($_REQUEST['stoptime']);
        $startlimit = trim($_REQUEST['startlimit']);
        if (empty($starttime)) {
            jieqi_printfail($jieqiLang['article']['need_time_format']);
        }
        if (!is_numeric($starttime)) {
            $starttime = mktime((int) substr($starttime, 11, 2), (int) substr($starttime, 14, 2), (int) substr($starttime, 17, 2), (int) substr($starttime, 5, 2), (int) substr($starttime, 8, 2), (int) substr($starttime, 0, 5));
        }
        if (empty($stoptime)) {
            $stoptime = JIEQI_NOW_TIME;
        }
        if (!is_numeric($stoptime)) {
            $stoptime = mktime((int) substr($stoptime, 11, 2), (int) substr($stoptime, 14, 2), (int) substr($stoptime, 17, 2), (int) substr($stoptime, 5, 2), (int) substr($stoptime, 8, 2), (int) substr($stoptime, 0, 5));
        }
        include_once $jieqiModules['article']['path'] . '/class/article.php';
        $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
        if (empty($startlimit)) {
            $startlimit = 0;
        }
        $criteria = new CriteriaCompo(new Criteria('lastupdate', $starttime, '>='));
        $criteria->add(new Criteria('lastupdate', $stoptime, '<='));
        $criteria->add(new Criteria('display', 2, '!='));
        if (0 < count($sortids)) {
            $criteria->add(new Criteria('sortid', '(' . implode(',', $sortids) . ')', 'IN'));
        }
        $criteria->setSort('lastupdate');
        $criteria->setOrder('ASC');
        $criteria->setStart($startlimit);
        $criteria->setLimit(1);
        $article_handler->queryObjects($criteria);
        $article = $article_handler->getObject();
        if (is_object($article)) {
            $articlename = $article->getVar('articlename');
            include_once $jieqiModules['article']['path'] . '/include/repack.php';
            $ptypes = array();
            foreach ($_REQUEST['flagary'] as $v) {
                $ptypes[$v] = 1;
            }
            echo str_repeat(' ', 4096);
            echo sprintf($jieqiLang['article']['batch_repack_doing'], $articlename, date('Y-m-d H:i:s', $starttime), date('Y-m-d H:i:s', $stoptime), date('Y-m-d H:i:s', $article->getVar('lastupdate')), $article->getVar('articleid'));
            ob_flush();
            flush();
            article_repack($article->getVar('articleid'), $ptypes, 1);
            $showinfo = $jieqiLang['article']['repack_success_next'];
        } else {
            jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['batch_repack_success'], $article_static_url . '/admin/batchrepack.php'));
            exit;
        }
        $startlimit++;
        $url = $article_static_url . '/admin/batchrepack.php?act=packwithtime&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&starttime=' . $starttime . '&stoptime=' . $stoptime . '&startlimit=' . $startlimit;
        foreach ($_REQUEST['packflag'] as $k => $v) {
            $url .= '&packflag[' . $k . ']=' . $v;
        }
        foreach ($sortids as $k => $v) {
            $url .= '&sortid[' . $k . ']=' . $v;
        }
        echo sprintf($jieqiLang['article']['repack_next_html'], JIEQI_CHAR_SET, $showinfo, $url, $url);
    } else {
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'SELECT MIN(articleid) AS minaid, MAX(articleid) AS maxaid FROM ' . jieqi_dbprefix('article_article') . ' WHERE 1';
        $query->execute($sql);
        $row = $query->getRow();
        $jieqiTpl->assign('minaid', $row['minaid']);
        $jieqiTpl->assign('maxaid', $row['maxaid']);
        $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));
        $jieqiTpl->assign('url_batchrepack', $article_static_url . '/admin/batchrepack.php?do=submit');
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/batchrepack.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
    }
}