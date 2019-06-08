<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (2.2 <= floatval(JIEQI_VERSION)) {
    $_REQUEST['bid'] = isset($_POST['bid']) ? $_POST['bid'] : 0;
    $_REQUEST['cid'] = isset($_POST['cid']) ? $_POST['cid'] : 0;
}
if (empty($_REQUEST['bid']) && empty($_REQUEST['cid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (isset($_REQUEST['bid'])) {
    $_REQUEST['bid'] = intval($_REQUEST['bid']);
}
if (isset($_REQUEST['cid'])) {
    $_REQUEST['cid'] = intval($_REQUEST['cid']);
}
if (isset($_REQUEST['ocid'])) {
    $_REQUEST['ocid'] = intval($_REQUEST['ocid']);
}
jieqi_checklogin();
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
if (!$jieqiUsers) {
    jieqi_printfail(LANG_NO_USER);
}
jieqi_loadlang('bookcase', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('system', 'honors');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
$maxnum = $jieqiConfigs['article']['maxbookmarks'];
$honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
if ($honorid && isset($jieqiRight['article']['maxbookmarks']['honors'][$honorid]) && is_numeric($jieqiRight['article']['maxbookmarks']['honors'][$honorid])) {
    $maxnum = intval($jieqiRight['article']['maxbookmarks']['honors'][$honorid]);
}
include_once $jieqiModules['article']['path'] . '/class/bookcase.php';
$bookcase_handler = JieqiBookcaseHandler::getInstance('JieqiBookcaseHandler');
$criteria = new CriteriaCompo(new Criteria('userid', $jieqiUsers->getVar('uid')));
$cot = $bookcase_handler->getCount($criteria);
unset($criteria);
if (!empty($_REQUEST['cid'])) {
    $article_query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    $criteria = new CriteriaCompo(new Criteria('c.chapterid', $_REQUEST['cid']));
    $criteria->setTables(jieqi_dbprefix('article_chapter') . ' c LEFT JOIN ' . jieqi_dbprefix('article_article') . ' a ON c.articleid=a.articleid');
    $article_query->queryObjects($criteria);
    $chapter = $article_query->getObject();
    unset($criteria);
    if (!$chapter) {
        jieqi_printfail($jieqiLang['article']['chapter_not_exists']);
    }
    $criteria = new CriteriaCompo(new Criteria('userid', $jieqiUsers->getVar('uid')));
    $criteria->add(new Criteria('articleid', $chapter->getVar('articleid')));
    $bookcase_handler->queryObjects($criteria);
    $bookcase = $bookcase_handler->getObject();
    if (!$bookcase) {
        if ($maxnum <= $cot) {
            jieqi_printfail(sprintf($jieqiLang['article']['bookcase_is_full'], $maxnum));
        }
        $article_query->execute('UPDATE ' . jieqi_dbprefix('article_article') . ' SET goodnum=goodnum+1 WHERE articleid=' . $chapter->getVar('articleid', 'n'));
        $bookcase = $bookcase_handler->create();
        $bookcase->setVar('joindate', JIEQI_NOW_TIME);
        $bookcase->setVar('lastvisit', JIEQI_NOW_TIME);
        $bookcase->setVar('flag', 0);
    }
    $bookcase->setVar('articleid', $chapter->getVar('articleid', 'n'));
    $bookcase->setVar('articlename', $chapter->getVar('articlename', 'n'));
    $bookcase->setVar('userid', $jieqiUsers->getVar('uid', 'n'));
    $bookcase->setVar('username', $jieqiUsers->getVar('uname', 'n'));
    $bookcase->setVar('chapterid', $chapter->getVar('chapterid', 'n'));
    $bookcase->setVar('chaptername', $chapter->getVar('chaptername', 'n'));
    $bookcase->setVar('chapterorder', $chapter->getVar('chapterorder', 'n'));
    if (!$bookcase_handler->insert($bookcase)) {
        jieqi_printfail($jieqiLang['article']['add_chaptermark_failure']);
    } else {
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['add_chaptermark_success']);
    }
} else {
    if (!empty($_REQUEST['ocid'])) {
        $article_query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $criteria = new CriteriaCompo(new Criteria('c.ochapterid', $_REQUEST['ocid']));
        $criteria->setTables(jieqi_dbprefix('obook_ochapter') . ' c LEFT JOIN ' . jieqi_dbprefix('article_article') . ' a ON c.articleid=a.articleid');
        $article_query->queryObjects($criteria);
        $chapter = $article_query->getObject();
        unset($criteria);
        if (!$chapter) {
            jieqi_printfail($jieqiLang['article']['chapter_not_exists']);
        }
        $criteria = new CriteriaCompo(new Criteria('userid', $jieqiUsers->getVar('uid')));
        $criteria->add(new Criteria('articleid', $chapter->getVar('articleid')));
        $bookcase_handler->queryObjects($criteria);
        $bookcase = $bookcase_handler->getObject();
        if (!$bookcase) {
            if ($maxnum <= $cot) {
                jieqi_printfail(sprintf($jieqiLang['article']['bookcase_is_full'], $maxnum));
            }
            $article_query->execute('UPDATE ' . jieqi_dbprefix('article_article') . ' SET goodnum=goodnum+1 WHERE articleid=' . $chapter->getVar('articleid', 'n'));
            $bookcase = $bookcase_handler->create();
            $bookcase->setVar('joindate', JIEQI_NOW_TIME);
            $bookcase->setVar('lastvisit', JIEQI_NOW_TIME);
        }
        $bookcase->setVar('flag', 1);
        $bookcase->setVar('articleid', $chapter->getVar('articleid', 'n'));
        $bookcase->setVar('articlename', $chapter->getVar('articlename', 'n'));
        $bookcase->setVar('userid', $jieqiUsers->getVar('uid', 'n'));
        $bookcase->setVar('username', $jieqiUsers->getVar('uname', 'n'));
        $bookcase->setVar('chapterid', $chapter->getVar('ochapterid', 'n'));
        $bookcase->setVar('chaptername', $chapter->getVar('chaptername', 'n'));
        $bookcase->setVar('chapterorder', $chapter->getVar('chapterorder', 'n'));
        if (!$bookcase_handler->insert($bookcase)) {
            jieqi_printfail($jieqiLang['article']['add_chaptermark_failure']);
        } else {
            jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['add_chaptermark_success']);
        }
    } else {
        if (!empty($_REQUEST['bid'])) {
            include_once $jieqiModules['article']['path'] . '/class/article.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $article = $article_handler->get($_REQUEST['bid']);
            if (!$article) {
                jieqi_printfail($jieqiLang['article']['article_not_exists']);
            }
            $criteria = new CriteriaCompo(new Criteria('userid', $jieqiUsers->getVar('uid')));
            $criteria->add(new Criteria('articleid', $article->getVar('articleid')));
            $bookcase_handler->queryObjects($criteria);
            $bookcase = $bookcase_handler->getObject();
            if ($bookcase) {
                jieqi_printfail($jieqiLang['article']['article_has_incase']);
            } else {
                if ($maxnum <= $cot) {
                    jieqi_printfail(sprintf($jieqiLang['article']['bookcase_is_full'], $maxnum));
                }
                $article_handler->execute('UPDATE ' . jieqi_dbprefix('article_article') . ' SET goodnum=goodnum+1 WHERE articleid=' . $_REQUEST['bid']);
                $bookcase = $bookcase_handler->create();
                $bookcase->setVar('joindate', JIEQI_NOW_TIME);
                $bookcase->setVar('lastvisit', JIEQI_NOW_TIME);
                $bookcase->setVar('flag', 0);
            }
            $bookcase->setVar('articleid', $article->getVar('articleid', 'n'));
            $bookcase->setVar('articlename', $article->getVar('articlename', 'n'));
            $bookcase->setVar('userid', $jieqiUsers->getVar('uid', 'n'));
            $bookcase->setVar('username', $jieqiUsers->getVar('uname', 'n'));
            $bookcase->setVar('chapterid', 0);
            $bookcase->setVar('chaptername', '');
            $bookcase->setVar('chapterorder', 0);
            if (!$bookcase_handler->insert($bookcase)) {
                jieqi_printfail($jieqiLang['article']['add_articlemark_failure']);
            } else {
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['add_articlemark_success']);
            }
        } else {
            jieqi_printfail($jieqiLang['article']['article_not_exists']);
        }
    }
}