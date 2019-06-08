<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('draft', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
include_once $jieqiModules['article']['path'] . '/class/draft.php';
$draft_handler = JieqiDraftHandler::getInstance('JieqiDraftHandler');
if (isset($_REQUEST['keyword']) && 0 < strlen($_REQUEST['keyword']) && $_REQUEST['searchdel'] == 1) {
    $criteria = new CriteriaCompo();
    switch ($_REQUEST['type']) {
        case 1:
            $criteria->add(new Criteria('display', 0));
            $criteria->add(new Criteria('ispub', 0));
            break;
        case 2:
            $criteria->add(new Criteria('ispub', 1));
            break;
        case 3:
            $criteria->add(new Criteria('display', 1));
            break;
    }
    if ($_REQUEST['keytype'] == 1) {
        $criteria->add(new Criteria('poster', $_REQUEST['keyword']));
    } else {
        $criteria->add(new Criteria('articlename', $_REQUEST['keyword']));
    }
    $draft_handler->delete($criteria);
    $_REQUEST['keyword'] = '';
    $_REQUEST['keytype'] = 0;
}
if (isset($_POST['act']) && !empty($_REQUEST['checkid'])) {
    jieqi_checkpost();
    if (!is_array($_REQUEST['checkid'])) {
        $_REQUEST['checkid'] = array(intval($_REQUEST['checkid']));
    }
    foreach ($_REQUEST['checkid'] as $k => $v) {
        $_REQUEST['checkid'][$k] = intval($_REQUEST['checkid'][$k]);
    }
    if (0 < count($_REQUEST['checkid'])) {
        @set_time_limit(0);
        @session_write_close();
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        switch ($_POST['act']) {
            case 'audit':
                include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
                include_once $jieqiModules['obook']['path'] . '/include/actobook.php';
                $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_draft') . ' WHERE draftid IN (' . implode(',', $_REQUEST['checkid']) . ') ORDER BY draftid ASC';
                $res = $query->execute($sql);
                $aflag = '';
                $uparticle = false;
                $upobook = false;
                while ($row = $query->getRow($res)) {
                    if (0 < $row['display']) {
                        if (0 < $row['ispub'] && JIEQI_NOW_TIME < $row['pubdate']) {
                            $sql = 'UPDATE ' . jieqi_dbprefix('article_draft') . ' SET display = 0 WHERE draftid = ' . intval($row['draftid']);
                            $query->execute($sql);
                        } else {
                            if ($aflag != $row['articleid']) {
                                $aflag = $row['articleid'];
                                $article = $article_handler->get($row['articleid']);
                            }
                            if (!is_object($article)) {
                                continue;
                            }
                            if (0 < $row['isvip']) {
                                $upobook = true;
                            } else {
                                $uparticle = true;
                            }
                            $postvars = $row;
                            $attachvars = array();
                            jieqi_article_addchapter($postvars, $attachvars, $article);
                        }
                    }
                }
                if ($uparticle || $upobook) {
                    if ($uparticle == true && $upobook == false) {
                        $upflag = -1;
                    } else {
                        if ($uparticle == false && $upobook == true) {
                            $upflag = -2;
                        } else {
                            $upflag = 0;
                        }
                    }
                    jieqi_article_updateinfo($upflag);
                }
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
                }
                break;
            case 'delete':
                include_once JIEQI_ROOT_PATH . '/include/funmessage.php';
                $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_draft') . ' WHERE draftid IN (' . implode(',', $_REQUEST['checkid']) . ')';
                $res = $query->execute($sql);
                $didarys = array();
                while ($row = $query->getRow()) {
                    $row['articleid'] = intval($row['articleid']);
                    $row['draftid'] = intval($row['draftid']);
                    $didarys[] = -$row['draftid'];
                    $attach_dir = jieqi_uploadpath($jieqiConfigs['article']['attachdir'], 'article') . jieqi_getsubdir($row['articleid']) . '/' . $row['articleid'] . '/0' . $row['draftid'];
                    if (is_dir($attach_dir)) {
                        jieqi_delfolder($attach_dir, true);
                    }
                    jieqi_sendmessage(array('toid' => $row['posterid'], 'toname' => $row['poster'], 'title' => sprintf($jieqiLang['article']['draft_delmsg_title'], $row['articlename'], $row['chaptername']), 'content' => sprintf($jieqiLang['article']['draft_delmsg_content'], $row['articlename'], $row['chaptername']), 'messagetype' => 22));
                }
                if (!empty($didarys)) {
                    $sql = 'DELETE FROM ' . jieqi_dbprefix('article_attachs') . ' WHERE chapterid IN (' . implode(',', $didarys) . ')';
                    $res = $query->execute($sql);
                }
                if (!empty($_REQUEST['checkid'])) {
                    $sql = 'DELETE FROM ' . jieqi_dbprefix('article_draft') . ' WHERE draftid IN (' . implode(',', $_REQUEST['checkid']) . ')';
                    $res = $query->execute($sql);
                }
                jieqi_jumppage($jieqiModules['article']['url'] . '/admin/draftlist.php?type=' . urlencode($_REQUEST['type']), '', '', true);
                break;
            default:
                break;
        }
    }
    unset($criteria);
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/draftlist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
$criteria = new CriteriaCompo();
switch ($_REQUEST['type']) {
    case 1:
        $criteria->add(new Criteria('display', 0));
        $criteria->add(new Criteria('ispub', 0));
        $criteria->setSort('draftid');
        $criteria->setOrder('DESC');
        break;
    case 2:
        $criteria->add(new Criteria('ispub', 1));
        $criteria->setSort('draftid');
        $criteria->setOrder('ASC');
        break;
    case 3:
        $criteria->add(new Criteria('display', 1));
        $criteria->setSort('draftid');
        $criteria->setOrder('ASC');
        break;
    default:
        $criteria->setSort('draftid');
        $criteria->setOrder('DESC');
        break;
}
if (isset($_REQUEST['keyword']) && 0 < strlen($_REQUEST['keyword'])) {
    if ($_REQUEST['keytype'] == 1) {
        $criteria->add(new Criteria('poster', $_REQUEST['keyword']));
    } else {
        $criteria->add(new Criteria('articlename', $_REQUEST['keyword']));
    }
}
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$draft_handler->queryObjects($criteria);
$draftrows = array();
$k = 0;
while ($v = $draft_handler->getObject()) {
    $draftrows[$k] = jieqi_query_rowvars($v);
    $k++;
}
$jieqiTpl->assign_by_ref('draftrows', $draftrows);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $draft_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->assign('authorarea', 1);
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';