<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
jieqi_loadlang('bookcase', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/header.php';
jieqi_getconfigs('article', 'configs');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$jieqiTpl->assign('article_static_url', $article_static_url);
$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
if (!empty($_POST['act']) && $_POST['act'] == 'delete' && !empty($_REQUEST['bid'])) {
    jieqi_checkpost();
    $_REQUEST['bid'] = intval($_REQUEST['bid']);
    include_once $jieqiModules['article']['path'] . '/class/bookcase.php';
    $bookcase_handler = JieqiBookcaseHandler::getInstance('JieqiBookcaseHandler');
    $bookcase = $bookcase_handler->get($_REQUEST['bid']);
    if (is_object($bookcase)) {
        if ($bookcase->getVar('userid') == $_SESSION['jieqiUserId']) {
            include_once $jieqiModules['article']['path'] . '/class/article.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $article_handler->execute('UPDATE ' . jieqi_dbprefix('article_article') . ' SET goodnum=goodnum-1 WHERE articleid=' . $bookcase->getVar('articleid', 'n') . ' AND goodnum>0');
            $bookcase_handler->delete($_REQUEST['bid']);
        }
    }
    jieqi_jumppage($jieqiModules['article']['url'] . '/bookcase.php?classid=' . urlencode($_REQUEST['classid']), '', '', true);
    exit;
}
if (!empty($_POST['act']) && $_POST['act'] == 'move' && !empty($_REQUEST['checkid']) && isset($_REQUEST['newclassid'])) {
    jieqi_checkpost();
    $_REQUEST['newclassid'] = intval($_REQUEST['newclassid']);
    $checkids = '';
    foreach ($_REQUEST['checkid'] as $v) {
        if (is_numeric($v)) {
            $v = intval($v);
            if (!empty($checkids)) {
                $checkids .= ', ';
            }
            $checkids .= $v;
        }
    }
    jieqi_includedb();
    $bookcase_query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    if (0 <= $_REQUEST['newclassid']) {
        $sql = 'UPDATE ' . jieqi_dbprefix('article_bookcase') . ' SET classid=' . $_REQUEST['newclassid'] . ' WHERE userid=' . $_SESSION['jieqiUserId'] . ' AND caseid IN (' . $checkids . ')';
        $bookcase_query->execute($sql);
    } else {
        $sql = 'SELECT caseid, articleid FROM ' . jieqi_dbprefix('article_bookcase') . ' WHERE userid=' . $_SESSION['jieqiUserId'] . ' AND caseid IN (' . $checkids . ')';
        $bookcase_query->execute($sql);
        $caseids = '';
        $articleids = '';
        while ($crow = $bookcase_query->getRow()) {
            if (!empty($caseids)) {
                $caseids .= ', ';
            }
            $caseids .= intval($crow['caseid']);
            if (!empty($articleids)) {
                $articleids .= ', ';
            }
            $articleids .= intval($crow['articleid']);
        }
        if (0 < strlen($caseids)) {
            $sql = 'DELETE FROM ' . jieqi_dbprefix('article_bookcase') . ' WHERE caseid IN (' . $caseids . ')';
            $bookcase_query->execute($sql);
            $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET goodnum=goodnum-1 WHERE articleid IN (' . $articleids . ') AND goodnum>0';
            $bookcase_query->execute($sql);
        }
    }
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'markclass', 'jieqiMarkclass');
if (empty($_REQUEST['classid'])) {
    $_REQUEST['classid'] = 0;
} else {
    $_REQUEST['classid'] = intval($_REQUEST['classid']);
}
if (isset($jieqiMarkclass['article']) && is_array($jieqiMarkclass['article'])) {
    $maxmarkclass = count($jieqiMarkclass['article']);
} else {
    if (isset($jieqiConfigs['article']['maxmarkclass'])) {
        $maxmarkclass = intval($jieqiConfigs['article']['maxmarkclass']);
    } else {
        $maxmarkclass = 0;
    }
}
$jieqiTpl->assign('maxmarkclass', $maxmarkclass);
$markclassrows = array();
$k = 0;
for ($i = 1; $i <= $maxmarkclass; $i++) {
    if (isset($jieqiMarkclass['article'][$i])) {
        $markclassrows[$k] = $jieqiMarkclass['article'][$i];
    }
    if (!isset($markclassrows[$k]['classid'])) {
        $markclassrows[$k]['classid'] = $i;
    }
    if (!isset($markclassrows[$k]['caption'])) {
        $markclassrows[$k]['caption'] = sprintf($jieqiLang['article']['bookcase_caption_group'], $i);
    }
    $k++;
}
$jieqiTpl->assign_by_ref('markclassrows', $markclassrows);
jieqi_getconfigs('system', 'honors');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
$maxnum = $jieqiConfigs['article']['maxbookmarks'];
$honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
if ($honorid && isset($jieqiRight['article']['maxbookmarks']['honors'][$honorid]) && is_numeric($jieqiRight['article']['maxbookmarks']['honors'][$honorid])) {
    $maxnum = intval($jieqiRight['article']['maxbookmarks']['honors'][$honorid]);
}
jieqi_includedb();
$bookcase_query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('userid', $_SESSION['jieqiUserId']));
$criteria->setTables(jieqi_dbprefix('article_bookcase'));
$jieqiTpl->assign('nowbookcase', $bookcase_query->getCount($criteria));
unset($criteria);
$criteria = new CriteriaCompo(new Criteria('c.userid', $_SESSION['jieqiUserId']));
if (is_numeric($_REQUEST['classid']) && 0 <= $_REQUEST['classid']) {
    $criteria->add(new Criteria('c.classid', $_REQUEST['classid']));
}
$criteria->setTables(jieqi_dbprefix('article_bookcase') . ' c LEFT JOIN ' . jieqi_dbprefix('article_article') . ' a ON c.articleid=a.articleid');
$criteria->setFields('c.*, a.articleid, a.lastupdate, a.articlename, a.articlecode, a.authorid, a.author, a.sortid, a.typeid, a.fullflag, a.imgflag, a.lastchapterid, a.lastchapter, a.lastvolume, a.freetime, a.vipchapterid, a.vipchapter, a.vipvolume, a.viptime, a.issign, a.isvip, a.vipid');
$criteria->setSort('a.lastupdate');
$criteria->setOrder('DESC');
$bookcase_query->queryObjects($criteria);
unset($criteria);
$bookcaserows = array();
$k = 0;
while ($v = $bookcase_query->getObject()) {
    $bookcaserows[$k] = jieqi_query_rowvars($v, 's', 'article');
    $bookcaserows[$k]['sort'] = $jieqiSort['article'][$bookcaserows[$k]['sortid']]['caption'];
    $bookcaserows[$k]['sortname'] = $bookcaserows[$k]['sort'];
    $bookcaserows[$k]['type'] = $jieqiSort['article'][$bookcaserows[$k]['sortid']]['types'][$bookcaserows[$k]['typeid']];
    $tmpvar = $v->getVar('articlename');
    if (!empty($tmpvar)) {
        $bookcaserows[$k]['url_articleinfo'] = $article_dynamic_url . '/readbookcase.php?bid=' . $v->getVar('caseid') . '&aid=' . $v->getVar('articleid') . '&acode=' . urlencode($v->getVar('articlecode', 'n'));
        $bookcaserows[$k]['url_index'] = $bookcaserows[$k]['url_articleinfo'] . '&indexflag=1';
        $bookcaserows[$k]['articlename'] = $v->getVar('articlename');
    } else {
        $bookcaserows[$k]['url_articleinfo'] = '#';
        $bookcaserows[$k]['url_index'] = '#';
        $bookcaserows[$k]['articlename'] = $jieqiLang['article']['articlemark_has_deleted'];
    }
    if ($v->getVar('lastchapter') == '') {
        $bookcaserows[$k]['lastchapter'] = '';
        $bookcaserows[$k]['url_lastchapter'] = '#';
    } else {
        $bookcaserows[$k]['lastchapter'] = $v->getVar('lastchapter');
        $bookcaserows[$k]['url_lastchapter'] = $article_dynamic_url . '/readbookcase.php?bid=' . $v->getVar('caseid') . '&aid=' . $v->getVar('articleid') . '&cid=' . $v->getVar('lastchapterid') . '&acode=' . urlencode($v->getVar('articlecode', 'n'));
    }
    if ($v->getVar('lastvisit') < $v->getVar('lastupdate')) {
        $bookcaserows[$k]['hasnew'] = 1;
    } else {
        $bookcaserows[$k]['hasnew'] = 0;
    }
    if ($v->getVar('chaptername') == '') {
        $bookcaserows[$k]['articlemark'] = '';
        $bookcaserows[$k]['url_articlemark'] = '#';
    } else {
        $bookcaserows[$k]['articlemark'] = $v->getVar('chaptername');
        $bookcaserows[$k]['url_articlemark'] = $article_dynamic_url . '/readbookcase.php?aid=' . $v->getVar('articleid') . '&bid=' . $v->getVar('caseid') . '&cid=' . $v->getVar('chapterid') . '&acode=' . urlencode($v->getVar('articlecode', 'n'));
    }
    $bookcaserows[$k]['url_delete'] = $article_dynamic_url . '/bookcase.php?bid=' . $v->getVar('caseid') . '&act=delete';
    $k++;
}
$jieqiTpl->assign_by_ref('bookcaserows', $bookcaserows);
$jieqiTpl->assign('maxbookcase', $maxnum);
$jieqiTpl->assign('classbookcase', count($bookcaserows));
$jieqiTpl->assign('classid', $_REQUEST['classid']);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/bookcase.html';
include_once JIEQI_ROOT_PATH . '/footer.php';