<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['key'])) {
    exit('no key');
} else {
    if (defined('JIEQI_SITE_KEY') && $_REQUEST['key'] != JIEQI_SITE_KEY) {
        exit('error key');
    } else {
        if ($_REQUEST['key'] != md5(JIEQI_DB_USER . JIEQI_DB_PASS . JIEQI_DB_NAME)) {
            exit;
        }
    }
}
if (!is_numeric($_REQUEST['id'])) {
    exit;
}
if (!is_array($_REQUEST['packflag']) || count($_REQUEST['packflag']) < 1) {
    exit;
}
$_REQUEST['id'] = intval($_REQUEST['id']);
@ignore_user_abort(true);
@set_time_limit(3600);
@session_write_close();
echo str_repeat(' ', 4096);
ob_flush();
flush();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/article.php';
include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/chapter.php';
include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/package.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!is_object($article)) {
    exit;
} else {
    $package = new JieqiPackage($_REQUEST['id']);
    $package->initPackage($article->getVars('n'), false);
    $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
    $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['id'], '='));
    $criteria->setSort('chapterorder ASC, chapterid');
    $criteria->setOrder('ASC');
    $res = $chapter_handler->queryObjects($criteria);
    $i = 0;
    $articlewords = 0;
    while ($chapter = $chapter_handler->getObject($res)) {
        $package->chapters[$i] = $chapter->getVars('n');
        $i++;
        if ($chapter->getVar('chaptertype', 'n') == 0) {
            $articlewords = $articlewords + intval($chapter->getVar('words', 'n'));
        }
        if ($chapter->getVar('chapterorder', 'n') != $i) {
            $chapter->setVar('chapterorder', $i);
            $chapter_handler->insert($chapter);
        }
    }
    $changeflag = false;
    if ($article->getVar('chapters', 'n') != $i) {
        $article->setVar('chapters', $i);
        $changeflag = true;
    }
    if (intval($article->getVar('words', 'n')) < $articlewords) {
        $article->setVar('words', $articlewords);
        $changeflag = true;
    }
    if ($articlewords != intval($article->getVar('freewords', 'n'))) {
        $article->setVar('freewords', $articlewords);
        $changeflag = true;
    }
    if ($changeflag) {
        $article_handler->insert($article);
    }
    $package->isload = true;
    if (in_array('makeopf', $_REQUEST['packflag'])) {
        $package->createOPF();
    }
    if (in_array('maketxtjs', $_REQUEST['packflag'])) {
        $chaptercount = count($package->chapters);
        for ($i = 1; $i <= $chaptercount; $i++) {
            if ($package->chapters[$i - 1]['chaptertype'] == 0) {
                $package->makeTxtjs($i);
            }
        }
    }
    if (in_array('maketxtfull', $_REQUEST['packflag'])) {
        $package->maketxtfull();
    }
    if (in_array('makefull', $_REQUEST['packflag'])) {
        $package->makefulltext();
    }
    if (in_array('makezip', $_REQUEST['packflag'])) {
        $package->makezip();
    }
    if (in_array('makeumd', $_REQUEST['packflag'])) {
        $package->makeumd();
    }
    if (in_array('makejar', $_REQUEST['packflag'])) {
        $package->makejar();
    }
    if (in_array('makehtml', $_REQUEST['packflag'])) {
        $package->makeRead('edit', 1);
    } else {
        if (in_array('makechapter', $_REQUEST['packflag'])) {
            $package->makeRead('edit', 1);
        } else {
            if (in_array('makeindex', $_REQUEST['packflag'])) {
                $package->makeRead('edit', 0);
            }
        }
    }
    return true;
}