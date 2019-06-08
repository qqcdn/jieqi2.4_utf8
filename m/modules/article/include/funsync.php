<?php

function jieqi_sync_articleinfo($sync_article)
{
    global $jieqiModules;
    global $jieqiConfigs;
    global $article_handler;
    global $jieqi_checker;
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (!isset($article_handler) || !is_a($article_handler, 'JieqiArticleHandler')) {
        include_once $jieqiModules['article']['path'] . '/class/article.php';
        $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
    }
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('siteid', $sync_article['siteid']));
    $criteria->add(new Criteria('sourceid', $sync_article['sourceid']));
    $criteria->setLimit(1);
    $article_handler->queryObjects($criteria);
    $article = $article_handler->getObject();
    if (!is_object($article)) {
        $options = array('action' => 'add', 'ismanager' => true, 'allowtrans' => true);
        $errors = jieqi_article_articlepcheck($sync_article, $options);
        $article = jieqi_article_articleadd($sync_article, $options);
    } else {
        $sync_article['articleid'] = intval($article->getVar('articleid', 'n'));
        if (isset($sync_article['display'])) {
            unset($sync_article['display']);
        }
        $options = array('action' => 'edit', 'ismanager' => true, 'allowtrans' => true, 'allowmodify' => true);
        $errors = jieqi_article_articlepcheck($sync_article, $options, $article);
        $article = jieqi_article_articleadd($sync_article, $options, $article);
    }
    if (is_object($article)) {
        return $article;
    } else {
        return $article;
    }
}
function jieqi_sync_getcover($cover)
{
    if (preg_match('/^https?:\\/\\/[^\\s\\r\\n\\t\\f<>]+(\\.gif|\\.jpg|\\.jpeg|\\.png|\\.bmp)/i', $cover, $matches)) {
        $imgtary = array('.gif' => 1, '.jpg' => 2, '.jpeg' => 3, '.png' => 4, '.bmp' => 5);
        if (!isset($imgtary[$matches[1]])) {
            return false;
        }
        $ret = array('imgflag' => $imgtary[$matches[1]], 'imgtype' => $matches[1]);
        $ret['imgdata'] = jieqi_sync_geturlcontent($cover);
        if (!$ret['imgdata']) {
            return false;
        } else {
            return $ret;
        }
    } else {
        return false;
    }
}
function jieqi_sync_geturlcontent($url)
{
    if (!preg_match('/^http/', $url)) {
        return false;
    } else {
        if (defined('PHP_VERSION') && '5.0.0' <= PHP_VERSION) {
            $context = array('http' => array('header' => 'User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)' . "\r\n" . ''));
            $stream_context = stream_context_create($context);
            $ret = @file_get_contents($url, false, $stream_context);
            if ($ret === false) {
                $ret = file_get_contents($url, false, $stream_context);
            }
        } else {
            $ret = @file_get_contents($url);
            if ($ret === false) {
                $ret = file_get_contents($url);
            }
        }
        return $ret;
    }
}
function jieqi_sync_chapterupdate($sync_chapter, $old_chapter)
{
    global $jieqiModules;
    global $jieqiConfigs;
    global $jieqiLang;
    global $query;
    global $article_handler;
    global $chapter_handler;
    global $obook_handler;
    global $ochapter_handler;
    global $jieqi_checker;
    $attachvars = array();
    $errors = jieqi_article_chapterpcheck($sync_chapter, $attachvars);
    $chapterwords = jieqi_strwords($sync_chapter['chaptercontent']);
    $sync_chapter['words'] = $chapterwords;
    $upfields = array();
    if ($sync_chapter['chapterorder'] != $old_chapter['chapterorder']) {
        $upfields['chapterorder'] = $sync_chapter['chapterorder'];
    }
    if ($sync_chapter['chaptername'] != $old_chapter['chaptername']) {
        $upfields['chaptername'] = $sync_chapter['chaptername'];
    }
    if ($sync_chapter['words'] != $old_chapter['words']) {
        $upfields['words'] = $sync_chapter['words'];
    }
    if (0 < $sync_chapter['lastupdate'] && $sync_chapter['lastupdate'] != $old_chapter['lastupdate']) {
        $upfields['lastupdate'] = $sync_chapter['lastupdate'];
    }
    if ($sync_chapter['isvip'] != $old_chapter['isvip']) {
        $upfields['isvip'] = $sync_chapter['isvip'];
    }
    if (0 < strlen($sync_chapter['chaptercontent'])) {
        $sync_chapter['summary'] = jieqi_substr($sync_chapter['chaptercontent'], 0, 500, '..');
    } else {
        $sync_chapter['summary'] = '';
    }
    if ($sync_chapter['summary'] != $old_chapter['summary']) {
        $upfields['summary'] = $sync_chapter['summary'];
    }
    if ($sync_chapter['display'] == 1 && $old_chapter['display'] == 0) {
        $upfields['display'] = $sync_chapter['display'];
    }
    if (empty($upfields)) {
        return true;
    }
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $sql = $query->makeupsql(jieqi_dbprefix('article_chapter'), $upfields, 'UPDATE', array('chapterid' => $old_chapter['chapterid']));
    $query->execute($sql);
    if ($old_chapter['chaptertype'] == 0 && 0 < $old_chapter['isvip']) {
        $fieldsmap = array('chaptername' => 'chaptername', 'words' => 'words', 'lastupdate' => 'lastupdate', 'summary' => 'summary', 'saleprice' => 'saleprice', 'display' => 'display');
        $ofields = array();
        foreach ($upfields as $k => $v) {
            if (isset($fieldsmap[$k])) {
                $ofields[$fieldsmap[$k]] = $v;
            }
        }
        if (!empty($ofields)) {
            $sql = $query->makeupsql(jieqi_dbprefix('obook_ochapter'), $ofields, 'UPDATE', array('chapterid' => $old_chapter['chapterid']));
            $query->execute($sql);
        }
    }
    if ($old_chapter['chaptertype'] == 0 && ($sync_chapter['words'] != $old_chapter['words'] || $sync_chapter['summary'] != $old_chapter['summary'])) {
        include_once $jieqiModules['article']['path'] . '/class/package.php';
        jieqi_save_achapterc($old_chapter['articleid'], $old_chapter['chapterid'], $sync_chapter['chaptercontent'], $old_chapter['isvip'], $old_chapter['chaptertype']);
    }
    if ($old_chapter['chaptertype'] == 0) {
        if ($sync_chapter['isvip'] != $old_chapter['isvip']) {
            if (!isset($chapter_handler) || !is_a($chapter_handler, 'JieqiChapterHandler')) {
                include_once $jieqiModules['article']['path'] . '/class/chapter.php';
                $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
            }
            $chapter = $chapter_handler->get($old_chapter['chapterid']);
            if (is_object($chapter)) {
                if (!isset($article_handler) || !is_a($article_handler, 'JieqiArticleHandler')) {
                    include_once $jieqiModules['article']['path'] . '/class/article.php';
                    $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
                }
                $article = $article_handler->get($chapter->getVar('articleid'));
                if (is_object($article)) {
                    if (0 < $sync_chapter['isvip'] && $old_chapter['isvip'] == 0) {
                        $ret = jieqi_article_chapterset($chapter, $article, 'vip');
                    } else {
                        if ($sync_chapter['isvip'] == 0 && 0 < $old_chapter['isvip']) {
                            $ret = jieqi_article_chapterset($chapter, $article, 'free');
                        }
                    }
                }
            }
        }
    }
    return true;
}
function jieqi_sync_chapternew($sync_chapter, $article)
{
    global $jieqiLang;
    $attachvars = array();
    $errors = jieqi_article_chapterpcheck($sync_chapter, $attachvars);
    $ret = jieqi_article_addchapter($sync_chapter, $attachvars, $article, true);
    return $ret;
}
function jieqi_sync_delchapters($cids, $article)
{
    global $query;
    global $jieqiConfigs;
    global $jieqiModules;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
    }
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $articleid = intval($article->getVar('articleid', 'n'));
    $cidary = array();
    foreach ($cids as $c) {
        $cidary[] = intval($c['chapterid']);
    }
    $sql = 'DELETE FROM ' . jieqi_dbprefix('article_chapter') . ' WHERE articleid = ' . $articleid . ' AND chapterid IN (' . implode(',', $cidary) . ')';
    $query->execute($sql);
    $sql = 'DELETE FROM ' . jieqi_dbprefix('article_attachs') . ' WHERE articleid = ' . $articleid . ' AND chapterid IN (' . implode(',', $cidary) . ')';
    $query->execute($sql);
    include_once $jieqiModules['article']['path'] . '/class/package.php';
    $htmldir = jieqi_uploadpath($jieqiConfigs['article']['htmldir'], 'article') . jieqi_getsubdir($articleid) . '/' . $articleid;
    $txtjsdir = jieqi_uploadpath($jieqiConfigs['article']['txtjsdir'], 'article') . jieqi_getsubdir($articleid) . '/' . $articleid;
    $attachdir = jieqi_uploadpath($jieqiConfigs['article']['attachdir'], 'article') . jieqi_getsubdir($articleid) . '/' . $articleid;
    foreach ($cids as $c) {
        jieqi_delete_achapterc($articleid, $c['chapterid'], intval($c['isvip']), intval($c['chaptertype']));
        if (is_file($htmldir . '/' . $c['chapterid'] . $jieqiConfigs['article']['htmlfile'])) {
            jieqi_delfile($htmldir . '/' . $c['chapterid'] . $jieqiConfigs['article']['htmlfile']);
        }
        if (is_file($txtjsdir . '/' . $c['chapterid'] . $jieqi_file_postfix['js'])) {
            jieqi_delfile($txtjsdir . '/' . $c['chapterid'] . $jieqi_file_postfix['js']);
        }
        if (is_dir($attachdir . '/' . $c['chapterid'])) {
            jieqi_delfolder($attachdir . '/' . $c['chapterid']);
        }
    }
    return true;
}
include_once $jieqiModules['article']['path'] . '/include/actarticle.php';